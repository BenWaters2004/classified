<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Applicant extends Authenticatable
{
    use Notifiable;

    protected $table = 'applicants';

    protected $primaryKey = 'id';




}
