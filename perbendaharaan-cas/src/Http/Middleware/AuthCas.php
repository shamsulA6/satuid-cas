<?php

declare(strict_types=1);

namespace Perbendaharaan\CasAuth\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Response;

final class AuthCas
{
    private const CAS_NAMESPACE = 'http://www.yale.edu/tp/cas';
    private const HTTP_TIMEOUT  = 10;

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Sudah log masuk — teruskan ke controller
        if (Auth::check()) {
            Log::info('CAS: User already authenticated', ['user_id' => Auth::id()]);
            return $next($request);
        }

        $ticket = $request->query('ticket');

        // 2. Ada ticket CAS dalam URL — proses validasi
        if (is_string($ticket) && $ticket !== '') {
            Log::info('CAS: Ticket received');
            return $this->processTicket($request, $ticket);
        }

        // 3. Tiada ticket — redirect pengguna ke SATUID untuk login
        $loginUrl = $this->buildLoginUrl($this->currentServiceUrl($request));
        Log::info('CAS: Redirecting to SATUID login', ['url' => $loginUrl]);

        return redirect()->away($loginUrl);
    }

    // -------------------------------------------------------------------------
    // Ticket validation
    // -------------------------------------------------------------------------

    private function processTicket(Request $request, string $ticket): RedirectResponse
    {
        Log::info('CAS: Validating ticket', ['ticket' => $ticket]);

        $serviceUrl  = $this->cleanServiceUrl($request);
        $validateUrl = $this->buildValidateUrl($serviceUrl, $ticket);
        $xml         = $this->fetchCasResponse($validateUrl);

        if ($xml === false) {
            Log::error('CAS: Failed to reach CAS server', ['url' => $validateUrl]);
            abort(Response::HTTP_UNAUTHORIZED, 'Tidak dapat menghubungi pelayan CAS. Sila cuba lagi.');
        }

        $ldapId = $this->extractLdapId($xml);

        if ($ldapId === null) {
            Log::warning('CAS: Invalid or rejected ticket');
            abort(Response::HTTP_UNAUTHORIZED, 'Pengesahan CAS gagal. Ticket tidak sah.');
        }

        Log::info('CAS: Authentication successful', ['ldap_id' => $ldapId]);

        $user = $this->resolveUser($ldapId);

        if ($user === null) {
            Log::warning('CAS: LDAP ID not found in database', ['ldap_id' => $ldapId]);
            abort(
                Response::HTTP_UNAUTHORIZED,
                "Pengguna '{$ldapId}' tidak wujud dalam sistem. Sila hubungi pentadbir."
            );
        }

        Auth::login($user, remember: true);

        // Redirect ke URL bersih (tanpa ?ticket=) supaya ticket tidak tersimpan dalam history
        return redirect()->to($serviceUrl);
    }

    // -------------------------------------------------------------------------
    // URL helpers
    // -------------------------------------------------------------------------

    /**
     * URL semasa yang sedang diakses pengguna (dengan query string, kecuali ticket).
     */
    private function currentServiceUrl(Request $request): string
    {
        $base   = $request->url();
        $params = $request->except('ticket');

        return $params !== []
            ? $base . '?' . http_build_query($params)
            : $base;
    }

    /**
     * URL bersih tanpa sebarang query string — digunakan selepas login berjaya.
     */
    private function cleanServiceUrl(Request $request): string
    {
        return $request->url();
    }

    private function buildLoginUrl(string $serviceUrl): string
    {
        return $this->casBaseUrl() . '/login?service=' . urlencode($serviceUrl);
    }

    private function buildValidateUrl(string $serviceUrl, string $ticket): string
    {
        $path = ltrim((string) config('cas.validate_path', '/gk/serviceValidate'), '/');

        return $this->casSchemeHost() . '/' . $path
            . '?service=' . urlencode($serviceUrl)
            . '&ticket='  . urlencode($ticket);
    }

    private function casBaseUrl(): string
    {
        return $this->casSchemeHost() . '/' . ltrim((string) config('cas.uri', '/gk'), '/');
    }

    private function casSchemeHost(): string
    {
        $hostname = (string) config('cas.hostname', 'satuid.treasury.gov.my');
        $port     = (int)    config('cas.port', 443);
        $scheme   = $port === 443 ? 'https' : 'http';
        $suffix   = in_array($port, [80, 443], strict: true) ? '' : ":{$port}";

        return "{$scheme}://{$hostname}{$suffix}";
    }

    // -------------------------------------------------------------------------
    // CAS server communication
    // -------------------------------------------------------------------------

    private function fetchCasResponse(string $url): string|false
    {
        $verifySsl = config('cas.validation', 'none') !== 'none';

        $context = stream_context_create([
            'ssl'  => [
                'verify_peer'      => $verifySsl,
                'verify_peer_name' => $verifySsl,
            ],
            'http' => [
                'method'  => 'GET',
                'timeout' => self::HTTP_TIMEOUT,
            ],
        ]);

        return @file_get_contents($url, context: $context);
    }

    // -------------------------------------------------------------------------
    // XML parsing
    // -------------------------------------------------------------------------

    private function extractLdapId(string $rawXml): ?string
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($rawXml);

        if (!$doc instanceof SimpleXMLElement) {
            Log::error('CAS: Failed to parse XML response', [
                'errors' => array_map(
                    static fn ($e) => $e->message,
                    libxml_get_errors()
                ),
            ]);
            return null;
        }

        $doc->registerXPathNamespace('cas', self::CAS_NAMESPACE);

        /** @var list<SimpleXMLElement>|false $success */
        $success = $doc->xpath('//cas:authenticationSuccess');

        if (empty($success)) {
            /** @var list<SimpleXMLElement>|false $failure */
            $failure = $doc->xpath('//cas:authenticationFailure');

            if (!empty($failure)) {
                Log::warning('CAS: Server returned authenticationFailure', [
                    'reason' => trim((string) $failure[0]),
                ]);
            }

            return null;
        }

        /** @var list<SimpleXMLElement>|false $users */
        $users = $doc->xpath('//cas:user');

        if (empty($users)) {
            Log::error('CAS: <cas:user> element missing from response');
            return null;
        }

        $ldapId = trim((string) $users[0]);

        return $ldapId !== '' ? $ldapId : null;
    }

    // -------------------------------------------------------------------------
    // User resolution
    // -------------------------------------------------------------------------

    private function resolveUser(string $ldapId): mixed
    {
        /** @var class-string $model */
        $model  = config('cas.user_model', \App\Models\User::class);
        $column = (string) config('cas.ldap_column', 'id_pgn_ldap');

        return $model::where($column, $ldapId)->first();
    }
}
