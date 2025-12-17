<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class ValidatePhpFiles extends Command
{
    protected $signature = 'validate:php-files';
    protected $description = 'Validasi file PHP yang di-push cah cah';

    public function handle()
    {
        $this->info('🔍 Menjalankan validasi file PHP');

        $finder = new Finder();
        $finder->files()->in([
            app_path(),
            base_path('routes'),
            resource_path('views'),
        ]);

        $errors = [];

        foreach ($finder as $file) {
            $relativePath = $file->getRelativePathname();
            $filename     = $file->getFilename();
            $realPath     = $file->getRealPath();

            /**
             * TEST CASE 1
             * File harus .php atau .blade.php
             */
            $isPhp =
                str_ends_with($filename, '.php') ||
                str_ends_with($filename, '.blade.php');

            if (! $isPhp) {
                $errors[] = "❌ [HARUS PHP] File non-PHP ditemukan: {$relativePath}";
                continue;
            }

            /**
             * TEST CASE 2
             * File PHP tidak boleh kosong
             */
            if ($file->getSize() === 0) {
                $errors[] = "❌ [NOT EMPTY] File kosong: {$relativePath}";
                continue;
            }

            /**
             * TEST CASE 3
             * Valid syntax PHP
             */
            $output = [];
            $return = 0;
            exec("php -l {$realPath} 2>&1", $output, $return);

            if ($return !== 0) {
                $errors[] = "❌ [VALID SYNTAX] Syntax error: {$relativePath}";
                continue;
            }

            /**
             * TEST CASE 4
             * Tidak boleh ada debug code
             */
            $content = file_get_contents($realPath);
            if (preg_match('/\b(dd|dump|var_dump)\s*\(/', $content)) {
                $errors[] = "❌ [DEBUG CODE] Debug function ditemukan: {$relativePath}";
            }
        }

        if (! empty($errors)) {
            $this->error('🚫 Validasi GAGAL. Ditemukan pelanggaran:');
            foreach ($errors as $error) {
                $this->line($error);
            }
            return Command::FAILURE; // exit code 1
        }

        $this->info('✅ Semua file PHP valid. Repository aman.');
        return Command::SUCCESS; // exit code 0
    }
}
