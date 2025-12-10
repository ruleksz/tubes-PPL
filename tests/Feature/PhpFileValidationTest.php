<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PhpFileValidationTest extends TestCase
{
    /** 1. Wajib upload file */
    public function test_file_php_wajib_dikirim()
    {
        $response = $this->post('/upload-php', []);

        $response->assertSessionHasErrors('file');
    }

    /** 2. Harus ber-ekstensi .php */
    public function test_file_harus_berekstensi_php()
    {
        $response = $this->post('/upload-php', [
            'file' => UploadedFile::fake()->create('test.txt', 1),
        ]);

        $response->assertSessionHasErrors('file');
    }

    /** 3. File yang diberikan tidak boleh kosong */
    public function test_file_tidak_boleh_kosong()
    {
        $response = $this->post('/upload-php', [
            'file' => UploadedFile::fake()->create('test.php', 0), // 0 KB → kosong
        ]);

        $response->assertSessionHasErrors('file');
    }

    /** 4. File PHP valid harus lolos */
    public function test_file_php_valid_diterima()
    {
        $response = $this->post('/upload-php', [
            'file' => UploadedFile::fake()->create('valid.php', 5), // 5 KB php file
        ]);

        $response->assertSessionDoesntHaveErrors();
    }

    /** 5. Nama file boleh apa saja asal .php */
    public function test_nama_file_bebas_asal_ekstensi_php()
    {
        $response = $this->post('/upload-php', [
            'file' => UploadedFile::fake()->create('apaaja_boleh123.php', 3),
        ]);

        $response->assertSessionDoesntHaveErrors();
    }
}
