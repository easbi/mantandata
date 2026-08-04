<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnomalyActivity extends Model
{
    protected $fillable = [
        'case_id', 'activity_type', 'reference_id', 'activity_date', 'payload',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'payload' => 'array',
    ];

    public function anomalyCase()
    {
        return $this->belongsTo(AnomalyCase::class, 'case_id');
    }
}
