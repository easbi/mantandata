<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$app['config']->set('database.default', 'sqlite');
$app['config']->set('database.connections.sqlite', [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
]);

$app['db']->setDefaultConnection('sqlite');
$app['db']->purge('sqlite');

Illuminate\Support\Facades\Schema::create('users', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamps();
});

Illuminate\Support\Facades\Schema::create('anomaly_types', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->string('kode')->unique();
    $table->string('nama');
    $table->text('deskripsi')->nullable();
    $table->json('kolom_wajib')->nullable();
    $table->json('kolom_tampil')->nullable();
    $table->json('form_followup')->nullable();
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});

Illuminate\Support\Facades\Schema::create('anomaly_runs', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->foreignId('anomaly_type_id');
    $table->date('tanggal_query');
    $table->string('nama_file')->nullable();
    $table->unsignedInteger('jumlah_data')->default(0);
    $table->unsignedInteger('jumlah_case_baru')->default(0);
    $table->unsignedInteger('jumlah_case_lama')->default(0);
    $table->unsignedInteger('jumlah_error')->default(0);
    $table->foreignId('created_by')->nullable();
    $table->timestamps();
});

Illuminate\Support\Facades\Schema::create('anomaly_cases', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->string('anomaly_key', 64)->unique();
    $table->foreignId('anomaly_type_id');
    $table->string('assignment_id')->index();
    $table->string('nks')->nullable();
    $table->string('id_responden')->nullable();
    $table->string('kode_wilayah', 20)->nullable()->index();
    $table->enum('status_penanganan', ['belum_ditangani', 'proses', 'menunggu_konfirmasi', 'selesai'])->default('belum_ditangani');
    $table->foreignId('first_run_id');
    $table->foreignId('latest_run_id');
    $table->date('first_seen_at');
    $table->date('last_seen_at');
    $table->unsignedInteger('times_seen')->default(1);
    $table->timestamps();
});

Illuminate\Support\Facades\Schema::create('anomaly_snapshots', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->foreignId('run_id');
    $table->foreignId('case_id');
    $table->string('assignment_id')->index();
    $table->json('data_query');
    $table->string('ppl_id')->nullable();
    $table->string('ppl_nama')->nullable();
    $table->string('pml_id')->nullable();
    $table->string('pml_nama')->nullable();
    $table->string('taskforce_id')->nullable();
    $table->string('taskforce_nama')->nullable();
    $table->timestamps();
});

Illuminate\Support\Facades\Schema::create('anomaly_followups', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->foreignId('case_id');
    $table->foreignId('snapshot_id')->nullable();
    $table->foreignId('user_id');
    $table->enum('status', ['belum_ditangani', 'proses', 'menunggu_konfirmasi', 'selesai']);
    $table->text('catatan')->nullable();
    $table->string('lampiran')->nullable();
    $table->timestamps();
});

Illuminate\Support\Facades\Schema::create('anomaly_activities', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->foreignId('case_id');
    $table->string('activity_type', 30);
    $table->unsignedBigInteger('reference_id')->nullable();
    $table->date('activity_date');
    $table->json('payload')->nullable();
    $table->timestamps();
});

Illuminate\Support\Facades\Schema::create('alokasi_petugas', function (Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->string('assignment_id')->index();
    $table->string('kode_wilayah')->nullable();
    $table->string('ppl_id')->nullable();
    $table->string('ppl_nama')->nullable();
    $table->string('pml_id')->nullable();
    $table->string('pml_nama')->nullable();
    $table->string('taskforce_id')->nullable();
    $table->string('taskforce_nama')->nullable();
    $table->date('periode');
    $table->timestamps();
});

$type = new App\Models\AnomalyType([
    'kode' => 'NON_RESP',
    'nama' => 'Non Respon',
    'deskripsi' => 'Test anomaly type',
    'kolom_wajib' => ['assignment_id'],
    'kolom_tampil' => ['assignment_id', 'nks'],
    'form_followup' => [],
    'aktif' => true,
]);
$type->save();

App\Models\AlokasiPetugas::create([
    'assignment_id' => 'A-001',
    'kode_wilayah' => 'W-001',
    'ppl_id' => 'PPL-1',
    'ppl_nama' => 'PPL Test',
    'pml_id' => 'PML-1',
    'pml_nama' => 'PML Test',
    'taskforce_id' => 'TF-1',
    'taskforce_nama' => 'TF Test',
    'periode' => '2026-08-01',
]);

$service = new App\Services\ImportAnomalyService();
$row = ['assignment_id' => 'A-001', 'nks' => 'NKS-001', 'id_responden' => 'RESP-001', 'kode_wilayah' => 'W-001'];
$service->import($type, [$row], Carbon\Carbon::parse('2026-08-01'), 'first.xlsx', 1);
$service->import($type, [$row], Carbon\Carbon::parse('2026-08-02'), 'second.xlsx', 1);

$case = App\Models\AnomalyCase::first();
echo 'cases=' . App\Models\AnomalyCase::count() . PHP_EOL;
echo 'snapshots=' . App\Models\AnomalySnapshot::count() . PHP_EOL;
echo 'activities=' . App\Models\AnomalyActivity::count() . PHP_EOL;
echo 'times_seen=' . $case->times_seen . PHP_EOL;
echo 'latest_run=' . $case->latest_run_id . PHP_EOL;
echo 'active=' . (App\Models\AnomalyCase::query()->active($type->id)->whereKey($case->id)->exists() ? 'yes' : 'no') . PHP_EOL;
