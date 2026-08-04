<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AnomalyCase extends Model
{
    protected $fillable = [
        'anomaly_key', 'anomaly_type_id', 'assignment_id', 'nks', 'id_responden',
        'kode_wilayah', 'status_penanganan', 'first_run_id', 'latest_run_id',
        'first_seen_at', 'last_seen_at', 'times_seen',
    ];

    protected $casts = [
        'first_seen_at' => 'date',
        'last_seen_at' => 'date',
    ];

    public function anomalyType()
    {
        return $this->belongsTo(AnomalyType::class);
    }

    public function firstRun()
    {
        return $this->belongsTo(AnomalyRun::class, 'first_run_id');
    }

    public function latestRun()
    {
        return $this->belongsTo(AnomalyRun::class, 'latest_run_id');
    }

    public function snapshots()
    {
        return $this->hasMany(AnomalySnapshot::class, 'case_id')->orderBy('created_at');
    }

    public function followups()
    {
        return $this->hasMany(AnomalyFollowup::class, 'case_id')->orderBy('created_at');
    }

    public function activities()
    {
        return $this->hasMany(AnomalyActivity::class, 'case_id')->orderBy('activity_date');
    }

    /**
     * Scope: hanya case yang muncul pada run terbaru untuk jenis anomalinya (daftar aktif default).
     * Menggantikan pendekatan field boolean "visible_on_latest_run".
     */
    public function scopeActiveOnLatestRun(Builder $query): Builder
    {
        return $query->whereColumn('latest_run_id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('anomaly_runs')
                ->whereColumn('anomaly_type_id', 'anomaly_cases.anomaly_type_id');
        });
    }

    /** Scope: case yang sempat hilang dari daftar aktif (hide), tapi masih ada di database. */
    public function scopeHiddenFromLatestRun(Builder $query): Builder
    {
        return $query->whereColumn('latest_run_id', '<', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('anomaly_runs')
                ->whereColumn('anomaly_type_id', 'anomaly_cases.anomaly_type_id');
        });
    }

    /** Apakah case ini pernah hilang lalu muncul kembali (reopen)? */
    public function getPernahReopenAttribute(): bool
    {
        return $this->times_seen > 1
            && $this->activities()->where('activity_type', 'IMPORT_REOPEN')->exists();
    }

    public function isActiveOnLatestRun(): bool
    {
        $maxRunId = AnomalyRun::where('anomaly_type_id', $this->anomaly_type_id)->max('id');

        return $this->latest_run_id == $maxRunId;
    }
}
