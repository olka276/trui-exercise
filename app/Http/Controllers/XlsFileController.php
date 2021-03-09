<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\XlsFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @return JsonResponse|string
     */
    public function getFileData(Request $request): JsonResponse
    {
        $data = $request->only([
            'column_name',
            'previous_option_array'
        ]);

        $path = app_path(config('app.xls_file_path'));
        $prefix = config('app.xls_file_filter_prefix');

        if(!file_exists($path)){
            return response()->json([
                "File doesn't exists."
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $xlsxData = $this
            ->xlsFileService
            ->getDataFromXlsFile($path, $prefix);

        $availableOptions = $this->xlsFileService->getAvailableOptions(
            $xlsxData,
            $data['column_name'],
            isset($data['previous_option_array']) ? $data['previous_option_array'] : [],
        );

        return response()->json([
            $availableOptions
        ], Response::HTTP_OK);
    }

    public function getFilterNames()
    {
        $path = app_path(config('app.xls_file_path'));
        $prefix = config('app.xls_file_filter_prefix');

        if(!file_exists($path)){
            return response()->json([
                "File doesn't exists."
            ], Response::HTTP_NOT_FOUND);
        }

        $xlsxData = $this
            ->xlsFileService
            ->getDataFromXlsFile($path, $prefix);

        $filters = $this
            ->xlsFileService
            ->getFilterNames($xlsxData);

        return response()->json([
            $filters
        ], Response::HTTP_OK);
    }
}
