<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Instansi;
use App\Admin\Models\Layanan;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class LayananController extends Controller
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
            ->header('Unit Layanan')
            ->description('Daftar Unit Layanan')
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
            ->description('Detail Unit Layanan')
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
            ->description('Ubah Unit Layanan')
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
            ->description('Tambah Unit Layanan')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Layanan());
        $grid->nama('Unit Layanan')->sortable()->editable();
        $grid->instansi()->nama('Nama Instansi')->sortable();
        $grid->filter(function ($filter) {
            $filter->like('nama', 'Unit Layanan');
            $filter->equal('instansi.id', 'Instansi')->select(Instansi::all(['nama', 'id'])->pluck('nama', 'id'));
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
        $show = new Show(Layanan::findOrFail($id));

        $show->id('ID');
        $show->nama('Nama Unit Layanan');
        $show->instansi('Nama Instansi')->as(function ($instansi) {
            return $instansi->nama;
        });
        $show->created_at('Dibuat Pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (d:m)');
        });
        $show->updated_at('Diperbaharui pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (d:m)');
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
        $form = new Form(new Layanan());

        if ($form->isEditing()) {
            $form->display('id', 'ID');
        }
        $form->text('nama', 'Nama Unit Layanan')->rules('required', ['required'=>'Nama Layanan Harus Terisi']);
        $form->select('instansi_id', 'Nama Instansi')->options(Instansi::all(['nama', 'id'])->pluck('nama', 'id'))->rules('required', ['required'=>'Nama Instansi Harus Terisi']);

        return $form;
    }
}
