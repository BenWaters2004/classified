<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningCheckFormRequirement extends Model
{
    protected $table = 'screening_check_form_requirements';

    protected $fillable = [
        'check_type_id',
        'form_key',
        'required',
        'sort_order',
    ];

    protected $casts = [
        'check_type_id' => 'integer',
        'required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function checkType()
    {
        return $this->belongsTo(
            ScreeningCheckType::class,
            'check_type_id'
        );
    }
}