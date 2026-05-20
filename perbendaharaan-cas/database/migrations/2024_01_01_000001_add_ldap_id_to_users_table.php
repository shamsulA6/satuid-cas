<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolum id_pgn_ldap ke jadual users.
     * Kolum ini digunakan untuk map pengguna SATUID (CAS) ke rekod tempatan.
     */
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'id_pgn_ldap')) {
                return;
            }

            $table->string('id_pgn_ldap')
                ->nullable()
                ->unique()
                ->after('id')
                ->comment('LDAP ID yang dikembalikan oleh SATUID Perbendaharaan selepas pengesahan CAS');
        });
    }

    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'id_pgn_ldap')) {
                $table->dropColumn('id_pgn_ldap');
            }
        });
    }
};
