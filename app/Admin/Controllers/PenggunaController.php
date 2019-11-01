<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Instansi;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class PenggunaController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.administrator');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());

        $grid->column('avatar', 'Avatar')->image(48, 48);
        $grid->column('name', trans('admin.name'));
        $grid->column('nip', 'NIP');
        $grid->column('jabatan', 'Jabatan');
        $grid->column('phone', 'Telepon');
        $grid->column('instansi.nama', 'Instansi');
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label();

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
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
        $userModel = config('admin.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->field('avatar', '')->image()->setWidth(2);
        $show->divider();
        $show->field('id', 'ID');
        $show->field('name', trans('admin.name'));
        $show->field('nip', 'NIP');
        $show->field('jabatan', 'Jabatan');
        $show->field('phone', 'Telepon');
        $show->field('instansi', 'Instansi')->as(function ($instansi) {
            return $instansi->nama;
        });
        $show->divider();
        $show->field('username', trans('admin.username'));
        $show->field('roles', trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->created_at('Dibuat Pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (H:i)');
        });
        $show->updated_at('Diperbaharui pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (H:i)');
        });
        $show->panel()->tools(function ($tools) use ($id) {
            if ($id == 1) {
                $tools->disableDelete();
            }
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->display('id', 'ID');
        $form->divider('Info Pengguna');
        $form->text('name', trans('admin.name'))->rules('required');
        $form->text('nip', 'NIP');
        $form->text('jabatan', 'Jabatan');
        $form->text('phone', 'Telepon')->rules('required')->required();
        $form->select('instansi_id', 'Instansi')
                ->options(Instansi::all(['nama', 'id'])->pluck('nama', 'id'))->rules('required', ['Instansi Harus Dipilih'])->required();
        $form->image('avatar', trans('admin.avatar'));
        $form->divider('Info Akun');
        $form->text('username', trans('admin.username'))
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }
}
