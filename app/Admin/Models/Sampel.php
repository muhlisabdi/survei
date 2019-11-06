<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sampel extends Model
{
    use SoftDeletes;

    protected $table = 'sampel';

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}
