<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'token';

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}
