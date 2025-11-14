<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommunityCategory extends Model
{
    use HasFactory;

    protected $table = 'community_categories';
    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'category_id', 'name', 'photo', 'created_dt',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->category_id)) {
                $category->category_id = 'categ-' . Str::uuid()->toString();
            }
            if (empty($category->created_dt)) {
                $category->created_dt = now();
            }
        });
    }
}
