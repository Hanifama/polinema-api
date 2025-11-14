<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'lat',
        'lng',
        'order',
    ];

    public $timestamps = false;


    // Relasi ke parent (misalnya: kabupaten punya provinsi)
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    // Relasi ke anak (misalnya: provinsi punya kabupaten/kota)
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }
}
