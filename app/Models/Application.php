<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Jobs\CreateApplicationApprovers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Application extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditingAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'program_id',
        'term_id',
        'last_name',
        'first_name',
        'middle_name',
        'birthdate',
        'gender',
        'email',
        'mobile_number',
        'phone_number',
        'work_number',
        'company',
        'residence_address_line_1',
        'residence_address_line_2',
        'residence_municipality',
        'residence_province',
        'residence_zip_code',
        'residence_country',
        'same_with_residence_address',
        'permanent_address_line_1',
        'permanent_address_line_2',
        'permanent_municipality',
        'permanent_province',
        'permanent_zip_code',
        'permanent_country',
        'status',
        'total_units',
        'requirements',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'birthdate' => 'date',
        'same_with_residence_address' => 'boolean',
        'total_units' => 'decimal:2',
        'requirements' => 'array',
    ];

    public static function booted()
    {
        static::creating(function (Application $application) {
            $application->user_id = $application->user_id ?? auth()->user()->id ?? null;
            $application->term_id = Term::getActive()->id;
        });

        static::created(function (Application $application) {
            dispatch(new CreateApplicationApprovers($application));
        });

        static::saving(function (Application $application) {
            $application->total_units = $application->getTotalUnits();
        });
    }

    public static function getFeesTabulation(?Application $application)
    {
        return [
            'Unit Fees' => optional($application)->getTotalUnits() * 450,
            'Miscellaneous Fee' => 100,
            'DLF' => 1500,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class)
            ->withPivot(['units']);
    }

    public function approvers()
    {
        return $this->hasMany(Approver::class);
    }

    public function getApplicantNameAttribute()
    {
        if (blank($this->middle_name)) {
            return "{$this->first_name} {$this->last_name}";
        }

        return "{$this->first_name} {$this->middle_initial}. {$this->last_name}";
    }

    public function getMiddleInitialAttribute()
    {
        return $this->middle_name ? $this->middle_name[0] : '';
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case ApplicationStatus::PENDING:
                return 'orange';

            case ApplicationStatus::REJECTED:
                return 'red';

            case ApplicationStatus::RECOMMENDED:
            case ApplicationStatus::ADMITTED:
            case ApplicationStatus::PROCESSED:
                return 'green';

            default:
                return 'default';
        }
    }


    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->isAdministrator()) {
            return;
        }

        if ($user->role === UserRole::Applicant) {
            return $query->where('user_id', $user->id);
        }

        if (in_array($user->role, [
            UserRole::ProgramAdviser,
            UserRole::Dean,
            UserRole::Registrar,
        ])) {
            return $query->whereHas('approvers', function ($query) use ($user) {
                $query->where('approvers.user_id', $user->id);
            });
        }
    }

    public function getTotalUnits()
    {
        return $this->subjects()->sum('application_subject.units');
    }

    public function getCurrentApprover(): ?Approver
    {
        return $this->approvers()
            ->noActionYet()
            ->first();
    }

    public function getLastApprover(): ?Approver
    {
        return $this->approvers()
            ->hadActionTaken()
            ->orderByRaw('IFNULL(approved_at, rejected_at) desc')
            ->first();
    }

    public function findApprover(User $user): Approver
    {
        return $this->approvers()->where('user_id', $user->id)->first();
    }

    public function findPreviousApprover(): ?Approver
    {
        return $this->approvers()
            ->hadActionTaken()
            ->orderByRaw('IFNULL(approved_at, rejected_at) desc')
            ->first();
    }

    public function approve(Approver $approver)
    {
        if ($approver->user->cannot('approve', $this)) {
            abort(403);
        }

        $approver->approved_at = now();
        $approver->save();
        $this->status = $approver->getApplicationStatus();
        $this->save();
    }

    public function reject(Approver $approver)
    {
        if ($approver->user->cannot('approve', $this)) {
            abort(403);
        }

        $approver->rejected_at = now();
        $approver->save();
        $this->status = ApplicationStatus::REJECTED;
        $this->save();
    }

    public function undoApproval(Approver $approver)
    {
        if ($approver->user->cannot('undoApproval', $this)) {
            abort(403);
        }

        $approver->approved_at = null;
        $approver->rejected_at = null;
        $approver->save();

        $prevApprover = $this->findPreviousApprover();

        $this->status = is_null($prevApprover)
            ? ApplicationStatus::PENDING
            : $prevApprover->getApplicationStatus();

        $this->save();
    }
}
