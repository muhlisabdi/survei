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
        $grid->model()->where('saran', '!=', null);
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
        $grid->instansi()->nama('Instansi');
        $grid->layanan()->nama('Unit Layanan')->sortable();
        $grid->filter(function ($filter) {
            $filter->equal('layanan.instansi_id', 'Instansi')->select(function () {

                return Instansi::all(['nama', 'id'])->pluck('nama', 'id')->toArray();
            })->load('layanan.id', 'api/layanan');

            $filter->equal('layanan.id', 'Layanan')->select(function ($id) {

                return Layanan::where('id', $id)->pluck('nama', 'id')->toArray();
            });
            $filter->between('tanggal', 'Tanggal')->date();
        });
        $grid->saran('Saran/masukan')->limit(30)->modal('Saran/Masukan', function ($saran) {
            return $saran->saran;
        });
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }
}
