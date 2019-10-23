<?php

namespace App\Admin\Actions\Token;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchExpired extends BatchAction
{
    public $name = 'Edit Kadaluarsa';

    public function handle(Collection $collection, Request $request)
    {
        try {
            foreach ($collection as $model) {
                $model->expired = $request->get('expired');
                $model->save();
            }
            return $this->response()->success("Berhasil mengubah {$collection->count()} token")->refresh();
            } catch (Exception $e) {

            return $this->response()->error('Error: '.$e->getMessage());
            }
    }
    public function form()
    {
        $this->datetime('expired', 'kadaluarsa')->required();
    }


}
