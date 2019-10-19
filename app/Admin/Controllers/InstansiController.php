<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Instansi;
use App\Admin\Models\Kelompok;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class InstansiController extends Controller
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
            ->header('Instansi')
            ->description('Daftar Instansi')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('Detail Instansi')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Ubah')
            ->description('Ubah Instansi')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Tambah')
            ->description('Tambah Instansi')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Instansi());

        $grid->nama('Nama Instansi')->sortable()->editable();
        $grid->layanan('Unit Layanan')->display(function ($layanan) {
            $count = count($layanan);

            return "{$count} Layanan";
        })->label('primary');
        $grid->kelompok()->display(function ($kelompok) {
            $kelompok = array_map(function ($grup) {
                return "<span class='label label-success'>{$grup['nama']}</span>";
            }, $kelompok);

            return implode('&nbsp;', $kelompok);
        });
        $grid->filter(function ($filter) {
            $filter->like('nama', 'Nama Instansi');
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Instansi::findOrFail($id));

        $show->id('ID');
        $show->nama('Nama Instansi');
        $show->alamat('Alamat Instansi');
        $show->kota('Kota Instansi');
        $show->kepala('Nama Kepala');
        $show->nip('NIP Kepala');
        $show->created_at('Dibuat Pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (d:m)');
        });
        $show->updated_at('Diperbaharui pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (d:m)');
        });
        $show->layanan('', function ($grid) {
            $grid->resource('/admin/layanan');
            $grid->nama('Unit Layanan');
            $grid->disableCreateButton();
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disablePagination();
            $grid->disableActions();
        });
        $show->kelompok('', function ($grid) {
            $grid->resource('/admin/kelompok');
            $grid->nama('Kelompok');
            $grid->disableCreateButton();
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disablePagination();
            $grid->disableActions();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Instansi());

        if ($form->isEditing()) {
            $form->display('id', 'ID');
        }
        $form->text('nama', 'Nama Instansi')->rules('required', ['required'=>'Nama Instansi Harus Terisi']);
        $form->textarea('alamat', 'Alamat Instansi')->rules('required', ['required'=>'Alamat Instansi Harus Terisi'])->required();
        $form->text('kota', 'Nama Kota')->rules('required', ['required'=>'Nama Kota Harus Terisi'])->required();
        $form->text('kepala', 'Nama Kepala')->rules('required', ['required'=>'Nama Kepala Harus Terisi'])->required();
        $form->text('nip', 'NIP Kepala')->rules('required', ['required'=>'NIP Kepala Harus Terisi'])->required();
        $form->multipleSelect('kelompok', 'Kelompok')->options(Kelompok::all()->pluck('nama', 'id'));
        $form->hasMany('layanan', function (Form\NestedForm $form) {
            $form->text('nama', 'Nama Layanan');
        })->useTable();

        return $form;
    }
}
