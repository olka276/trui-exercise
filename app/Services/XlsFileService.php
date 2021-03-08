<?php declare(strict_types=1);


namespace App\Services;

use App\Imports\DocumentImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class XlsFileService
 * @package App\Services
 */
class XlsFileService
{
    /**
     * Returns multidimensional array of xlsx file rows.
     *
     * @param string $path Path of XLSX file
     * @return array
     */
    private function loadFile(string $path): array
    {
        return Excel::toArray(new DocumentImport(), $path)[0];
    }

    /**
     * Returns multidimensional array with filter name without prefix
     * as key, and this filter available values array as value.
     *
     * @param array $array XLSX file rows
     * @param string $prefix Prefix of available filters
     * @return array
     */
    private function convertArray(array $array, $prefix = ''): array
    {
        $out = [];
        foreach ($array as $rowkey => $row) {
            if (!empty(array_filter($row, function ($a) {
                return $a !== null;
            }))) {
                foreach($row as $colkey => $col){
                    $filterName = $array[0][$colkey];
                    if (substr($filterName, 0, strlen($prefix) ) === $prefix) {
                        $filterName = substr($filterName, strlen($prefix), strlen($filterName));
                        if($rowkey != 0) {
                            $out[$filterName][]=$col;
                        }
                    }
                }
            }
        }

        return $out;
    }

    /**
     * Converts xlsx file into multidimensional array
     *
     * @param string $path Path of XLSX file
     * @param string $prefix Prefix of available filters
     * @return array
     */
    public function getDataFromXlsFile(string $path, string $prefix): array
    {
        $array = $this->loadFile($path);
        return $this->convertArray($array, $prefix);
    }
}
