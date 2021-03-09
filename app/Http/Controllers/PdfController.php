<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class PdfController
 * @package App\Http\Controllers
 */
class PdfController extends Controller
{
    /**
     * Triggers downloading file in browser
     *
     * @return BinaryFileResponse
     */
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
