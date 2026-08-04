<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlokasiPetugas extends Model
{
    protected $table = 'alokasi_petugas';

    protected $fillable = [
        'assignment_id', 'kode_wilayah', 'nama_wilayah',
        'ppl_id', 'ppl_nama', 'pml_id', 'pml_nama',
        'taskforce_id', 'taskforce_nama', 'periode',
    ];

    protected $casts = [
        'periode' => 'date',
    ];
}
