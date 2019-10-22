<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Token\GenerateToken;
use App\Admin\Models\Layanan;
use App\Admin\Models\Token;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class TokenController extends Controller
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
            ->header('Index')
            ->description('List of Token')
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
            ->description('Detail of Token')
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
            ->header('Edit')
            ->description('Edit Token')
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
            ->header('Create')
            ->description('Create Token')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Token());

        $grid->token('Token');
        $grid->expired('Expired')->display(function ($date) {
            return Carbon::parse($date)->translatedFormat('d F Y h:m:s');
        });
        $grid->layanan()->nama('Layanan')->sortable();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new GenerateToken());
        });
        $grid->filter(function ($filter) {
            $filter->equal('layanan.id', 'Unit Layanan')->select(Layanan::all(['nama', 'id'])->pluck('nama', 'id'));
            $filter->between('expired', 'Tanggal Expired')->datetime();
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
        $show = new Show(Token::findOrFail($id));

        $show->id('id');
        $show->token('token');
        $show->expired('expired')->as(function ($date) {
            return Carbon::parse($date)->translatedFormat('d F Y h:m:s');
        });
        $show->id_layanan('layanan_id');
        $show->created_at()->as(function ($created_at) {
            return Carbon::parse($created_at)->translatedFormat('d F Y d:m:s');
        });
        $show->updated_at()->as(function ($updated_at) {
            return Carbon::parse($updated_at)->translatedFormat('d F Y d:m:s');
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
        $form = new Form(new Token());

        if ($form->isEditing()) {
            $form->display('id');
            $form->text('token')->rules('required|digits:6', [
                'required'=> 'Jumlah tidak boleh kosong',
                'digits'  => 'Isian  Harus 6 digit angka',
                ])->required();
        } elseif ($form->isCreating()) {
            $form->text('token')->rules('required|digits:6|unique:token,token', [
            'required'=> 'Jumlah tidak boleh kosong',
            'digits'  => 'Isian  Harus 6 digit angka',
            'unique'  => 'Token sudah ada',
            ])->required();
        }
        $form->datetime('expired');
        $form->select('layanan_id', 'Unit Layanan')->options(Layanan::all(['nama', 'id'])->pluck('nama', 'id'))
            ->required()->rules('required', ['Layanan Harus Dipilih']);

        return $form;
    }
}
