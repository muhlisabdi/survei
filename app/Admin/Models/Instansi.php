<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Instansi extends Model
{
    protected $table = 'instansi';

    public function kelompok(): BelongsToMany
    {
        return $this->belongsToMany(Kelompok::class);
    }

    public function layanan()
    {
        return $this->hasMany(Layanan::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $model->layanan()->delete();
            $model->kelompok()->detach();
        });
    }
}
