<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Klasifikasi;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class KlasifikasiController extends Controller
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
            ->header('Klasifikasi')
            ->description('Pengaturan Klasifikasi')
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
            ->header('Detail')
            ->description('Detail Klasifikasi')
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
            ->header('Ubah')
            ->description('Ubah Klasifikasi')
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
            ->description('Tambah Klasifikasi')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Klasifikasi());

        $grid->batas('Batas Bawah');
        $grid->klasifikasi('Klasifikasi');
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
        $show = new Show(Klasifikasi::findOrFail($id));

        $show->id('Id');
        $show->batas('Batas Bawah');
        $show->klasifikasi('Klasifikasi');
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
        $form = new Form(new Klasifikasi());
        $form->text('batas', 'Batas Bawah')->rules('numeric|required', [
            'required'=> 'Kode Harus Terisi',
            'numeric' => 'Batas harus berupa angka',
            ])->help('Gunakan . (titik) sebagai pembatas desimal');
        $form->text('klasifikasi')->rules('required', ['required'=>'Nama Keterangan Harus Terisi']);

        return $form;
    }
}
