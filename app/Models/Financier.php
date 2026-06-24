<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Financier extends Model
{
    protected $table = 'financiers';

    protected $fillable = [
        'name',
        'account_number',
    ];
}
