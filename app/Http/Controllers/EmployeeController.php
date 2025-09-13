<?php

namespace App\Http\Controllers;

use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     *             @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15", description="Fecha de contratación")
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
        $employee = $this->employeeRepository->create($request->all());

        return response()->json($employee, Response::HTTP_CREATED);
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
     *             @OA\Property(property="hire_date", type="string", format="date", example="2023-01-15", description="Fecha de contratación")
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
        $employee = $this->employeeRepository->update($id, $request->all());
        if (! $employee) {
            return response()->json(['message' => 'Employee not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($employee);
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
