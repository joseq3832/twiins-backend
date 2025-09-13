<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="ImmediateFamily",
 *     type="object",
 *     title="Immediate Family",
 *     description="Immediate Family model",
 *     required={"id", "employee_id", "family_name", "relationship", "date_of_birth"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier for the immediate family member",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="employee_id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the employee this family member belongs to",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="family_name",
 *         type="string",
 *         maxLength=255,
 *         description="Full name of the family member",
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="relationship",
 *         type="string",
 *         maxLength=255,
 *         description="Relationship to the employee",
 *         example="spouse",
 *         enum={"spouse", "child", "parent", "sibling", "grandparent", "grandchild"}
 *     ),
 *     @OA\Property(
 *         property="date_of_birth",
 *         type="string",
 *         format="date",
 *         description="Date of birth of the family member",
 *         example="1990-05-15"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the record was created",
 *         example="2023-09-13T10:30:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the record was last updated",
 *         example="2023-09-13T10:30:00Z"
 *     )
 * )
 */
class ImmediateFamily
{
    // This class is used only for Swagger documentation
}