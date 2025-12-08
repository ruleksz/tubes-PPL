<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;

class PhpFileValidationTest extends TestCase
{
    public function test_file_php_wajib_dikirim()
    {
        $response = $this->post('/upload-php', []);

        $response->assertSessionHasErrors('file');
    }

    public function test_file_harus_berekstensi_php()
    {
        $response = $this->post('/upload-php', [
            'file' => UploadedFile::fake()->create('test.txt', 10)
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_file_php_tidak_boleh_kosong()
    {
        $file = UploadedFile::fake()->createWithContent('empty.php', '');

        $response = $this->post('/upload-php', ['file' => $file]);

        $response->assertSessionHasErrors('file');
    }

    public function test_file_harus_mengandung_tag_php()
    {
        $file = UploadedFile::fake()->createWithContent('notag.php', 'echo 1;');

        $response = $this->post('/upload-php', ['file' => $file]);

        $response->assertSessionHasErrors('file');
    }

    public function test_file_php_valid()
    {
        $file = UploadedFile::fake()->createWithContent(
            'valid.php',
            "<?php function test() {}"
        );

        $response = $this->post('/upload-php', ['file' => $file]);

        $response->assertJson(['message' => 'File PHP valid!']);
    }
}
