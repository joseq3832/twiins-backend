<?php

namespace App\Http\Controllers;

use App\Models\ImmediateFamily;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class EmployeeController extends Controller
{
    protected EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * @OA\Get(
     *     path="/v1/employees",
     *     tags={"employees"},
     *     summary="Listar empleados con filtrado avanzado",
     *     description="Obtiene una lista paginada de empleados con capacidades de filtrado, búsqueda y ordenamiento avanzadas",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página para paginación",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de elementos por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda en columnas configuradas (name, email, position)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="John")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Columnas para ordenar separadas por comas (usar - para descendente)",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="name,-hire_date")
     *     ),
     *
     *     @OA\Parameter(
     *         name="select",
     *         in="query",
     *         description="Columnas específicas a seleccionar separadas por comas",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="id,name,email")
     *     ),
     *
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Relaciones a incluir separadas por comas",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="immediateFamily")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[position][$eq]",
     *         in="query",
     *         description="Filtrar por posición exacta",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="Developer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[hire_date][$gte]",
     *         in="query",
     *         description="Filtrar por fecha de contratación mayor o igual",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2022-01-01")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empleados obtenida exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedEmployeeResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Parámetros de consulta inválidos",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(Request $request)
    {
        $employees = $this->employeeRepository->filter($request);

        return response()->json($employees);
    }

    /**
     * @OA\Post(
     *     path="/v1/employees",
     *     tags={"employees"},
     *     summary="Crear un nuevo empleado",
     *     description="Crea un nuevo empleado en el sistema",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del empleado a crear",
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "position", "hire_date"},
     *
     *             @OA\Property(property="name", type="string", example="Juan Pérez", description="Nombre completo del empleado"),
     *             @OA\Property(property="email", type="string", format="email", example="juan.perez@company.com", description="Correo electrónico"),
     *             @OA\Property(property="position", type="string", example="Desarrollador Senior", description="Cargo o posición"),
     *             @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15", description="Fecha de contratación"),
     *             @OA\Property(
     *                 property="immediate_family",
     *                 type="array",
     *                 description="Lista de familiares inmediatos (opcional)",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"family_name", "relationship", "date_of_birth"},
     *                     @OA\Property(property="family_name", type="string", example="María García", description="Nombre del familiar"),
     *                     @OA\Property(property="relationship", type="string", example="Hijo/a", description="Relación familiar"),
     *                     @OA\Property(property="date_of_birth", type="string", format="date", example="2025-09-16", description="Fecha de nacimiento")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Empleado creado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'immediate_family' => 'sometimes|array',
            'immediate_family.*.family_name' => 'required_with:immediate_family|string|max:255',
            'immediate_family.*.relationship' => 'required_with:immediate_family|string|max:255',
            'immediate_family.*.date_of_birth' => 'required_with:immediate_family|date',
        ]);

        DB::beginTransaction();
        try {
            // Create employee
            $employeeData = collect($validatedData)->except('immediate_family')->toArray();
            $employee = $this->employeeRepository->create($employeeData);

            // Create immediate family members if provided
            if (isset($validatedData['immediate_family'])) {
                foreach ($validatedData['immediate_family'] as $familyData) {
                    $familyData['employee_id'] = $employee->id;
                    ImmediateFamily::create($familyData);
                }
            }

            DB::commit();

            // Load the employee with immediate family for response
            $employee->load('immediateFamily');

            return response()->json($employee, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error creating employee: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/employees/{id}",
     *     tags={"employees"},
     *     summary="Obtener un empleado específico",
     *     description="Obtiene los detalles de un empleado por su ID",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Empleado encontrado",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(string $id)
    {
        $employee = $this->employeeRepository->find($id);
        if (! $employee) {
            return response()->json(['message' => 'Employee not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($employee);
    }

    /**
     * @OA\Put(
     *     path="/v1/employees/{id}",
     *     tags={"employees"},
     *     summary="Actualizar un empleado",
     *     description="Actualiza los datos de un empleado existente",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del empleado a actualizar",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="Juan Pérez", description="Nombre completo del empleado"),
     *             @OA\Property(property="email", type="string", format="email", example="juan.perez@company.com", description="Correo electrónico"),
     *             @OA\Property(property="position", type="string", example="Desarrollador Senior", description="Cargo o posición"),
     *             @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15", description="Fecha de contratación"),
     *             @OA\Property(
     *                 property="immediate_family",
     *                 type="array",
     *                 description="Lista de familiares inmediatos (opcional). Para actualizar: incluir 'id'. Para eliminar: incluir 'id' y '_delete': true. Para crear: omitir 'id'",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="ID del familiar (solo para actualizar o eliminar)"),
     *                     @OA\Property(property="family_name", type="string", example="María García", description="Nombre del familiar"),
     *                     @OA\Property(property="relationship", type="string", example="Hijo/a", description="Relación familiar"),
     *                     @OA\Property(property="date_of_birth", type="string", format="date", example="2025-09-16", description="Fecha de nacimiento"),
     *                     @OA\Property(property="_delete", type="boolean", example=false, description="Marcar como true para eliminar este familiar")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Empleado actualizado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:employees,email,' . $id,
            'position' => 'sometimes|string|max:255',
            'hire_date' => 'sometimes|date',
            'immediate_family' => 'sometimes|array',
            'immediate_family.*.id' => 'sometimes|integer|exists:immediate_family,id',
            'immediate_family.*.family_name' => 'required_with:immediate_family|string|max:255',
            'immediate_family.*.relationship' => 'required_with:immediate_family|string|max:255',
            'immediate_family.*.date_of_birth' => 'required_with:immediate_family|date',
            'immediate_family.*._delete' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Update employee
            $employeeData = collect($validatedData)->except('immediate_family')->toArray();
            $employee = $this->employeeRepository->update($id, $employeeData);
            
            if (! $employee) {
                DB::rollback();
                return response()->json(['message' => 'Employee not found'], Response::HTTP_NOT_FOUND);
            }

            // Handle immediate family updates if provided
            if (isset($validatedData['immediate_family'])) {
                $currentFamilyIds = [];
                
                foreach ($validatedData['immediate_family'] as $familyData) {
                    // If _delete is true, delete the family member
                    if (isset($familyData['_delete']) && $familyData['_delete'] && isset($familyData['id'])) {
                        ImmediateFamily::where('id', $familyData['id'])
                            ->where('employee_id', $employee->id)
                            ->delete();
                        continue;
                    }
                    
                    // Remove control fields
                    unset($familyData['_delete']);
                    
                    if (isset($familyData['id'])) {
                        // Update existing family member
                        $familyMember = ImmediateFamily::where('id', $familyData['id'])
                            ->where('employee_id', $employee->id)
                            ->first();
                        
                        if ($familyMember) {
                            $familyMember->update(collect($familyData)->except('id')->toArray());
                            $currentFamilyIds[] = $familyData['id'];
                        }
                    } else {
                        // Create new family member
                        $familyData['employee_id'] = $employee->id;
                        $newFamilyMember = ImmediateFamily::create($familyData);
                        $currentFamilyIds[] = $newFamilyMember->id;
                    }
                }
            }

            DB::commit();

            // Load the employee with immediate family for response
            $employee->load('immediateFamily');

            return response()->json($employee);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error updating employee: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/employees/{id}",
     *     tags={"employees"},
     *     summary="Eliminar un empleado",
     *     description="Elimina un empleado del sistema",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Empleado eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $deleted = $this->employeeRepository->delete($id);
        if (! $deleted) {
            return response()->json(['message' => 'Employee not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
