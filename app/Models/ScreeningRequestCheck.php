<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningRequestCheck extends Model
{
    protected $table = 'screening_request_checks';

    protected $fillable = [
        'screening_request_id',
        'check_type_id',
        'status',
        'provider_reference',
        'provider_record_type',
        'provider_record_id',
        'metadata_json',
        'started_at',
        'submitted_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'screening_request_id' => 'integer',
        'check_type_id' => 'integer',
        'provider_record_id' => 'integer',

        'metadata_json' => 'array',

        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(
            ScreeningRequest::class,
            'screening_request_id'
        );
    }

    public function checkType()
    {
        return $this->belongsTo(
            ScreeningCheckType::class,
            'check_type_id'
        );
    }
}