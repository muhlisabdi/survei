<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Instansi;
use App\Admin\Models\Layanan;
use App\Admin\Models\Sampel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Carbon;

class SaranController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Saran')
            ->description('Daftar Saran')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Sampel());
        $grid->model()->orderBy('tanggal', 'desc');
        $grid->tanggal('Tanggal')->display(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        })
        ->sortable();
        $grid->nama('Nama Responden')->display(function ($nama) {
            if ($nama === null) {
                $nama = 'Anonim';
            }

            return $nama;
        })->sortable();
        $grid->layanan()->instansi_id('Instansi')->display(function ($instansi_id) {
            return Instansi::where('id', $instansi_id)->get('nama')->pluck('nama')[0];
        })->sortable();
        $grid->layanan()->nama('Unit Layanan')->sortable();
        $grid->filter(function ($filter) {
            $filter->equal('layanan.id', 'Unit Layanan')->select(Layanan::all()->pluck('nama', 'id'));
            $filter->between('tanggal', 'Tanggal')->date();
        });
        $grid->saran('Saran/masukan')->limit(30)->modal('Saran/Masukan', function ($saran) {
            return $saran->saran;
        });
        $grid->disableCreateButton();
        $grid->disableActions();

        return $grid;
    }
}
