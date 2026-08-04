<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnomalyFollowup extends Model
{
    protected $fillable = [
        'case_id', 'snapshot_id', 'user_id', 'status', 'catatan', 'lampiran',
    ];

    public function anomalyCase()
    {
        return $this->belongsTo(AnomalyCase::class, 'case_id');
    }

    public function snapshot()
    {
        return $this->belongsTo(AnomalySnapshot::class, 'snapshot_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
