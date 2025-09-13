<?php

namespace App\Http\Controllers;

use App\Repositories\ImmediateFamilyRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Immediate Family",
 *     description="API Endpoints for managing employee immediate family members"
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
     *     path="/api/immediate-family",
     *     summary="Get all immediate family members",
     *     tags={"Immediate Family"},
     *     @OA\Response(
     *         response=200,
     *         description="List of immediate family members",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImmediateFamily")
     *         )
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
     *     path="/api/immediate-family",
     *     summary="Create a new immediate family member",
     *     tags={"Immediate Family"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_id", "family_name", "relationship", "date_of_birth"},
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="family_name", type="string", example="John Doe"),
     *             @OA\Property(property="relationship", type="string", example="spouse"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-05-15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Immediate family member created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ImmediateFamily")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
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
     *     path="/api/immediate-family/{id}",
     *     summary="Get immediate family member by ID",
     *     tags={"Immediate Family"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Immediate family member details",
     *         @OA\JsonContent(ref="#/components/schemas/ImmediateFamily")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Immediate family member not found"
     *     )
     * )
     */
    public function show($id)
    {
        $immediateFamily = $this->immediateFamilyRepository->find($id);
        
        if (!$immediateFamily) {
            return response()->json(['message' => 'Immediate family member not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($immediateFamily);
    }

    /**
     * @OA\Put(
     *     path="/api/immediate-family/{id}",
     *     summary="Update immediate family member",
     *     tags={"Immediate Family"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="family_name", type="string", example="John Doe"),
     *             @OA\Property(property="relationship", type="string", example="spouse"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-05-15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Immediate family member updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ImmediateFamily")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Immediate family member not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $immediateFamily = $this->immediateFamilyRepository->find($id);
        
        if (!$immediateFamily) {
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
     *     path="/api/immediate-family/{id}",
     *     summary="Delete immediate family member",
     *     tags={"Immediate Family"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Immediate family member deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Immediate family member not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $immediateFamily = $this->immediateFamilyRepository->find($id);
        
        if (!$immediateFamily) {
            return response()->json(['message' => 'Immediate family member not found'], Response::HTTP_NOT_FOUND);
        }

        $this->immediateFamilyRepository->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/api/employees/{employeeId}/immediate-family",
     *     summary="Get immediate family members by employee ID",
     *     tags={"Immediate Family"},
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of immediate family members for the employee",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImmediateFamily")
     *         )
     *     )
     * )
     */
    public function getByEmployee($employeeId)
    {
        $immediateFamily = $this->immediateFamilyRepository->getByEmployeeId($employeeId);
        return response()->json($immediateFamily);
    }
}