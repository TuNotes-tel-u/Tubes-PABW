<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aktifkan autentikasi session (Sanctum) untuk request API dari browser,
        // sehingga halaman view bisa memanggil API dengan cookie login yang sama.
        $middleware->statefulApi();

        // Semua response /api/* dipaksa berformat JSON (termasuk error validasi).
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 404 untuk API harus berupa JSON, bukan halaman HTML error.
        $exceptions->render(function (NotFoundHttpException|ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Catatan tidak ditemukan.',
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }
        });
    })->create();
