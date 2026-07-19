<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID', 'RoleID');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'UserID', 'UserID');
    }
}
