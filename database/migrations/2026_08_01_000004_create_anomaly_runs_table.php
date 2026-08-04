<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anomaly_type_id')->constrained('anomaly_types')->cascadeOnDelete();
            $table->date('tanggal_query'); // tanggal kondisi query, mis. 23-07-2026 dari nama file
            $table->string('nama_file')->nullable();
            $table->unsignedInteger('jumlah_data')->default(0);
            $table->unsignedInteger('jumlah_case_baru')->default(0);
            $table->unsignedInteger('jumlah_case_lama')->default(0);
            $table->unsignedInteger('jumlah_error')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // index untuk cari "run terakhir per jenis anomali" dengan cepat
            $table->index(['anomaly_type_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_runs');
    }
};
