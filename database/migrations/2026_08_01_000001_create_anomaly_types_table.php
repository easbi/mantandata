<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_types', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // NR, IJ, DUP, OUTLIER, dst
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            // Konfigurasi dynamic form/table per jenis anomali (tanpa coding)
            $table->json('kolom_wajib')->nullable();   // kolom wajib saat import
            $table->json('kolom_tampil')->nullable();  // kolom yang ditampilkan di tabel
            $table->json('form_followup')->nullable(); // definisi field form tindak lanjut
            $table->string('warna', 20)->nullable();   // untuk badge di dashboard
            $table->string('ikon', 50)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_types');
    }
};
