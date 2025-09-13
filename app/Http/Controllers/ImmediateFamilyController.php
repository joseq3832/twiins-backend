<?php

namespace App\Http\Controllers;

use App\Repositories\ImmediateFamilyRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="immediate-family",
 *     description="Endpoints para gestionar los familiares inmediatos de los empleados"
 * )
 */
class ImmediateFamilyController extends Controller
{
    protected $immediateFamilyRepository;

    public function __construct(ImmediateFamilyRepository $immediateFamilyRepository)
    {
        $this->immediateFamilyRepository = $immediateFamilyRepository;
    }

    /**
     * @OA\Get(
     *     path="/v1/immediate-family",
     *     tags={"immediate-family"},
     *     summary="Obtener todos los familiares inmediatos",
     *     description="Obtiene una lista paginada de todos los familiares inmediatos",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de elementos por página",
     *
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de familiares inmediatos",
     *
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedImmediateFamilyResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Error en los parámetros",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index()
    {
        $immediateFamily = $this->immediateFamilyRepository->all();

        return response()->json($immediateFamily);
    }

    /**
     * @OA\Post(
     *     path="/v1/immediate-family",
     *     tags={"immediate-family"},
     *     summary="Crear un nuevo familiar inmediato",
     *     description="Crea un nuevo familiar inmediato para un empleado",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del familiar inmediato a crear",
     *
     *         @OA\JsonContent(
     *             required={"employee_id", "relative_name", "relationship", "birth_date"},
     *
     *             @OA\Property(property="employee_id", type="integer", example=1, description="ID del empleado"),
     *             @OA\Property(property="relative_name", type="string", example="María García", description="Nombre del familiar"),
     *             @OA\Property(property="relationship", type="string", example="esposa", description="Relación familiar"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-05-15", description="Fecha de nacimiento")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Familiar inmediato creado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ImmediateFamily")
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
            'employee_id' => 'required|integer|exists:employees,id',
            'family_name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
        ]);

        $immediateFamily = $this->immediateFamilyRepository->create($validatedData);

        return response()->json($immediateFamily, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/v1/immediate-family/{id}",
     *     tags={"immediate-family"},
     *     summary="Obtener un familiar inmediato específico",
     *     description="Obtiene los detalles de un familiar inmediato por su ID",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del familiar inmediato",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Familiar inmediato encontrado",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ImmediateFamily")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Familiar inmediato no encontrado",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show($id)
    {
        $immediateFamily = $this->immediateFamilyRepository->find($id);

        if (! $immediateFamily) {
            return response()->json(['message' => 'Immediate family member not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($immediateFamily);
    }

    /**
     * @OA\Put(
     *     path="/v1/immediate-family/{id}",
     *     tags={"immediate-family"},
     *     summary="Actualizar un familiar inmediato",
     *     description="Actualiza los datos de un familiar inmediato existente",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del familiar inmediato",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del familiar inmediato a actualizar",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="employee_id", type="integer", example=1, description="ID del empleado"),
     *             @OA\Property(property="relative_name", type="string", example="María García", description="Nombre del familiar"),
     *             @OA\Property(property="relationship", type="string", example="esposa", description="Relación familiar"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-05-15", description="Fecha de nacimiento")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Familiar inmediato actualizado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ImmediateFamily")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Familiar inmediato no encontrado",
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
    public function update(Request $request, $id)
    {
        $immediateFamily = $this->immediateFamilyRepository->find($id);

        if (! $immediateFamily) {
            return response()->json(['message' => 'Immediate family member not found'], Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validate([
            'employee_id' => 'sometimes|integer|exists:employees,id',
            'family_name' => 'sometimes|string|max:255',
            'relationship' => 'sometimes|string|max:255',
            'date_of_birth' => 'sometimes|date',
        ]);

        $updatedImmediateFamily = $this->immediateFamilyRepository->update($id, $validatedData);

        return response()->json($updatedImmediateFamily);
    }

    /**
     * @OA\Delete(
     *     path="/v1/immediate-family/{id}",
     *     tags={"immediate-family"},
     *     summary="Eliminar un familiar inmediato",
     *     description="Elimina un familiar inmediato del sistema",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del familiar inmediato",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Familiar inmediato eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Familiar inmediato no encontrado",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy($id)
    {
        $immediateFamily = $this->immediateFamilyRepository->find($id);

        if (! $immediateFamily) {
            return response()->json(['message' => 'Immediate family member not found'], Response::HTTP_NOT_FOUND);
        }

        $this->immediateFamilyRepository->delete($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/v1/employees/{employeeId}/immediate-family",
     *     tags={"immediate-family"},
     *     summary="Obtener familiares inmediatos por empleado",
     *     description="Obtiene todos los familiares inmediatos de un empleado específico",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de familiares inmediatos del empleado",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/ImmediateFamily")
     *         )
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
    public function getByEmployee($employeeId)
    {
        $immediateFamily = $this->immediateFamilyRepository->getByEmployeeId($employeeId);

        return response()->json($immediateFamily);
    }
}
