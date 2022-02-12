<?php

namespace App\Models;

use App\Enums\ApproverAction;
use App\Models\Traits\ImportsFromJson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    use ImportsFromJson;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'label',
        'recommending_user_id',
        'admitting_user_id',
        'processing_user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function recommendingUser()
    {
        return $this->belongsTo(User::class);
    }

    public function admittingUser()
    {
        return $this->belongsTo(User::class);
    }

    public function processingUser()
    {
        return $this->belongsTo(User::class);
    }

    public function getRecommendingUser()
    {
        return $this->recommendingUser;
    }

    public function getAdmittingUser()
    {
        return $this->admittingUser;
    }

    public function getProcessingUser()
    {
        return $this->processingUser;
    }

    public function makeApprovers()
    {
        return collect([
            [
                'action' => ApproverAction::RECOMMEND,
                'user_id' => $this->getRecommendingUser()->id,
            ],
            [
                'action' => ApproverAction::ADMIT,
                'user_id' => $this->getAdmittingUser()->id,
            ],
            [
                'action' => ApproverAction::PROCESS,
                'user_id' => $this->getProcessingUser()->id,
            ],
        ])->map(fn($item) => new Approver($item));
    }
}
