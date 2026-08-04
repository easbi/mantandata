<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnomalyRun extends Model
{
    protected $fillable = [
        'anomaly_type_id', 'tanggal_query', 'nama_file', 'jumlah_data',
        'jumlah_case_baru', 'jumlah_case_lama', 'jumlah_error', 'created_by',
    ];

    protected $casts = [
        'tanggal_query' => 'date',
    ];

    public function anomalyType()
    {
        return $this->belongsTo(AnomalyType::class);
    }

    public function snapshots()
    {
        return $this->hasMany(AnomalySnapshot::class, 'run_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
