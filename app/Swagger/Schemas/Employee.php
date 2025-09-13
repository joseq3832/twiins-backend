<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Employee",
 *     type="object",
 *     title="Employee",
 *     description="Modelo de empleado",
 *     required={"name", "email", "position", "hire_date"},
 *
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID único del empleado",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre completo del empleado",
 *         example="Juan Pérez"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Correo electrónico del empleado",
 *         example="juan.perez@company.com"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="string",
 *         description="Cargo o posición del empleado",
 *         example="Desarrollador Senior"
 *     ),
 *     @OA\Property(
 *         property="hire_date",
 *         type="string",
 *         format="date",
 *         description="Fecha de contratación",
 *         example="2023-01-15"
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
 *         property="immediate_family",
 *         type="array",
 *         description="Lista de familiares inmediatos",
 *
 *         @OA\Items(ref="#/components/schemas/ImmediateFamily")
 *     )
 * )
 */
class Employee
{
    // Esta clase solo contiene anotaciones de Swagger
}
