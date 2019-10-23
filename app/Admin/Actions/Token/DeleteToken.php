<?php

namespace App\Admin\Actions\Token;

use App\Admin\Models\Token;
use Encore\Admin\Actions\Action;

class DeleteToken extends Action
{
    protected $selector = '.delete-token';
    public $name = 'Hapus Token Kadaluarsa';

    public function handle()
    {
        try {
            $affected = Token::where('expired', '<', now())->delete();

            return $this->response()->success("Berhasil menghapus {$affected} token kadaluarsa")->refresh();
        } catch (Exception $e) {
            return $this->response()->error('Error: '.$e->getMessage());
        }
    }

    public function dialog()
    {
        $this->confirm('Yakin Ingin Menghapus Token Kadaluarsa?', '', [
            'type'               => 'warning',
            'confirmButtonColor' => '#d33',
            'confirmButtonText'  => 'Ya, Hapus!',
            ]);
    }

    public function html()
    {
        return <<<'HTML'
        <a class="btn btn-sm btn-danger delete-token">Hapus Token Kadaluarsa</a>
HTML;
    }
}
