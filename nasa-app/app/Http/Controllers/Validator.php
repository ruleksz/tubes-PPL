<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Validator extends Controller
{
    public function uploadPhp(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:php',
                function ($attribute, $value, $fail) {

                    $content = file_get_contents($value->getRealPath());

                    if (trim($content) === '') {
                        $fail('Isi file PHP tidak boleh kosong.');
                        return;
                    }

                    if (!str_contains($content, '<?php')) {
                        $fail('File harus mengandung tag pembuka <?php.');
                        return;
                    }

                    $isPhpCode = preg_match('/class|function|\$[A-Za-z_]/', $content);

                    if (!$isPhpCode) {
                        $fail('Isi file tidak terdeteksi sebagai kode PHP.');
                    }
                }
            ]
        ]);

        return response()->json(['message' => 'File PHP valid!']);
    }
}
