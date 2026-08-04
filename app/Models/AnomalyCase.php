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
        'anomaly_type_id' => 'integer',
        'first_run_id' => 'integer',
        'latest_run_id' => 'integer',
        'first_seen_at' => 'date',
        'last_seen_at' => 'date',
        'times_seen' => 'integer',
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
     * Scope: Kasus yang muncul pada run terbaru untuk jenis anomalinya (daftar aktif default).
     */
    public function scopeActive(Builder $query, $anomalyTypeId = null): Builder
    {
        if ($anomalyTypeId !== null) {
            $query->where('anomaly_type_id', $anomalyTypeId);
        }

        return $query->whereRaw('latest_run_id = (SELECT MAX(id) FROM anomaly_runs WHERE anomaly_type_id = anomaly_cases.anomaly_type_id)');
    }

    /** Scope: case yang sempat hilang dari daftar aktif (hide), tapi masih ada di database. */
    public function scopeHidden(Builder $query, $anomalyTypeId = null): Builder
    {
        if ($anomalyTypeId !== null) {
            $query->where('anomaly_type_id', $anomalyTypeId);
        }

        return $query->whereRaw('latest_run_id < (SELECT MAX(id) FROM anomaly_runs WHERE anomaly_type_id = anomaly_cases.anomaly_type_id)');
    }

    public function scopeActiveOnLatestRun(Builder $query): Builder
    {
        return $query->active();
    }

    public function scopeHiddenFromLatestRun(Builder $query): Builder
    {
        return $query->hidden();
    }

    public function scopeRecurring(Builder $query): Builder
    {
        return $query->where('times_seen', '>', 1)
            ->whereHas('activities', function (Builder $subQuery): void {
                $subQuery->where('activity_type', 'IMPORT_REOPEN');
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
