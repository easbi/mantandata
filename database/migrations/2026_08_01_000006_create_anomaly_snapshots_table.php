<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_id')->constrained('anomaly_runs')->cascadeOnDelete();
            $table->foreignId('case_id')->constrained('anomaly_cases')->cascadeOnDelete();

            $table->string('assignment_id')->index();
            $table->json('data_query'); // seluruh kolom mentah hasil query (dynamic per jenis anomali)

            // hasil join dengan alokasi_petugas pada saat import (di-freeze, bukan live join)
            $table->string('ppl_id')->nullable();
            $table->string('ppl_nama')->nullable();
            $table->string('pml_id')->nullable();
            $table->string('pml_nama')->nullable();
            $table->string('taskforce_id')->nullable();
            $table->string('taskforce_nama')->nullable();

            $table->timestamps();

            $table->unique(['run_id', 'case_id']); // satu case hanya 1 snapshot per run
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_snapshots');
    }
};
