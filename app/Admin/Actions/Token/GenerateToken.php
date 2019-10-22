<?php

namespace App\Admin\Actions\Token;

use App\Admin\Models\Layanan;
use App\Admin\Models\Token;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class GenerateToken extends Action
{
    protected $selector = '.generate-token';
    public $name = 'Buat Token';

    public function handle(Request $request)
    {
        $data = [];
        for ($i = 0; $i < (int) $request->input('jumlah'); $i++) {
            $data[] = [
                'token'      => $this->generateToken(),
                'layanan_id' => $request->input('layanan'),
                'expired'    => $request->input('expired'),
            ];
        }
        Token::insert($data);
        return $this->response()->info("Berhasil menambah {$request->input('jumlah')} token")->refresh();
    }

    public function form()
    {
        $this->text('jumlah', 'Jumlah Token')->required()->rules('required|integer|min:1|max:100', [
            'required'=> 'Jumlah tidak boleh kosong',
            'integer' => 'Isian Jumlah Harus Angka',
            'min'     => 'Isian Minimal 1',
            'max'     => 'Isian maksimal 100',
            ])->placeholder('Maksimal 100 Token');
        $this->select('layanan', 'Unit layanan')->options(Layanan::all(['nama', 'id'])->pluck('nama', 'id'))->required();
        $this->datetime('expired', 'Berlaku Sampai')->placeholder('Berlaku Sampai');
    }

    protected function generateToken()
    {
        $token = mt_rand(100000, 999999);

        if ($this->tokenExists($token)) {
            return $this->generateToken();
        }
        return $token;
    }

    protected function tokenExists($token)
    {
        return Token::where('token', $token)->exists();
    }

    public function html()
    {
        return <<<'HTML'
        <a class="btn btn-sm btn-info generate-token">Buat Token</a>
HTML;
    }
}
