<?php

declare(strict_types=1);

namespace Perbendaharaan\CasAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk persekitaran local / development.
 *
 * Digunakan apabila CAS_ENABLED=false.
 * Redirect ke halaman login tempatan pakej jika pengguna belum log masuk.
 */
final class AuthLocal
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        // Simpan URL yang ingin diakses — redirect balik selepas login
        session(['cas_intended' => $request->fullUrl()]);

        return redirect()->route('cas.local.login');
    }
}
