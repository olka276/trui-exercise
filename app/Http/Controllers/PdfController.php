<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PdfController extends Controller
{
    public function downloadFile(): BinaryFileResponse
    {
        $path = config('app.pdf_file_path');
        $headers = array(
            'Content-Description: File Transfer',
            'Content-Type: application/octet-stream',
            'Content-Disposition: attachment; filename="' . 'doc.pdf' . '"',
        );

        return response()->download(app_path($path), 'doc.pdf', $headers);
    }
}
