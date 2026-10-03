<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationScreeningCheck extends Model
{
    protected $table = 'organisation_screening_checks';

    protected $fillable = [
        'organisation_id',
        'check_type_id',
        'enabled',
        'config_json',
    ];

    protected $casts = [
        'organisation_id' => 'integer',
        'check_type_id' => 'integer',
        'enabled' => 'boolean',
        'config_json' => 'array',
    ];

    public function checkType()
    {
        return $this->belongsTo(
            ScreeningCheckType::class,
            'check_type_id'
        );
    }
}