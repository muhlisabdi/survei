<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Admin\Extensions\Nav;

Grid::init(function (Grid $grid) {

    $grid->disableColumnSelector();
    $grid->filter(function ($filter) {
        $filter->disableIdFilter();
    });

});

Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    $navbar->right(Nav\Shortcut::make([
        'Sampel' => 'sampel/create'
    ], 'fa-plus')->title('Tambah'));

});

Encore\Admin\Form::forget(['map', 'editor']);
