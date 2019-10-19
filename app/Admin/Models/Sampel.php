<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sampel extends Model
{
    use SoftDeletes;

    protected $table = 'sampel';
    public function jk()
    {
        return $this->hasOne(Jk::class);
    }
    public function pendidikan()
    {
        return $this->hasOne(Pendidikan::class);
    }
    public function pekerjaan()
    {
        return $this->hasOne(Pekerjaan::class);
    }
    public function jam()
    {
        return $this->hasOne(Jam::class);
    }
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }


}
