<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Periode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class PeriodeController extends Controller
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
            ->header('Periode')
            ->description('Periode Survei')
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
            ->description('Detail Periode Survei')
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
            ->description('Ubah Periode Survei')
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
            ->description('Tambah Periode Survei')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Periode());
        $grid->nama('Periode')->sortable()->editable();
        $grid->start('Awal')->display(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        });
        $grid->end('Akhir')->display(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        });
        $grid->filter(function ($filter) {
            $filter->like('nama', 'Periode');
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
        $show = new Show(Periode::findOrFail($id));

        $show->id('ID');
        $show->nama('Periode');
        $show->start('Awal')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        });
        $show->end('Akhir')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        });
        $show->created_at('Dibuat Pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (H:i)');
        });
        $show->updated_at('Diperbaharui pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (H:i)');
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
        $form = new Form(new Periode());
        if ($form->isEditing()) {
            $form->display('id', 'ID');
        }
        $form->text('nama', 'Periode')->rules('required', ['required'=>'Nama Periode Harus Terisi']);
        $form->dateRange('start', 'end', 'Periode')->rules('required', ['required'=>'Tanggal harus terisi'])->required();

        return $form;
    }
}
