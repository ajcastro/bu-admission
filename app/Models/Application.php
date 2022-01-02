<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
