<?php

namespace Tests\Feature;

use App\Models\AnomalyType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AnomalyImportUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        $this->app['db']->setDefaultConnection('sqlite');
        $this->app['db']->purge('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('anomaly_types', function (Blueprint $table) {
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

        Schema::create('anomaly_runs', function (Blueprint $table) {
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

        Schema::create('anomaly_cases', function (Blueprint $table) {
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

        Schema::create('anomaly_snapshots', function (Blueprint $table) {
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

        Schema::create('anomaly_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id');
            $table->foreignId('snapshot_id')->nullable();
            $table->foreignId('user_id');
            $table->enum('status', ['belum_ditangani', 'proses', 'menunggu_konfirmasi', 'selesai']);
            $table->text('catatan')->nullable();
            $table->string('lampiran')->nullable();
            $table->timestamps();
        });

        Schema::create('anomaly_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id');
            $table->string('activity_type', 30);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->date('activity_date');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('alokasi_petugas', function (Blueprint $table) {
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
    }

    public function test_uploading_a_csv_imports_rows_into_anomaly_runs_and_cases(): void
    {
        $type = AnomalyType::create([
            'kode' => 'NON_RESP',
            'nama' => 'Non Respon',
            'deskripsi' => 'Test anomaly type',
            'kolom_wajib' => ['assignment_id'],
            'kolom_tampil' => ['assignment_id', 'nks'],
            'form_followup' => [],
            'aktif' => true,
        ]);

        $file = tempnam(sys_get_temp_dir(), 'anomaly');
        file_put_contents($file, "assignment_id,nks,id_responden,kode_wilayah\nA-001,NKS-001,RESP-001,W-001\n");

        $uploadedFile = new UploadedFile($file, 'anomalies.csv', 'text/csv', null, true);

        $response = $this->post('/anomalies/import', [
            'anomaly_type_id' => $type->id,
            'tanggal_query' => '2026-08-04',
            'file' => $uploadedFile,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('anomaly_runs', 1);
        $this->assertDatabaseCount('anomaly_cases', 1);
        $this->assertDatabaseCount('anomaly_snapshots', 1);
    }
}
