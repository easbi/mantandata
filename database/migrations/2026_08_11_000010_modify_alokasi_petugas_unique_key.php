<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alokasi_petugas', function (Blueprint $table) {
            $table->dropUnique(['assignment_id', 'periode']);
            $table->unique(['kode_wilayah', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::table('alokasi_petugas', function (Blueprint $table) {
            $table->dropUnique(['kode_wilayah', 'periode']);
            $table->unique(['assignment_id', 'periode']);
        });
    }
};
