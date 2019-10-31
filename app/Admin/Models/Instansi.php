<?php

namespace App\Admin\Models;

use Encore\Admin\Auth\Database\Administrator;
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

    public function sampel()
    {
        return $this->hasManyThrough(Sampel::class, Layanan::class);
    }

    public function token()
    {
        return $this->hasManyThrough(Token::class, Layanan::class);
    }

    public function user()
    {
        return $this->hasMany(Administrator::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $model->sampel()->delete();
            $model->user()->delete();
            $model->token()->delete();
            $model->layanan()->delete();
            $model->kelompok()->detach();
        });
    }
}
