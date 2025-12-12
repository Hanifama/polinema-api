<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Polinema API",
 *     version="1.0",
 *     description="API untuk sistem Polinema Apps Mobile",
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api", 
 *     description="Local Server"
 * )
 * @OA\Server(
 *     url="https://api-polinema.webview.cloud/api",
 *     description="Staging Server"
 * )
 * @OA\Server(
 *     url="http://202.10.56.166/api",
 *     description="Prod Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Gunakan Bearer Token yang dihasilkan setelah login untuk mengakses semua API pada projek ini"
 * )
 */
abstract class Controller
{
    //
}
