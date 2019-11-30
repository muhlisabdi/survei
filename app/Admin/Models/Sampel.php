<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sampel extends Model
{
    use SoftDeletes;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $table = 'sampel';

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function instansi()
    {
        return $this->belongsToThrough(Instansi::class, Layanan::class);
    }
}
