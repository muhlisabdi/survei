<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Admin\Models\Kelompok;
use App\Admin\Models\Instansi;
use App\Admin\Models\Layanan;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jxlwqq\DataTable\DataTable;
use Illuminate\Support\Carbon;

class DownloadController extends Controller
{
    public function index(Request $request, Content $content)
    {
        $content->title('Cascading select');

        $form = new Form($request->all());
        $form->method('GET');
        $form->action('/'.config('admin.route.prefix').'/download');

        $form->select('kelompok')->options(
            Kelompok::all()->pluck('nama', 'id')
        )->load('instansi', '/'.config('admin.route.prefix').'/api/instansi');

        $form->select('instansi')->options(function ($id) {

            return Instansi::where('id', $id)->pluck('nama', 'id');
        })->load('layanan', '/'.config('admin.route.prefix').'/api/layanan');

        $form->select('layanan')->options(function ($id) {

            return Layanan::where('id', $id)->pluck('nama', 'id');
        });
        $box1 = new Box('Filter', $form);
        $box1->collapsable();
        $content->row($box1);
        if ($request->has('kelompok')) {
            $box2 = new Box('Daftar Sampel', $this->setTable($request->kelompok, $request->instansi, $request->layanan));
            $box2->removable();
            $content->row($box2);
        }

        return $content;
    }

    private function setTable($kelompok = null, $instansi = null, $layanan = null)
    {
        if ($kelompok == null) {
            $table = new DataTable(
                $this->setHeader(),
                $this->allQuery(),
                $this->tableStyle(),
                $this->tableOption('Daftar Semua Sampel'),
                'sampel');
        } elseif ($instansi == null) {
            $table = new DataTable(
                $this->setHeader(),
                $this->kelompokQuery($kelompok),
                $this->tableStyle(),
                $this->tableOption('Daftar Sampel Kelompok '.Kelompok::where('id', $kelompok)->get('nama')[0]->nama),
                'sampel');
        }  elseif ($layanan == null) {
            $table = new DataTable(
                $this->setHeader(),
                $this->instansiQuery($instansi),
                $this->tableStyle(),
                $this->tableOption('Daftar Sampel '.Instansi::where('id', $instansi)->get('nama')[0]->nama),
                'sampel');
            } else {
                $table = new DataTable(
                    $this->setHeader(),
                    $this->layananQuery($layanan),
                    $this->tableStyle(),
                    $this->tableOption('Daftar Sampel Layanan '.Layanan::where('id', $layanan)->get('nama')[0]->nama),
                    'sampel');
                }

        return $table;
    }

    public function instansi(Request $request)
    {
        $kelompokId = $request->get('q');
        if (!is_null(Kelompok::find($kelompokId))){
            return Kelompok::find($kelompokId)->instansi()->get(['instansi.id', DB::raw('nama as text')]);
        } else {
            return [];
        }
    }

    public function layanan(Request $request)
    {
        $instansiId = $request->get('q');
        if (!is_null(Instansi::find($instansiId))){
            return Instansi::find($instansiId)->layanan()->get(['layanan.id', DB::raw('nama as text')]);
        } else {
            return [];
        }
    }

    private function allQuery()
    {
        $data = DB::table('sampel')
            ->selectRaw('sampel.id, sampel.nama, layanan_id, tanggal, jam_id, jk_id, pendidikan_id, pekerjaan_id, umur, u1, u2, u3, u4, u5, u6, u7, u8, u9')
            ->whereRaw('deleted_at IS NULL')
            ->orderBy('sampel.id', 'asc')
            ->get()
            ->toArray();

        return $data;
    }

    private function kelompokQuery($kelompokID)
    {
        $data = DB::table('sampel')
                ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
                ->join('instansi', 'layanan.instansi_id', '=', 'instansi.id')
                ->join('instansi_kelompok', 'instansi_kelompok.instansi_id', '=', 'instansi.id')
                ->join('kelompok', 'kelompok.id', '=', 'instansi_kelompok.kelompok_id')
                ->selectRaw('sampel.id, sampel.nama, layanan_id, tanggal, jam_id, jk_id, pendidikan_id, pekerjaan_id, umur, u1, u2, u3, u4, u5, u6, u7, u8, u9')
                ->whereRaw('deleted_at IS NULL AND sampel.layanan_id IN(
                    SELECT layanan.id FROM layanan WHERE layanan.instansi_id IN (
                    SELECT instansi_id FROM instansi_kelompok WHERE instansi_kelompok.kelompok_id ='.$kelompokID.'))')
                ->orderBy('sampel.id', 'asc')
                ->get()
                ->toArray();

        return $data;
    }

    private function instansiQuery($instansiID)
    {
        $data = DB::table('sampel')
        ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
        ->join('instansi', 'layanan.instansi_id', '=', 'instansi.id')
        ->selectRaw('sampel.id, sampel.nama, layanan_id, tanggal, jam_id, jk_id, pendidikan_id, pekerjaan_id, umur, u1, u2, u3, u4, u5, u6, u7, u8, u9')
        ->whereRaw('deleted_at IS NULL AND layanan.instansi_id  ='.$instansiID)
        ->orderBy('sampel.id', 'asc')
        ->get()
        ->toArray();

        return $data;
    }

    private function layananQuery($layananID)
    {
        $data = DB::table('sampel')
        ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
        ->selectRaw('sampel.id, sampel.nama, layanan_id, tanggal, jam_id, jk_id, pendidikan_id, pekerjaan_id, umur, u1, u2, u3, u4, u5, u6, u7, u8, u9')
        ->whereRaw('deleted_at IS NULL AND sampel.layanan_id  ='.$layananID)
        ->orderBy('sampel.id', 'asc')
        ->get()
        ->toArray();

        return $data;
    }

    private function setFooter()
    {
        return 'Dicetak pada '.Carbon::parse(now())->translatedFormat('d F Y (H:i:s)');
    }

    private function setHeader()
    {
        return ['Kode', 'Nama', 'ID Layanan', 'Tanggal', 'Jam', 'JK', 'Pendidikan', 'Pekerjaan', 'Umur',
        'U1', 'U2', 'U3', 'U4', 'U5', 'U6', 'U7', 'U8', 'U9'];
    }

    private function tableOption($title= '')
    {
        return [
            'buttons'=> [
                [
                    'extend'        => 'excelHtml5',
                    'title'         => $title,
                    'messageBottom' => $this->setFooter(),
                ],                [
                    'extend'        => 'print',
                    'title'         => $title,
                    'messageBottom' => $this->setFooter(),
                ],
            ],
        ];
    }

    private function tableStyle()
    {
        return ['table-bordered', 'table-hover', 'table-striped'];
    }

}
