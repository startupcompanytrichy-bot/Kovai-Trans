<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $fillable = ['name', 'display_name', 'icon', 'route', 'order', 'is_active'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_menus');
    }
}
