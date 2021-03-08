<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\XlsFileService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class XlsFileController
 * @package App\Http\Controllers
 */
class XlsFileController extends Controller
{
    /**
     * @var XlsFileService
     */
    private $xlsFileService;

    /**
     * XlsFileController constructor.
     * @param XlsFileService $xlsFileService
     */
    public function __construct
    (
        XlsFileService $xlsFileService
    )
    {
        $this->xlsFileService = $xlsFileService;
    }

    /**
     * @return JsonResponse|string
     */
    public function getFileData() {
        $path = app_path(config('app.xls_file_path'));
        $prefix = config('app.xls_file_filter_prefix');

        if(!file_exists($path)){
            return response()->json([
                "File doesn't exists."
            ], Response::HTTP_NOT_FOUND);
        }

        $xlsxData = $this->xlsFileService->getDataFromXlsFile($path, $prefix);

        return response()->json([
            $xlsxData
        ], Response::HTTP_OK);
    }
}
