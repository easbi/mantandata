<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnomalyType extends Model
{
    protected $fillable = [
        'kode', 'nama', 'deskripsi', 'kolom_wajib', 'kolom_tampil',
        'form_followup', 'warna', 'ikon', 'aktif',
    ];

    protected $casts = [
        'kolom_wajib' => 'array',
        'kolom_tampil' => 'array',
        'form_followup' => 'array',
        'aktif' => 'boolean',
    ];

    public function runs()
    {
        return $this->hasMany(AnomalyRun::class);
    }

    public function cases()
    {
        return $this->hasMany(AnomalyCase::class);
    }

    /** Run terbaru untuk jenis anomali ini — dipakai untuk cek "visible on latest run". */
    public function latestRun()
    {
        return $this->hasOne(AnomalyRun::class)->latestOfMany();
    }
}
