<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Pendidikan;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class PendidikanController extends Controller
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
            ->header('Pendidikan')
            ->description('Pengaturan Pilihan Pendidikan')
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
            ->description('Detail Pendidikan')
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
            ->description('Ubah Pilihan Pendidikan')
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
            ->description('Tambah Pilihan Pendidikan')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Pendidikan());

        $grid->kode('Kode');
        $grid->keterangan('Keterangan')->editable();
        $grid->disableFilter();

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
        $show = new Show(Pendidikan::findOrFail($id));

        $show->kode('Kode');
        $show->keterangan('Keterangan');
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
        $form = new Form(new Pendidikan());
        if ($form->isEditing()) {
            $form->display('kode', 'Kode');
        } else {
            $form->number('kode', 'Kode')->rules('required|numeric|unique:pendidikan,kode', [
                'required'=> 'Kode Harus Terisi',
                'unique'  => 'Kode Sudah Ada, Silakan buat kode yang lain',
                ]);
        }
        $form->text('keterangan', 'Keterangan')->rules('required', ['required'=>'Nama Keterangan Harus Terisi']);

        return $form;
    }
}
