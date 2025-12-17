<?php

namespace Tests\Feature;

use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class PhpFileValidationTest extends TestCase
{
    /**
     * Test Case 1 harus php
     */
    public function test_harus_php()
    {
        $finder = new Finder();
        $finder->files()->in([
            app_path(),
            base_path('routes'),
            resource_path('views'),
        ]);

        $invalidFiles = [];

        foreach ($finder as $file) {
            if ($file->getExtension() !== 'php') {
                $invalidFiles[] = $file->getRelativePathname();
            }
        }

        $this->assertEmpty(
            $invalidFiles,
            'File non-PHP ditemukan' . implode(', ', $invalidFiles)
        );
    }
    /**
     * Test Case 2: Valid Syntax
     * file php harus valid
     */
    public function test_file_php_syntax_valid()
    {
        $finder = new Finder();
        $finder->files()
            ->in([app_path(), base_path('routes'), resource_path('views')])
            ->name('*.php');

        $errors = [];

        foreach ($finder as $file) {
            $output = [];
            $return = 0;
            exec("php -l {$file->getRealPath()}", $output, $return);

            if ($return !== 0) {
                $errors[] = $file->getRelativePathname();
            }
        }

        $this->assertEmpty(
            $errors,
            'File dengan syntax error: ' . implode(', ', $errors)
        );
    }

    /**
     * Test Case 3: Not Empty
     * File PHP tidak boleh kosong (0 byte)
     * dan tidak boleh hanya berisi tag PHP / komentar
     */
    public function test_file_php_tidak_boleh_kosong()
    {
        $finder = new Finder();
        $finder->files()
            ->in([app_path(), base_path('routes'), resource_path('views')])
            ->name('*.php');

        $invalidFiles = [];

        foreach ($finder as $file) {
            // 1. Validasi ukuran file
            if ($file->getSize() === 0) {
                $invalidFiles[] = $file->getRelativePathname() . ' (0 byte)';
                continue;
            }

            // 2. Ambil dan bersihkan isi file
            $content = trim(file_get_contents($file->getRealPath()));

            // Hapus tag <?php di awal
            $content = preg_replace('/^\<\?php\s*/', '', $content);

            // Hapus komentar (//, #, /* */)
            $content = preg_replace([
                '/\/\/.*$/m',
                '/#.*$/m',
                '/\/\*[\s\S]*?\*\//',
            ], '', $content);

            // Jika setelah dibersihkan tetap kosong
            if (trim($content) === '') {
                $invalidFiles[] = $file->getRelativePathname() . ' (hanya tag PHP / komentar)';
            }
        }

        $this->assertEmpty(
            $invalidFiles,
            "File PHP tidak boleh kosong atau hanya berisi tag PHP:\n" .
            implode("\n", $invalidFiles)
        );
    }

    /**
     * Test case 4: Tidak ada debug code
     */
    public function test_tidak_ada_debug_code()
    {
        $finder = new Finder();
        $finder->files()
            ->in([app_path(), base_path('routes'), resource_path('views')])
            ->name('*.php');

        $filesWithDebug = [];

        foreach ($finder as $file) {
            $content = file_get_contents($file->getRealPath());

            if (preg_match('/\b(dd|dump|var_dump)\s*\(/', $content)) {
                $filesWithDebug[] = $file->getRelativePathname();
            }
        }

        $this->assertEmpty(
            $filesWithDebug,
            'Debug code terdeteksi di: ' . implode(', ', $filesWithDebug)
        );
    }
}
