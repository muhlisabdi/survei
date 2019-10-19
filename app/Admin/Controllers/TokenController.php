<?php

namespace App\Admin\Controllers;

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

        $grid->id('id')->sortable();
        $grid->token('token');
        $grid->expired('expired')->display(function ($date) {
            return Carbon::parse($date)->translatedFormat('d F Y h:m:s');
        });
        $grid->id_layanan('id_layanan');
        $grid->created_at()->display(function ($created_at) {
            return Carbon::parse($created_at)->translatedFormat('d F Y d:m:s');
        });
        $grid->updated_at()->display(function ($updated_at) {
            return Carbon::parse($updated_at)->translatedFormat('d F Y d:m:s');
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
        $show->id_layanan('id_layanan');
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
        }
        $form->text('token');
        $form->datetime('expired');
        $form->number('id_layanan');

        return $form;
    }
}
