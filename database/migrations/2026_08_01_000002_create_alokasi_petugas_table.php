<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alokasi_petugas', function (Blueprint $table) {
            $table->id();
            $table->string('assignment_id')->index();
            $table->string('kode_wilayah', 20)->nullable()->index();
            $table->string('nama_wilayah')->nullable();

            $table->string('ppl_id')->nullable();
            $table->string('ppl_nama')->nullable();

            $table->string('pml_id')->nullable();
            $table->string('pml_nama')->nullable();

            $table->string('taskforce_id')->nullable();
            $table->string('taskforce_nama')->nullable();

            $table->date('periode')->nullable(); // untuk alokasi yang berubah per periode
            $table->timestamps();

            // satu assignment hanya satu alokasi aktif per periode
            $table->unique(['assignment_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alokasi_petugas');
    }
};
