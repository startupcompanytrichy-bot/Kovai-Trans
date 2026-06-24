<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $table = 'login';

    protected $fillable = [
        'company_id',
        'branch_id',
        'company',
        'branch',
        'role',
        'email',
        'mobile',
        'password',
        'status',
        'is_deleted',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    /**
     * Scope a query to only include active (non-deleted) records.
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    protected $hidden = [
        'password',
    ];

    /**
     * Get the User model associated with this login
     */
    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    /**
     * Get permissions through the User model
     */
    public function getPermissionsAttribute()
    {
        $user = $this->user;
        return $user ? $user->permissions : collect();
    }
}
