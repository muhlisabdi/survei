<?php

namespace App\Admin\Actions\Token;

use App\Admin\Models\Layanan;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchLayanan extends BatchAction
{
    public $name = 'Edit Layanan';

    public function handle(Collection $collection, Request $request)
    {
        try {
            foreach ($collection as $model) {
                $model->layanan_id = $request->get('layanan');
                $model->save();
            }

            return $this->response()->success("Berhasil mengubah {$collection->count()} token")->refresh();
        } catch (Exception $e) {
            return $this->response()->error('Error: '.$e->getMessage());
        }
    }

    public function form()
    {
        $this->select('layanan', 'Unit layanan')->options(Layanan::all(['nama', 'id'])->pluck('nama', 'id'))->required();
    }
}
