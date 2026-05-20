<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Toggle CAS
    |--------------------------------------------------------------------------
    | true  → SATUID (pengesahan CAS) — staging / production
    | false → Laravel standard auth   — development tempatan
    */
    'cas_enabled' => (bool) env('CAS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | CAS Server (SATUID)
    |--------------------------------------------------------------------------
    */
    'hostname' => env('CAS_HOSTNAME', 'satuid.treasury.gov.my'),
    'port'     => (int) env('CAS_PORT', 443),
    'uri'      => env('CAS_URI', '/gk'),

    /*
    |--------------------------------------------------------------------------
    | URL Aplikasi — MESTI didaftarkan di SATUID
    |--------------------------------------------------------------------------
    */
    'client_service' => env('CAS_CLIENT_SERVICE', env('APP_URL', 'http://localhost')),

    /*
    |--------------------------------------------------------------------------
    | Redirect selepas login / logout
    |--------------------------------------------------------------------------
    */
    'redirect_path'  => env('CAS_REDIRECT_PATH',  '/dashboard'),
    'logout_url'     => env('CAS_LOGOUT_URL',     'https://satuid.treasury.gov.my/gk/logout'),
    'logout_redirect'=> env('CAS_LOGOUT_REDIRECT', env('APP_URL', 'http://localhost')),

    /*
    |--------------------------------------------------------------------------
    | Endpoint validasi ticket SATUID
    |--------------------------------------------------------------------------
    */
    'validate_path' => env('CAS_VALIDATE_PATH', '/gk/serviceValidate'),

    /*
    |--------------------------------------------------------------------------
    | SSL verification
    | 'ca'   → verify SSL cert (production)
    | 'none' → skip verification (development)
    |--------------------------------------------------------------------------
    */
    'validation' => env('CAS_VALIDATION', 'none'),

    /*
    |--------------------------------------------------------------------------
    | Pemetaan LDAP → User DB
    |--------------------------------------------------------------------------
    */
    'ldap_column' => env('CAS_LDAP_COLUMN', 'id_pgn_ldap'),
    'user_model'  => env('CAS_USER_MODEL',  \App\Models\User::class),

];
