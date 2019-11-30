<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Instansi;
use App\Admin\Models\Kelompok;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function instansi(Request $request)
    {
        $kelompokId = $request->get('q');
        if (!is_null(Kelompok::find($kelompokId))) {
            return Kelompok::find($kelompokId)->instansi()->get(['instansi.id', DB::raw('nama as text')]);
        } else {
            return [];
        }
    }

    public function layanan(Request $request)
    {
        $instansiId = $request->get('q');
        if (!is_null(Instansi::find($instansiId))) {
            return Instansi::find($instansiId)->layanan()->get(['layanan.id', DB::raw('nama as text')]);
        } else {
            return [];
        }
    }
}
