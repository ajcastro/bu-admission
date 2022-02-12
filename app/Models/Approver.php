<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApproverAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Approver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'application_id',
        'user_id',
        'action',
        'remarks',
        'approved_at',
        'rejected_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'application_id' => 'integer',
        'user_id' => 'integer',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionDisplayAttribute()
    {
        return Str::studly($this->action);
    }

    public function scopeNoActionYet($query)
    {
        $query->where(function ($query) {
            $query->whereNull('approved_at');
            $query->whereNull('rejected_at');
        });
    }

    public function scopeHadActionTaken($query)
    {
        $query->where(function ($query) {
            $query->whereNotNull('approved_at');
            $query->orWhereNotNull('rejected_at');
        });
    }

    public function getApplicationStatus()
    {
        return ([
            ApproverAction::RECOMMEND => ApplicationStatus::RECOMMENDED,
            ApproverAction::ADMIT => ApplicationStatus::ADMITTED,
            ApproverAction::PROCESS => ApplicationStatus::PROCESSED,
        ])[$this->action] ?? null;
    }
}
