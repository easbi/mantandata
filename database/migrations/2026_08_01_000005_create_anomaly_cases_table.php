<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_cases', function (Blueprint $table) {
            $table->id();

            // identitas permanen: sha1(assignment_id + kode_anomali [+ nks/id_responden bila perlu])
            $table->string('anomaly_key', 64)->unique();

            $table->foreignId('anomaly_type_id')->constrained('anomaly_types')->cascadeOnDelete();

            $table->string('assignment_id')->index();
            $table->string('nks')->nullable();
            $table->string('id_responden')->nullable();
            $table->string('kode_wilayah', 20)->nullable()->index();

            // dua dimensi status (terpisah, sesuai kesepakatan)
            $table->enum('status_penanganan', [
                'belum_ditangani', 'proses', 'menunggu_konfirmasi', 'selesai',
            ])->default('belum_ditangani')->index();

            // status kemunculan dihitung dari latest_run_id, bukan field manual
            $table->foreignId('first_run_id')->constrained('anomaly_runs');
            $table->foreignId('latest_run_id')->constrained('anomaly_runs');

            $table->date('first_seen_at');
            $table->date('last_seen_at');
            $table->unsignedInteger('times_seen')->default(1);

            $table->timestamps();

            $table->index(['anomaly_type_id', 'latest_run_id']); // query dashboard aktif
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_cases');
    }
};
