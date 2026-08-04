<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // admin | task_force | pml | ppl | viewer
            $table->string('role', 20)->default('viewer')->after('email');
            // scope wilayah untuk PML (kode_wilayah), atau assignment_id untuk PPL
            $table->string('wilayah_scope')->nullable()->after('role');
            $table->boolean('aktif')->default(true)->after('wilayah_scope');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'wilayah_scope', 'aktif']);
        });
    }
};
