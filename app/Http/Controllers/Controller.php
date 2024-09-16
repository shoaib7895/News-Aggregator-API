<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="API Docs",
 *     description="Admin API Documentation"
 *   )
 * @OA\PathItem(
 *     path="/example",
 *     @OA\Get(
 *         summary="Example endpoint",
 *         @OA\Response(
 *             response="200",
 *             description="Successful response"
 *         )
 *     )
 * )
 *  @OA\SecurityScheme(
 *     type="http",
 *     description="Use a Bearer token for authorization",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="2|aoNzWP40BWNqCwVTJO0Kk8xwfad20citddWrUqpd66b2a474"
 * )
 */
abstract class Controller
{
    //
}
