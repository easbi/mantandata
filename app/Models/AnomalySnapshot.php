<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnomalySnapshot extends Model
{
    protected $fillable = [
        'run_id', 'case_id', 'assignment_id', 'data_query',
        'ppl_id', 'ppl_nama', 'pml_id', 'pml_nama', 'taskforce_id', 'taskforce_nama',
    ];

    protected $casts = [
        'data_query' => 'array',
    ];

    public function run()
    {
        return $this->belongsTo(AnomalyRun::class, 'run_id');
    }

    public function anomalyCase()
    {
        return $this->belongsTo(AnomalyCase::class, 'case_id');
    }

    public function followups()
    {
        return $this->hasMany(AnomalyFollowup::class, 'snapshot_id');
    }
}
