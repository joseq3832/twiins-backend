<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="PaginatedEmployeeResponse",
 *     type="object",
 *     title="PaginatedEmployeeResponse",
 *     description="Respuesta paginada de empleados",
 *
 *     @OA\Property(
 *         property="current_page",
 *         type="integer",
 *         description="Página actual",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         description="Lista de empleados",
 *
 *         @OA\Items(ref="#/components/schemas/Employee")
 *     ),
 *
 *     @OA\Property(
 *         property="first_page_url",
 *         type="string",
 *         description="URL de la primera página",
 *         example="http://localhost/api/v1/employees?page=1"
 *     ),
 *     @OA\Property(
 *         property="from",
 *         type="integer",
 *         description="Número del primer elemento",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="last_page",
 *         type="integer",
 *         description="Número de la última página",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="last_page_url",
 *         type="string",
 *         description="URL de la última página",
 *         example="http://localhost/api/v1/employees?page=5"
 *     ),
 *     @OA\Property(
 *         property="next_page_url",
 *         type="string",
 *         nullable=true,
 *         description="URL de la siguiente página",
 *         example="http://localhost/api/v1/employees?page=2"
 *     ),
 *     @OA\Property(
 *         property="path",
 *         type="string",
 *         description="Ruta base",
 *         example="http://localhost/api/v1/employees"
 *     ),
 *     @OA\Property(
 *         property="per_page",
 *         type="integer",
 *         description="Elementos por página",
 *         example=15
 *     ),
 *     @OA\Property(
 *         property="prev_page_url",
 *         type="string",
 *         nullable=true,
 *         description="URL de la página anterior",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="to",
 *         type="integer",
 *         description="Número del último elemento",
 *         example=15
 *     ),
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         description="Total de elementos",
 *         example=75
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedImmediateFamilyResponse",
 *     type="object",
 *     title="PaginatedImmediateFamilyResponse",
 *     description="Respuesta paginada de familia inmediata",
 *
 *     @OA\Property(
 *         property="current_page",
 *         type="integer",
 *         description="Página actual",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         description="Lista de familiares",
 *
 *         @OA\Items(ref="#/components/schemas/ImmediateFamily")
 *     ),
 *
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         description="Total de elementos",
 *         example=25
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="ErrorResponse",
 *     description="Respuesta de error",
 *
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Mensaje de error",
 *         example="Recurso no encontrado"
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Detalles de errores de validación",
 *         example={
 *             "email": {"El campo email es requerido"}
 *         }
 *     )
 * )
 */
class PaginatedResponse
{
    // Esta clase solo contiene anotaciones de Swagger
}
