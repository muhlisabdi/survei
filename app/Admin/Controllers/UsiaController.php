<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Usia;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class UsiaController extends Controller
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
            ->header('Kelompok Usia')
            ->description('Pengaturan Daftar Kelompok Usia')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description('Detail Kelompok Usia Usia')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description('Edit Kelompok Usia')
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
            ->header(trans('admin.create'))
            ->description('Tambah Kelompok Usia')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Usia());

        $grid->batas_bawah('Batas Bawah')->editable();
        $grid->kelompok('Kelompok Usia')->editable();
        $grid->disableFilter();
        $grid->disableExport();

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
        $show = new Show(Usia::findOrFail($id));

        $show->id('id');
        $show->batas_bawah('batas_bawah');
        $show->kelompok('kelompok');
        $show->created_at(trans('admin.created_at'))->as(function ($created_at) {
            return Carbon::parse($created_at)->translatedFormat('d F Y H:m:s');
        });
        $show->updated_at(trans('admin.updated_at'))->as(function ($updated_at) {
            return Carbon::parse($updated_at)->translatedFormat('d F Y H:m:s');
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
        $form = new Form(new Usia());

        $form->text('batas_bawah')->rules('numeric|required', [
            'required'=> 'Kode Harus Terisi',
            'numeric' => 'Batas harus berupa angka',
            ]);
        $form->text('kelompok', 'Kelompok Usia')->rules('required', ['required'=>'Nama Kelompok Harus Terisi']);

        return $form;
    }
}
