<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    protected $table = 'kelompok';
    public function instansi()
    {
        return $this->belongsToMany(Instansi::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $model->instansi()->detach();
        });
    }
}
