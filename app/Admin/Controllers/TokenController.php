<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Token\BatchExpired;
use App\Admin\Actions\Token\BatchLayanan;
use App\Admin\Actions\Token\DeleteToken;
use App\Admin\Actions\Token\GenerateToken;
use App\Admin\Models\Layanan;
use App\Admin\Models\Token;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Form;
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
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Token());

        $grid->token('Token');
        $grid->expired('Kadaluarsa')->display(function ($date) {
            return Carbon::parse($date)->translatedFormat('d F Y H:i:s');
        })->sortable();
        $grid->layanan()->nama('Layanan')->sortable();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new GenerateToken());
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new DeleteToken());
        });
        $grid->batchActions(function ($batch) {
            $batch->add(new BatchLayanan());
        });
        $grid->batchActions(function ($batch) {
            $batch->add(new BatchExpired());
        });
        $grid->filter(function ($filter) {
            $filter->equal('layanan.id', 'Unit Layanan')->select(Layanan::all(['nama', 'id'])->pluck('nama', 'id'));
            $filter->between('expired', 'Kadaluarsa')->datetime();
        });
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableEdit();
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

        $show->id('Id');
        $show->token('Token');
        $show->expired('Kadaluarsa')->as(function ($date) {
            return Carbon::parse($date)->translatedFormat('d F Y H:i:s');
        });
        $show->layanan('Unit Layanan')->as(function ($layanan) {
            return $layanan->nama;
        });
        $show->created_at('Dibuat pada')->as(function ($created_at) {
            return Carbon::parse($created_at)->translatedFormat('d F Y H:i:s');
        });
        $show->updated_at('Diperbaharui pada')->as(function ($updated_at) {
            return Carbon::parse($updated_at)->translatedFormat('d F Y H:i:s');
        });
        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            $tools->disableDelete();
        });

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Token());
        return $form;
    }
}
