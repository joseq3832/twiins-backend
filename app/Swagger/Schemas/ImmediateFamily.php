<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ImmediateFamily",
 *     type="object",
 *     title="ImmediateFamily",
 *     description="Modelo de familia inmediata",
 *     required={"employee_id", "relative_name", "relationship", "birth_date"},
 *
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID único del familiar",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="employee_id",
 *         type="integer",
 *         description="ID del empleado al que pertenece",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="relative_name",
 *         type="string",
 *         description="Nombre del familiar",
 *         example="María Pérez"
 *     ),
 *     @OA\Property(
 *         property="relationship",
 *         type="string",
 *         description="Relación con el empleado",
 *         enum={"spouse", "child", "parent", "sibling"},
 *         example="spouse"
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         format="date",
 *         description="Fecha de nacimiento",
 *         example="1990-05-20"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de creación del registro",
 *         example="2023-01-15T10:30:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de última actualización",
 *         example="2023-01-15T10:30:00Z"
 *     ),
 *     @OA\Property(
 *         property="employee",
 *         ref="#/components/schemas/Employee",
 *         description="Empleado al que pertenece este familiar"
 *     )
 * )
 */
class ImmediateFamily
{
    // Esta clase solo contiene anotaciones de Swagger
}
