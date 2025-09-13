<?php

namespace App\Http\Controllers;

use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeController extends Controller
{
    protected EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display a listing of the resource with advanced filtering.
     * 
     * Query parameters supported:
     * - page: Page number for pagination
     * - limit: Number of items per page
     * - search: Search term across searchable columns
     * - sort: Comma-separated list of columns to sort by (prefix with - for desc)
     * - select: Comma-separated list of columns to select
     * - include: Comma-separated list of relations to include
     * - filter[column][$operator]: Filter by column with operator
     *   Operators: $eq, $not, $null, $in, $gt, $gte, $lt, $lte, $btw, $ilike, $sw, $contains
     */
    public function index(Request $request)
    {
        $employees = $this->employeeRepository->filter($request);
        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employee = $this->employeeRepository->create($request->all());

        return response()->json($employee, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
