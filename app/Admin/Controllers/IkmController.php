<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Klasifikasi;
use App\Http\Controllers\Controller;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class IkmController extends Controller
{
    public function index(Content $content)
    {
        $content->header('IKM')
            ->description('Indeks Kepuasan Masyarakat');
        $content->body((new Widgets\Box('Indeks Kepuasan Masyarakat Kota Banjarbaru', $this->indexTable()))->style('success')->collapsable());
        return $content;
    }

    public function layanan(Content $content)
    {
        $content->header('IKM')
            ->description('Indeks Kepuasan Masyarakat berdasarkan Unit Layanan');
        $content->body((new Widgets\Box('Indeks Kepuasan Masyarakat per Unit Layanan', $this->layananTable()))->style('success')->collapsable());
        return $content;
    }

    public function kelompok(Content $content)
    {
        $content->header('IKM')
            ->description('Indeks Kepuasan Masyarakat berdasarkan Kelompok');
        $content->body((new Widgets\Box('Indeks Kepuasan Masyarakat per Kelompok', $this->kelompokTable()))->style('success')->collapsable());
        return $content;
    }

    private function indexTable()
    {
        $header = $this->header();
        $arrTable=DB::table('sampel')
        ->selectRaw($this->select())
        ->get()->toArray();
        $tabel = new Table($header, $this->columns($arrTable, true));

        return $tabel;
    }

    private function layananTable()
    {
        $header = $this->header('Unit Layanan');
        $arrTable=DB::table('sampel')
        ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
        ->selectRaw($this->select('layanan.nama, '))
        ->groupBy('layanan.nama', 'layanan_id')
        ->orderBy('IKM', 'desc')
        ->get()->toArray();
        $tabel = new Table($header, $this->columns($arrTable));

        return $tabel;
    }

    private function kelompokTable()
    {
        $header = $this->header('Kelompok');
        $arrTable=DB::table('sampel')
        ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
        ->join('instansi', 'layanan.instansi_id', '=', 'instansi.id')
        ->join('instansi_kelompok', 'instansi_kelompok.instansi_id', '=', 'instansi.id')
        ->join('kelompok', 'kelompok.id', '=', 'instansi_kelompok.kelompok_id')
        ->selectRaw($this->select('kelompok.nama, '))
        ->whereRaw('sampel.layanan_id IN(
            SELECT layanan.id FROM layanan WHERE layanan.instansi_id IN (
            SELECT instansi_id FROM instansi_kelompok
            ))')
        ->groupBy('kelompok.nama', 'kelompok.id')
        ->orderBy('IKM', 'desc')
        ->get()->toArray();
        $tabel = new Table($header, $this->columns($arrTable));

        return $tabel;
    }


    private function select($pre = null)
    {
        return $pre.'count(sampel.id), sum(u1), sum(u2), sum(u3), sum(u4), sum(u5), sum(u6), sum(u7), sum(u8), sum(u9),
        ROUND((sum(u1+u2+u3+u4+u5+u6+u7+u8+u9))/(9*count(sampel.id)),2) as NRRT,
        ROUND(25*(sum(u1+u2+u3+u4+u5+u6+u7+u8+u9))/(9*count(sampel.id)),2) as IKM';
    }

    private function header($rinc = 'Kabupaten')
    {
        return [$rinc, 'Jumlah Sampel', 'U1', 'U2', 'U3', 'U4', 'U5', 'U6', 'U7', 'U8', 'U9', 'NRRT', 'Index','Kategori'];
    }


    private function columns(array $arrTable, bool $prepend = false)
    {
        $arrTable=json_decode(json_encode($arrTable, true), true);
        foreach ($arrTable as $key) {
            if ($prepend) {
                $key = Arr::prepend($key, '<a href="ikm/kelompok">Antah Berantah</a>', 'rincian');
            }
            $key = Arr::add($key, 'klasifikasi', Klasifikasi::where('batas', '<=', $key['IKM'])->orderBy('batas', 'desc')->first('klasifikasi')->klasifikasi);
            $arrTable2[] =  $key;
        }
        return $arrTable2;
    }
}
