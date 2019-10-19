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
use Illuminate\Support\Carbon;

class KelompokController extends Controller
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
            ->header('Kelompok')
            ->description('Daftar Kelompok')
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
            ->description('Detail Kelompok')
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
            ->description('Ubah Kelompok')
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
            ->description('Tambah kelompok')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Kelompok());

        $grid->nama('Nama Kelompok')->sortable()->editable();
        $grid->instansi('Instansi')->display(function ($instansi) {
            $count = count($instansi);

            return "<span class='label label-success'>{$count} Instansi</span>";
        });
        $grid->filter(function ($filter) {
            $filter->like('nama', 'Nama Kelompok');
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
        $show = new Show(Kelompok::findOrFail($id));

        $show->id('ID');
        $show->nama('Nama Kelompok');
        $show->created_at('Dibuat Pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (d:m)');
        });
        $show->updated_at('Diperbaharui pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (d:m)');
        });
        $show->instansi('', function ($grid) {
            $grid->resource('/admin/instansi');
            $grid->nama('Nama Instansi');
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
        $form = new Form(new Kelompok());
        if ($form->isEditing()) {
            $form->display('id', 'ID');
        }
        $form->text('nama', 'Nama Kelompok')->rules('required', ['required'=>'Nama Kelompok Harus Terisi']);
        $form->listbox('instansi', 'Nama Instansi')->options(Instansi::all()->pluck('nama', 'id'));

        return $form;
    }
}
