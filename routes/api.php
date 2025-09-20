<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    DirectoryController,
    LocationController,
    BusinessController,
    MenuController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Prefix padrão: /api
*/

Route::get('/filters',   [DirectoryController::class, 'filters']);          // cidades + categorias
Route::get('/locations', [LocationController::class, 'index']);            // busca da Home
Route::get('/business/{slug}', [BusinessController::class, 'show']);       // página da empresa
Route::get('/menu/{businessSlug}', [MenuController::class, 'show']);       // cardápio

// Ex.: futuramente rotas autenticadas (donos)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/business', ...);
// });
