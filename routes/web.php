<?php

use App\Http\Controllers\Front\Home\HomeController;
use App\RoutePaths\Front\Page\PageRoutePath;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'show'])->name(PageRoutePath::HOME);

/**
 * Health check for the platform load balancer. Verifies the database is
 * reachable, not just that PHP responded.
 */
Route::get('/up', function () {
    try {
        DB::connection()->getPdo();
    } catch (\Throwable $e) {
        return response()->json(['status' => 'error', 'database' => 'unreachable'], 503);
    }

    return response()->json(['status' => 'ok']);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
