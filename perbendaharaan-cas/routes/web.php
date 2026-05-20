<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Pakej CAS Auth — Perbendaharaan Malaysia
|--------------------------------------------------------------------------
| Dimuatkan secara automatik oleh CasAuthServiceProvider.
*/

// ---------------------------------------------------------------------------
// Shared routes (CAS & local)
// ---------------------------------------------------------------------------

/**
 * POST /cas-logout
 * Log keluar dari sesi semasa.
 * - CAS_ENABLED=true  → redirect ke SATUID logout
 * - CAS_ENABLED=false → redirect ke halaman login tempatan
 */
Route::post('/cas-logout', static function (): never {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    if ((bool) config('cas.cas_enabled')) {
        $url = (string) config('cas.logout_url')
            . '?url=' . urlencode((string) config('cas.logout_redirect'));
        redirect()->away($url)->send();
    } else {
        redirect()->route('cas.local.login')->send();
    }

    exit;
})->name('cas.logout');


/**
 * GET /auth-status
 * Semak status authentication (JSON) — berguna untuk debugging.
 */
Route::get('/auth-status', static function (): JsonResponse {
    /** @var \Illuminate\Contracts\Auth\Authenticatable|null $user */
    $user = Auth::user();

    return response()->json([
        'authenticated' => Auth::check(),
        'user'          => $user?->only(['id', 'name', 'email']),
        'cas_enabled'   => (bool) config('cas.cas_enabled'),
        'cas_server'    => config('cas.hostname'),
        'mode'          => config('cas.cas_enabled') ? 'satuid' : 'local',
    ]);
})->name('cas.status');


// ---------------------------------------------------------------------------
// Local development routes (CAS_ENABLED=false sahaja)
// ---------------------------------------------------------------------------

/**
 * GET /local-login
 * Papar borang login tempatan.
 * Hanya aktif apabila CAS_ENABLED=false.
 */
Route::get('/local-login', static function (): \Illuminate\View\View|RedirectResponse {
    if ((bool) config('cas.cas_enabled')) {
        return redirect('/');
    }

    return view('cas-auth::local.login');
})->name('cas.local.login');


/**
 * POST /local-login
 * Proses borang login tempatan menggunakan id_pgn_ldap + password.
 */
Route::post('/local-login', static function (Request $request): RedirectResponse {
    if ((bool) config('cas.cas_enabled')) {
        return redirect('/');
    }

    $credentials = $request->validate([
        'ldap_id'  => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $ldapColumn = (string) config('cas.ldap_column', 'id_pgn_ldap');

    // Cuba login menggunakan id_pgn_ldap + password
    $authenticated = Auth::attempt([
        $ldapColumn => $credentials['ldap_id'],
        'password'  => $credentials['password'],
    ]);

    if (! $authenticated) {
        return back()
            ->withInput($request->only('ldap_id'))
            ->withErrors(['ldap_id' => 'LDAP ID atau kata laluan tidak sah.']);
    }

    $request->session()->regenerate();

    // Redirect ke URL asal yang ingin diakses, atau ke redirect_path config
    $intended = session()->pull('cas_intended', config('cas.redirect_path', '/dashboard'));

    return redirect()->to((string) $intended);
})->name('cas.local.login.submit');
