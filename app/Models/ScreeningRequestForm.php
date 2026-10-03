<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningRequestForm extends Model
{
    protected $table = 'screening_request_forms';

    protected $fillable = [
        'screening_request_id',
        'form_key',
        'status',
        'required',
        'sort_order',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'screening_request_id' => 'integer',
        'required' => 'boolean',
        'sort_order' => 'integer',

        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(
            ScreeningRequest::class,
            'screening_request_id'
        );
    }
}