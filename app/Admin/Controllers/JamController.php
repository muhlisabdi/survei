<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Jam;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class JamController extends Controller
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
            ->header('Jam Layanan')
            ->description('Pengaturan Pilihan Jam Layanan')
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
            ->description('Detail Jam Layanan')
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
            ->description('Ubah Pilihan Jam Layanan')
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
            ->description('Tambah Pilihan Jam Layanan')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Jam());

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
        $show = new Show(Jam::findOrFail($id));

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
        $form = new Form(new Jam());
        if ($form->isEditing()) {
            $form->display('kode', 'Kode');
        } else {
            $form->number('kode', 'Kode')->rules('numeric|required|unique:jam,kode', [
                'required'=> 'Kode Harus Terisi',
                'unique'  => 'Kode Sudah Ada, Silakan buat kode yang lain',
                ]);
        }
        $form->text('keterangan', 'Keterangan')->rules('required', ['required'=>'Nama Keterangan Harus Terisi']);

        return $form;
    }
}
