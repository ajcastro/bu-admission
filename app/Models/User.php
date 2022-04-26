<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApproverAction;
use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Traits\ImportsFromJson;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function booted()
    {
        static::saving(function (User $user) {
            $user->name = $user->name ?: $user->getFullNameAttribute();
        });
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function isAdministrator()
    {
        return $this->role === UserRole::Admin;
    }

    public function getApproverAction()
    {
        return ([
            UserRole::ProgramAdviser => ApproverAction::RECOMMEND,
            UserRole::Dean => ApproverAction::ADMIT,
            UserRole::Registrar => ApproverAction::PROCESS,
        ])[$this->role] ?? null;
    }

    public function getFullNameAttribute()
    {
        if ($this->first_name || $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
    }

    public function getDefaultApplicationStatus()
    {
        if (in_array($this->role, [
            UserRole::ProgramAdviser,
        ])) {
            return [ApplicationStatus::PENDING];
        }

        if (in_array($this->role, [
            UserRole::Dean,
        ])) {
            return [ApplicationStatus::RECOMMENDED];
        }

        if (in_array($this->role, [
            UserRole::Registrar,
        ])) {
            return [ApplicationStatus::ADMITTED];
        }

        return [];
    }

    public function getAdvisingPrograms($columns = ['*'])
    {
        return Program::where('recommending_user_id', $this->id)->get($columns);
    }
}
