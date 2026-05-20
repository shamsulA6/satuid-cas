<?php

declare(strict_types=1);

namespace Perbendaharaan\CasAuth\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'cas:install')]
final class CasInstall extends Command
{
    protected $signature   = 'cas:install';
    protected $description = 'Pasang dan konfigurasikan pakej CAS Auth Perbendaharaan';

    public function handle(): int
    {
        $this->newLine();
        $this->line('<fg=magenta>╔══════════════════════════════════════════════╗</>');
        $this->line('<fg=magenta>║</>   <fg=white;options=bold>CAS Auth — Perbendaharaan Malaysia</>         <fg=magenta>║</>');
        $this->line('<fg=magenta>║</>   <fg=gray>perbendaharaan/cas-auth (Laravel 12)</>       <fg=magenta>║</>');
        $this->line('<fg=magenta>╚══════════════════════════════════════════════╝</>');
        $this->newLine();

        $this->publishConfig();
        $this->publishMigrations();
        $this->publishViews();
        $this->printNextSteps();

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $this->info('  Menerbitkan config/cas.php ...');
        $this->callSilently('vendor:publish', ['--tag' => 'cas-config', '--force' => false]);
        $this->line('  <fg=green>✓</> config/cas.php & .env.cas.example berjaya diterbitkan');
    }

    private function publishMigrations(): void
    {
        $this->info('  Menerbitkan database migration ...');
        $this->callSilently('vendor:publish', ['--tag' => 'cas-migrations', '--force' => false]);
        $this->line('  <fg=green>✓</> Migration berjaya diterbitkan');
    }

    private function publishViews(): void
    {
        $this->info('  Menerbitkan views login tempatan ...');
        $this->callSilently('vendor:publish', ['--tag' => 'cas-views', '--force' => false]);
        $this->line('  <fg=green>✓</> Views diterbitkan ke resources/views/vendor/cas-auth/');
        $this->newLine();
    }

    private function printNextSteps(): void
    {
        $this->warn('  Langkah seterusnya:');
        $this->newLine();

        $this->line('  <fg=yellow>1.</> Salin nilai dari .env.cas.example ke .env');
        $this->line('  <fg=yellow>2.</> Tukar <fg=cyan>CAS_CLIENT_SERVICE</> kepada URL sistem anda');
        $this->line('  <fg=yellow>3.</> Jalankan: <fg=cyan>php artisan migrate</>');
        $this->line('  <fg=yellow>4.</> Dalam <fg=cyan>routes/web.php</>, pilih middleware ikut persekitaran:');
        $this->newLine();
        $this->line("         <fg=gray>// Production/Staging (SATUID)</>");
        $this->line("         <fg=cyan>\$mid = ['cas.auth'];</>");
        $this->newLine();
        $this->line("         <fg=gray>// Development tempatan</>");
        $this->line("         <fg=cyan>\$mid = ['cas.local'];</>");
        $this->newLine();
        $this->line("         <fg=gray>// Atau auto-detect ikut .env</>");
        $this->line("         <fg=cyan>\$mid = config('cas.cas_enabled') ? ['cas.auth'] : ['cas.local'];</>");
        $this->newLine();
        $this->line('  <fg=yellow>5.</> Daftarkan URL sistem dengan pihak SATUID (untuk production)');
        $this->newLine();
        $this->line('  <fg=green>✓ Selesai!</> Rujuk README.md untuk panduan penuh.');
        $this->newLine();
    }
}
