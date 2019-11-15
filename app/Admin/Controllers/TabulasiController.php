<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Instansi;
use App\Admin\Models\Kelompok;
use App\Admin\Models\Klasifikasi;
use App\Admin\Models\Layanan;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Jxlwqq\DataTable\DataTable;

class TabulasiController extends Controller
{
    public function main(Content $content, $table ='all', $group = '', $id = null)
    {
        return $content->header('Tabulasi')
        ->description('Tabulasi Sampel')
        ->row($this->generateBox($table, 'sampel', 'main', 'sampel', $group, $id))
        ->row($this->generateBox($table, 'ikm', 'main', 'ikm', $group, $id));
    }

    public function detail(Content $content, $table ='Jk', $group = '', $id = null)
    {
        return $content->header('Tabulasi')
        ->description('Tabulasi Sampel')
        ->row($this->generateBox($table, 'sampel', 'detail', 'sampel', $group, $id))
        ->row($this->generateBox($table, 'ikm', 'detail', 'ikm', $group, $id));
    }

    private function generateBox($tabel, $dataTableID, $type, $display, $group='', $id = null)
    {
        $box = new Widgets\Box(
            $this->setTitle($type, $display, $tabel, $group, $id),
            $this->generateTable($group, $tabel, $dataTableID, $type, $display, $id),
            $this->setFooter()
        );
        $box->collapsable()->removable();

        return $box;
    }

    /**
     * generateTableDetail.
     *
     * @param mixed $group kelompok|instansi|layanan
     * @param mixed $tabel Jk|Pendidikan|Pekerjaan|Jam
     * @param mixed $id
     *
     * @return void
     */
    private function generateTable($group, $tabel, $dataTableID, $type, $display, $id = null)
    {
        $judul = $this->tableOption($this->setTitle($type, $display, $tabel, $group, $id, false));
        switch ($group) {
            case 'kelompok':
                if (is_null($id)) {
                    if ($type == 'main') {
                        $data = $this->headerAndQueryMain('Kelompok', $group, $display);
                    } else {
                        $data = $this->headerAndQueryDetail($tabel, 'Kelompok', $group, $display);
                    }
                    $arrTable = $this->kelompokQuery($data[0], null)->groupBy('kelompok.nama', 'kelompok.id')
                                ->orderBy('kelompok.id', 'asc')
                                ->get()->toArray();
                    $header = $data[1];
                    $tabel = new DataTable($header, $this->generateRows($type, $tabel, $arrTable, $group, $display), $this->tableStyle(), $judul, $dataTableID);
                } else {
                    if ($type == 'main') {
                        $data = $this->headerAndQueryMain('Instansi', 'instansi', $display);
                    } else {
                        $data = $this->headerAndQueryDetail($tabel, 'Instansi', 'instansi', $display);
                    }
                    $arrTable = $this->kelompokQuery($data[0], $id)->groupBy('instansi.nama', 'instansi.id')
                                ->orderBy('instansi.id', 'asc')
                                ->get()->toArray();
                    $header = $data[1];
                    $tabel = new DataTable($header, $this->generateRows($type, $tabel, $arrTable, 'instansi', $display), $this->tableStyle(), $judul, $dataTableID);
                }
                break;

            case 'instansi':
            if (is_null($id)) {
                if ($type == 'main') {
                    $data = $this->headerAndQueryMain('Instansi', $group, $display);
                } else {
                    $data = $this->headerAndQueryDetail($tabel, 'Instansi', $group, $display);
                }
                $arrTable = $this->instansiQuery($data[0], null)->groupBy('instansi.nama', 'instansi.id')
                            ->orderBy('instansi.id', 'asc')
                            ->get()->toArray();
                $header = $data[1];
                $tabel = new DataTable($header, $this->generateRows($type, $tabel, $arrTable, $group, $display), $this->tableStyle(), $judul, $dataTableID);
            } else {
                if ($type == 'main') {
                    $data = $this->headerAndQueryMain('Layanan', 'layanan', $display);
                } else {
                    $data = $this->headerAndQueryDetail($tabel, 'Layanan', 'layanan', $display);
                }
                $arrTable = $this->instansiQuery($data[0], $id)->groupBy('layanan.nama', 'layanan.id')
                            ->orderBy('layanan.id', 'asc')
                            ->get()->toArray();
                $header = $data[1];
                $tabel = new DataTable($header, $this->generateRows($type, $tabel, $arrTable, 'layanan', $display), $this->tableStyle(), $judul, $dataTableID);
            }
                break;

            case 'layanan':
            if (is_null($id)) {
                if ($type == 'main') {
                    $data = $this->headerAndQueryMain('Layanan', $group, $display);
                } else {
                    $data = $this->headerAndQueryDetail($tabel, 'Layanan', $group, $display);
                }
                $arrTable = $this->layananQuery($data[0], null)->groupBy('layanan.nama', 'layanan.id')
                            ->orderBy('layanan.id', 'asc')
                            ->get()->toArray();
                $header = $data[1];
                $tabel = new DataTable($header, $this->generateRows($type, $tabel, $arrTable, $group, $display), $this->tableStyle(), $judul, $dataTableID);
            } else {
                if ($type == 'main') {
                    $data = $this->headerAndQueryMain('Nama', 'sampel', $display);
                } else {
                    $data = $this->headerAndQueryDetail($tabel, 'Nama', 'sampel', $display);
                }
                $arrTable = $this->layananQuery($data[0], $id)->groupBy('sampel.nama', 'sampel.id')
                            ->orderBy('sampel.id', 'asc')
                            ->get()->toArray();
                $header = $data[1];
                $tabel = new DataTable($header, $this->generateRows($type, $tabel, $arrTable, 'sampel', $display), $this->tableStyle(), $judul, $dataTableID);
            }
                break;

            default:
                if ($type == 'main') {
                    $data = $this->headerAndQueryMain('Kabupaten', $group, $display);
                } else {
                    $data = $this->headerAndQueryDetail($tabel, 'Kabupaten', $group, $display);
                }
                $arrTable = $this->layananQuery($data[0], null)
                            ->get()->toArray();
                $header = $data[1];
                $tabel = new DataTable($header, $this->generateRows($type, $tabel, $arrTable, $group, $display, 'Kabupaten'), $this->tableStyle(), $judul, $dataTableID);

                break;

        }

        return $tabel;
    }

    /**
     * headerAndQueryDetail
     * Generate Header and Query Detail.
     *
     * @param mixed $pre       kolom database pertama
     * @param mixed $model     Jk|Jam|Pendidikan|Pekerjaan
     * @param mixed $display   sampel|ikm
     * @param mixed $preheader untuk header kolom Kabupaten
     *
     * @return void
     */
    private function headerAndQueryDetail($model, $preheader, $group, $display):array
    {
        $q = '';
        if ($display == 'ikm' && $group !== 'sampel') {
            $header = [$preheader, 'Jumlah Sampel'];
        } else {
            $header = [$preheader];
        }

        $arr = App("App\Admin\Models\\{$model}")::all(['kode', 'keterangan'])->pluck('keterangan', 'kode');
        if ($group !== 'sampel') {
            foreach ($arr as $key => $value) {
                if ($display == 'ikm') {
                    $q .= 'count(sampel.id) as jumlah, AVG(CASE WHEN '.$model.'_id='.$key.' THEN u1+u2+u3+u4+u5+u6+u7+u8+u9 END)/9,';
                } else {
                    $q .= 'SUM(IF(sampel.'.$model.'_id='.$key.',1,0)),';
                }
                $header[] = $value;
            }
            unset($arr);
        }
        if ($display == 'ikm') {
            $q .= 'AVG(u1+u2+u3+u4+u5+u6+u7+u8+u9)/9 AS nrrt, ';
            $q .= '25*AVG(u1+u2+u3+u4+u5+u6+u7+u8+u9)/9 AS ikm';
        } else {
            $q .= 'COUNT(sampel.id) AS total';
        }
        if ($display == 'ikm') {
            $header[] = 'NRRT';
            $header[] = 'IKM';
            $header[] = 'Kategori';
        } else {
            $header[] = 'Total';
        }
        if ($group == '') {
            return [$q, $header];
        } else {
            return [$group.'.id AS link, '.$group.'.nama as firstcol,'.$q, $header];
        }
    }

    private function headerAndQueryMain($preheader, $group, $display):array
    {
        if ($display == 'sampel') {
            $header = [
                $preheader,
                'Jumlah Sampel',
                '&Sigma;U1',
                '&Sigma;U2',
                '&Sigma;U3',
                '&Sigma;U4',
                '&Sigma;U5',
                '&Sigma;U6',
                '&Sigma;U7',
                '&Sigma;U8',
                '&Sigma;U9',
            ];
            $q = 'count(sampel.id), sum(u1), sum(u2), sum(u3), sum(u4), sum(u5), sum(u6), sum(u7), sum(u8), sum(u9)';
        } else {
            $header = [
                $preheader,
                'Jumlah Sampel',
                'U1',
                'U2',
                'U3',
                'U4',
                'U5',
                'U6',
                'U7',
                'U8',
                'U9',
                'NRRT',
                'IKM',
                'Kategori',
            ];
            $q = 'count(sampel.id) as jumlah, avg(u1), avg(u2), avg(u3), avg(u4), avg(u5), avg(u6), avg(u7), avg(u8), avg(u9),
            (sum(u1+u2+u3+u4+u5+u6+u7+u8+u9))/(9*count(sampel.id)) as nrrt,
            25*(sum(u1+u2+u3+u4+u5+u6+u7+u8+u9))/(9*count(sampel.id)) as ikm';
        }
        if ($group == '') {
            return [$q, $header];
        } else {
            return [$group.'.id as link, '.$group.'.nama as firstcol,'.$q, $header];
        }
    }

    /**
     * kelompokQuery.
     *
     * @param mixed $qselect Raw Select Query
     * @param mixed $id
     *
     * @return void
     */
    private function kelompokQuery($qselect, $id = null)
    {
        $data = DB::table('sampel')
                ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
                ->join('instansi', 'layanan.instansi_id', '=', 'instansi.id')
                ->join('instansi_kelompok', 'instansi_kelompok.instansi_id', '=', 'instansi.id')
                ->join('kelompok', 'kelompok.id', '=', 'instansi_kelompok.kelompok_id')
                ->selectRaw($qselect);
        if (is_null($id)) {
            $data->whereRaw('deleted_at IS NULL AND sampel.layanan_id IN(
                SELECT layanan.id FROM layanan WHERE layanan.instansi_id IN (
                SELECT instansi_id FROM instansi_kelompok))');
        } else {
            $data->whereRaw('deleted_at IS NULL AND sampel.layanan_id IN(
                SELECT layanan.id FROM layanan WHERE layanan.instansi_id IN (
                SELECT instansi_id FROM instansi_kelompok WHERE instansi_kelompok.kelompok_id ='.$id.'))');
        }

        return $data;
    }

    /**
     * instansiQuery.
     *
     * @param mixed $qselect Raw Select Query
     * @param mixed $id
     *
     * @return void
     */
    private function instansiQuery($qselect, $id = null)
    {
        $data = DB::table('sampel')
                ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
                ->join('instansi', 'layanan.instansi_id', '=', 'instansi.id')
                ->selectRaw($qselect);
        if (!is_null($id)) {
            $data->whereRaw('deleted_at IS NULL AND layanan.instansi_id  ='.$id);
        } else {
            $data->whereRaw('deleted_at IS NULL');
        }

        return $data;
    }

    /**
     * layananQuery.
     *
     * @param mixed $qselect Raw Select Query
     * @param mixed $id
     *
     * @return void
     */
    private function layananQuery($qselect, $id = null)
    {
        $data = DB::table('sampel');
        $data->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
                ->selectRaw($qselect)
                ->whereRaw('deleted_at IS NULL');
        if (!is_null($id)) {
            $data->selectRaw($qselect);
            $data->whereRaw('deleted_at IS NULL AND sampel.layanan_id  ='.$id);
        }

        return $data;
    }

    /**
     * generateRows.
     *
     * @param mixed display sampel|ikm
     * @param array $arrTable  Array Hasil Query
     * @param mixed $kabupaten rincian kolom pertama
     *
     * @return void
     */
    private function generateRows($type, $tabel, array $arrTable, $group, $display, $kabupaten = '')
    {
        $arrTable = json_decode(json_encode($arrTable, true), true);
        foreach ($arrTable as $arr) {
            if ($kabupaten !== '') {
                $arr = Arr::prepend($arr, $kabupaten, 'firstcol');
            }
            foreach ($arr as $key => $value) {
                if ($value == 0) {
                    $value = '';
                }
                if ($key !== 'ikm' && $display == 'ikm' && $key !== 'jumlah' && $key !== 'firstcol' && $key !== 'link' && $value !== '') {
                    $arr[$key] = $this->setWarna(25 * $value, number_format($value, 2));
                }
            }
            if ($display == 'ikm') {
                $arr = Arr::add($arr, 'klasifikasi', $this->setWarna($arr['ikm'], $this->setKlasifikasi($arr['ikm'])));
                $arr['ikm'] = $this->setWarna($arr['ikm'], number_format($arr['ikm'], 2));
            }
            switch ($group) {
                case 'sampel':
                    $arr['firstcol'] = $this->generateLink("sampel/{$arr['link']}/edit", $arr['firstcol'] ?: 'Anonim');
                    break;
                case '':
                    $arr['firstcol'] = $this->generateLink("tabulasi/{$type}/{$tabel}/kelompok", $kabupaten);
                    break;

                default:
                    $arr['firstcol'] = $this->generateLink("tabulasi/{$type}/{$tabel}/{$group}/{$arr['link']}", $arr['firstcol']);
                    break;
            }
            unset($arr['link']);
            $arrTable2[] = $arr;
        }

        return $arrTable2;
    }

    /**
     * tableOption
     * Set Judul Print Table.
     *
     * @param mixed $title Judul saat Print/Eksport Tabel
     *
     * @return void
     */
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

    private function setFooter()
    {
        return 'Dicetak pada '.Carbon::parse(now())->translatedFormat('d F Y (H:i:s)');
    }

    private function setTitle($type, $display, $table, $group, $id, $html = true)
    {
        $title = 'Tabulasi';
        switch ($display) {
            case 'sampel':
                $title .= ' Sampel';
                break;
            default:
            $title .= ' Index';
                break;
        }
        if ($group !='') {
            $title .= ' menurut ';
            if (is_null($id)) {
                $title .= ucfirst($group).' (Semua)';
            } else {
                switch ($group) {
                    case 'kelompok':
                        if (!$html) {
                            $title .= 'Instansi';
                        } else {
                            $title .= '<a href="'.admin_url('tabulasi/'.$type.'/'.$table.'/instansi').'">Instansi</a>';
                        }
                        $title .= ' (Kelompok '.Kelompok::where('id', $id)->get('nama')->pluck('nama')[0].')';
                        break;
                    case 'instansi':
                    if (!$html) {
                        $title .= 'Layanan';
                    } else {
                        $title .= '<a href="'.admin_url('tabulasi/'.$type.'/'.$table.'/layanan').'">layanan</a>';
                    }
                        $title .= ' ('.Instansi::where('id', $id)->get('nama')->pluck('nama')[0].')';
                        break;
                    case 'layanan':
                        $title .= 'Sampel';
                        $title .= ' ('.Layanan::where('id', $id)->get('nama')->pluck('nama')[0].')';
                        break;
                }
            }
        }
        if ($type == 'detail') {
            $title .= ' berdasarkan ';
            switch ($table) {
                case 'Jk':
                    $title .= 'Jenis Kelamin';
                    break;
                case 'Jam':
                    $title .= 'Jam Pelayanan';
                    break;
                default:
                    $title .= ucfirst($table);
                    break;
            }
        }

        return $title;
    }

    /**
     * tableStyle
     * Style datatable.
     *
     * @return void
     */
    private function tableStyle()
    {
        return ['table-bordered', 'table-hover', 'table-striped'];
    }

    /**
     * setWarna
     * Set Warna Cell.
     *
     * @param mixed $batasBawah
     * @param mixed $text       isi Cell
     *
     * @return void
     */
    private function setWarna($batasBawah, $text)
    {
        $warna = Klasifikasi::where('batas', '<=', $batasBawah)->orderBy('batas', 'desc')->first('warna')->warna;

        return "<span style=\"color:{$warna};\">{$text}</span>";
    }

    /**
     * setKlasifikasi
     * membuat teks klasifikasi ikm.
     *
     * @param mixed $batasBawah
     *
     * @return void
     */
    private function setKlasifikasi($batasBawah)
    {
        return Klasifikasi::where('batas', '<=', $batasBawah)->orderBy('batas', 'desc')->first('klasifikasi')->klasifikasi;
    }

    private function generateLink($link, $text)
    {
        $link = admin_url($link);

        return "<a href=\"{$link}\">{$text}</a>";
    }
}
