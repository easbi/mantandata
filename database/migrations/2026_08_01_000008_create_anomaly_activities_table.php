<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('anomaly_cases')->cascadeOnDelete();

            // IMPORT (muncul), IMPORT_HIDDEN (tidak muncul di run ybs), IMPORT_REOPEN (muncul lagi
            // setelah sempat hilang), FOLLOWUP, SYSTEM
            $table->string('activity_type', 30)->index();

            $table->unsignedBigInteger('reference_id')->nullable(); // run_id atau followup_id
            $table->date('activity_date');
            $table->json('payload')->nullable(); // ringkasan teks/data untuk ditampilkan di timeline

            $table->timestamps();

            $table->index(['case_id', 'activity_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_activities');
    }
};
