<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

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
