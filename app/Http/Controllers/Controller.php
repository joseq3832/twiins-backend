<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Employee Management API",
 *     version="1.0.0",
 *     description="API para gestión de empleados y familia inmediata con sistema de filtrado avanzado",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost/api/v1",
 *     description="Servidor de desarrollo"
 * )
 * 
 * @OA\Tag(
 *     name="employees",
 *     description="Operaciones relacionadas con empleados"
 * )
 * 
 * @OA\Tag(
 *     name="immediate-family",
 *     description="Operaciones relacionadas con familia inmediata"
 * )
 * 
 * @OA\Tag(
 *     name="health",
 *     description="Verificación de estado del sistema"
 * )
 */
abstract class Controller
{
    //
}
