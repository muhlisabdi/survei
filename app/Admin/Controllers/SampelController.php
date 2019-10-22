<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Instansi;
use App\Admin\Models\Jam;
use App\Admin\Models\Jk;
use App\Admin\Models\Layanan;
use App\Admin\Models\Pekerjaan;
use App\Admin\Models\Pendidikan;
use App\Admin\Models\Sampel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Illuminate\Support\Carbon;

class SampelController extends Controller
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
            ->header('Sampel')
            ->description('Daftar Sampel')
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
            ->description('Detail Sampel')
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
            ->title('Ubah')
            ->description('Ubah Sampel')
            ->row(function (Row $row) {
                $row->column(2, function (Column $column) {
                    $column->append('');
                });

                $row->column(8, function (Column $column) {
                    $column->append('<div class="box"><div class="box-header with-border">Judul</div></div>');
                });

                $row->column(2, function (Column $column) {
                    $column->append('');
                });
            })
            ->row(function (Row $row) use ($id) {
                $row->column(2, function (Column $column) {
                    $column->append('');
                });

                $row->column(8, function (Column $column) use ($id) {
                    $column->append($this->form()->edit($id));
                });

                $row->column(2, function (Column $column) {
                    $column->append('');
                });
            });
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
            ->title('Tambah')
            ->description('Tambah Sampel')
            ->row(function (Row $row) {
                $row->column(2, function (Column $column) {
                    $column->append('');
                });

                $row->column(8, function (Column $column) {
                    $column->append('<div class="box"><div class="box-header with-border">Judul</div></div>');
                });

                $row->column(2, function (Column $column) {
                    $column->append('');
                });
            })
            ->row(function (Row $row) {
                $row->column(2, function (Column $column) {
                    $column->append('');
                });

                $row->column(8, function (Column $column) {
                    $column->append($this->form());
                });

                $row->column(2, function (Column $column) {
                    $column->append('');
                });
            });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Sampel());
        $grid->model()->orderBy('tanggal', 'desc');
        $grid->id('Nomor Sampel')->sortable();
        $grid->tanggal('Tanggal')->display(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        })
        ->sortable();
        $grid->nama('Nama Responden')->display(function ($nama) {
            if ($nama === null) {
                $nama = 'Anonim';
            }

            return $nama;
        })->sortable();
        $grid->layanan()->instansi_id('Instansi')->display(function ($instansi_id) {
            return Instansi::where('id', $instansi_id)->get('nama')->pluck('nama')[0];
        })->sortable();
        $grid->layanan()->nama('Unit Layanan')->sortable();
        $grid->filter(function ($filter) {
            $filter->equal('layanan.id', 'Unit Layanan')->select(Layanan::all(['nama', 'id'])->pluck('nama', 'id'));
            $filter->between('tanggal', 'Tanggal')->date();
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
        $show = new Show(Sampel::findOrFail($id));

        $show->id('Nomor Sampel');
        $show->created_at('Dibuat Pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (d:m)');
        });
        $show->updated_at('Diperbaharui pada')->as(function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y (d:m)');
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
        $form = new Form(new Sampel());

        if ($form->isEditing()) {
            $form->divider('Identitas Sampel');
            $form->display('id', 'Nomor Sampel');
        }

        $form->divider('Identitas Unit Layanan');

        // $form->select('namadinas')->options()->load('nama', '/api/instansi','id','nama');
        $form->select('layanan_id')->options(Layanan::all(['nama', 'id'])->pluck('nama', 'id'))->rules('required', ['Jenis Layanan Harus Dipilih'])->required();

        $form->date('tanggal', 'Tanggal')->help('Tanggal mendapatkan layanan')->rules('required|date', [
            'required'=> 'Tanggal tidak boleh kosong',
            ])->placeholder('Tanggal')->required();

        $form->radio('jam_id', 'Waktu')->options(Jam::all(['keterangan', 'kode'])->pluck('keterangan', 'kode'))->rules('required', [
            'Jam Memperoleh Layanan harus terisi',
            ])->required()->help('Jam mendapatkan Layanan')->stacked();

        $form->divider();

        $form->divider('Identitas Responden');

        $form->text('nama', 'Nama')->rules('regex: ^[a-zA-Z][a-zA-Z\\s]+$^|nullable', [
            'Nama hanya boleh mengandung huruf',
            ]);

        $form->text('umur', 'Umur')->rules('required|numeric|max:120|min:10', [
            'required'=> 'Umur tidak boleh kosong',
            'numeric' => 'Umur hanya bisa diisi angka',
            'max'     => 'Umur tidak sesuai',
            'min'     => 'Umur tidak sesuai',
            ])->placeholder('Umur')->setWidth(2)->required();

        $form->radio('jk_id', 'Jawaban')->options(Jk::all(['keterangan', 'kode'])->pluck('keterangan', 'kode'))->rules('required', [
            'Jenis kelamin harus terisi',
            ])->required()->stacked();

        $form->select('pendidikan_id', 'Pendidikan')->options(Pendidikan::all(['keterangan', 'kode'])->pluck('keterangan', 'kode'))->rules('required', [
            'Pendidikan harus terisi',
            ])->required()->help('Pendidikan terakhir yang ditamatkan');

        $form->select('pekerjaan_id', 'Pekerjaan')->options(Pekerjaan::all(['keterangan', 'kode'])->pluck('keterangan', 'kode'))->rules('required', [
            'Pekerjaan harus terisi',
            ])->required();

        $form->divider();

        $form->divider('Penilaian Terhadap Layanan');

        $form->divider('1. Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?')->note();

        $form->radio('u1', 'Jawaban')->options([
            '1'=> 'Tidak Sesuai',
            '2'=> 'Kurang Sesuai',
            '3'=> 'Sesuai',
            '4'=> 'Sangat Sesuai',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider('2. Bagaimana pemahaman saudara tentang kemudahan prosedur pelayananan di unit layanan ini?')->note();

        $form->radio('u2', 'Jawaban')->options([
            '1'=> 'Tidak Mudah',
            '2'=> 'Kurang Mudah',
            '3'=> 'Mudah',
            '4'=> 'Sangat Mudah',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider('3. Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan?')->note();

        $form->radio('u3', 'Jawaban')->options([
            '1'=> 'Tidak Cepat',
            '2'=> 'Kurang Cepat',
            '3'=> 'Cepat',
            '4'=> 'Sangat Cepat',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider('4. Bagaimana pendapat Saudara tentang kewajaran biaya/ tarif dalam pelayan?')->note();

        $form->radio('u4', 'Jawaban')->options([
            '1'=> 'Sangat Mahal',
            '2'=> 'Mahal',
            '3'=> 'Murah',
            '4'=> 'Gratis',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider('5. Bagaimana pendapat Saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?')->note();

        $form->radio('u5', 'Jawaban')->options([
            '1'=> 'Tidak Sesuai',
            '2'=> 'Kurang Sesuai',
            '3'=> 'Sesuai',
            '4'=> 'Sangat Sesuai',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider('6. Bagaimana pendapat Saudara tentang kompetensi/ kemampuan petugas dalam pelayanan?')->note();

        $form->radio('u6', 'Jawaban')->options([
            '1'=> 'Tidak Kompeten',
            '2'=> 'Kurang Kompeten',
            '3'=> 'Kompeten',
            '4'=> 'Sangat Kompeten',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider();

        $form->divider('7. Bagaimana pendapat Saudara perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?')->note();

        $form->radio('u7', 'Jawaban')->options([
            '1'=> 'Tidak Sopan dan Tidak Ramah',
            '2'=> 'Kurang Sopan dan Kurang Ramah',
            '3'=> 'Sopan dan Ramah',
            '4'=> 'Sangat Sopan dan Sangat Ramah',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider('8. Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana?')->note();

        $form->radio('u8', 'Jawaban')->options([
            '1'=> 'Buruk',
            '2'=> 'Cukup',
            '3'=> 'Baik',
            '4'=> 'Sangat Baik',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider('9. Bagaimana pendapat Saudara tentang penanganan pengaduan pengguna layanan?')->note();

        $form->radio('u9', 'Jawaban')->options([
            '1'=> 'Tidak Ada',
            '2'=> 'Ada tetapi Tidak Berfungsi',
            '3'=> 'Berfungsi Kurang Maksimal',
            '4'=> 'Dikelola dengan baik',
        ])->rules('required', ['Pilihan harus terisi'])->required()->stacked();

        $form->divider();

        $form->divider('10. Berikan tanggapan, saran maupun kesan Saudara setelah menggunakan layanan ini')->note();

        $form->textarea('saran', 'Jawaban');

        $form->divider();

        return $form;
    }
}
