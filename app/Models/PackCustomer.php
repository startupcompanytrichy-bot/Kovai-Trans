<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackCustomer extends Model
{
    protected $table = 'pack_customers';

    protected $fillable = [
        'name', 'phone', 'email', 'gstin', 'address', 'notes', 'created_by',
    ];
}
