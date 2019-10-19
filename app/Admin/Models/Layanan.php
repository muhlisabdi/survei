<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $fillable = ['nama'];
    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }
    public function sampel()
    {
        return $this->hasMany(Sampel::class);
    }
}
