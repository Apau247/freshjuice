<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $primaryKey = 'StaffID';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'StaffID', 'UserID', 'FirstName', 'LastName', 'Email',
        'Phone', 'Department', 'Position', 'DateHired', 'Status',
    ];
}
