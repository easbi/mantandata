<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_followups', function (Blueprint $table) {
            $table->id();

            // melekat ke CASE (permanen), bukan ke snapshot
            $table->foreignId('case_id')->constrained('anomaly_cases')->cascadeOnDelete();

            // referensi snapshot saat TL dilakukan, nullable (konteks saja, bukan pemilik data)
            $table->foreignId('snapshot_id')->nullable()->constrained('anomaly_snapshots')->nullOnDelete();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('status', [
                'belum_ditangani', 'proses', 'menunggu_konfirmasi', 'selesai',
            ]);
            $table->text('catatan')->nullable();
            $table->string('lampiran')->nullable(); // path file bukti

            $table->timestamps();

            $table->index(['case_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_followups');
    }
};
