<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    
    protected $primaryKey = 'department_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'department_id',
        'name',
        'created_dt',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class, 'department_id', 'department_id');
    }
}
