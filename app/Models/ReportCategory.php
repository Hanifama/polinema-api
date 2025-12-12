<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCategory extends Model
{
    protected $table = 'report_categories';
    public $timestamps = false;

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'created_dt',
        'updated_dt',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code) && !empty($model->name)) {
                $model->code = $model->generateCode($model->name);
            }
        });
    }

    private function generateCode($name)
    {
        return strtoupper(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9 ]/', '', $name)));
    }

    public function reports()
    {
        return $this->hasMany(UserReport::class, 'category_code', 'code');
    }
}
