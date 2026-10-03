<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningRequest extends Model
{
    protected $table = 'screening_requests';

    protected $fillable = [
        'applicant_id',
        'user_id',
        'organisation_id',
        'status',
        'created_by',
        'registered_at',
        'submitted_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'applicant_id' => 'integer',
        'user_id' => 'integer',
        'organisation_id' => 'integer',
        'created_by' => 'integer',

        'registered_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function checks()
    {
        return $this->hasMany(
            ScreeningRequestCheck::class,
            'screening_request_id'
        );
    }

    public function forms()
    {
        return $this->hasMany(
            ScreeningRequestForm::class,
            'screening_request_id'
        )->orderBy('sort_order');
    }
}