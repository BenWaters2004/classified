<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningCheckType extends Model
{
    protected $table = 'screening_check_types';

    protected $fillable = [
        'code',
        'name',
        'provider',
        'category',
        'description',
        'active',
        'sort_order',
        'config_json',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
        'config_json' => 'array',
    ];

    public function organisationSettings()
    {
        return $this->hasMany(
            OrganisationScreeningCheck::class,
            'check_type_id'
        );
    }

    public function requestChecks()
    {
        return $this->hasMany(
            ScreeningRequestCheck::class,
            'check_type_id'
        );
    }

    public function formRequirements()
    {
        return $this->hasMany(
            ScreeningCheckFormRequirement::class,
            'check_type_id'
        )->orderBy('sort_order');
    }
}