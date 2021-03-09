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
     * Converts xlsx file into two-dimensional array
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

    /**
     * Returns array of available options, according to previous choice
     *
     * @param array $array Two-dimensional array of all available options of filter column
     * @param string $columnName Name of filter column to show
     * @param array $previousOptionArray Array of chosen option, returned from this function
     * @return array
     */
    public function getAvailableOptions(array $array, string $columnName, array $previousOptionArray): array
    {
        $currentColumn = $array[$columnName];
        $indexArray = $this->getKeyArrayOfGivenValues($currentColumn);

        if(!empty($previousOptionArray)) {
            $availableKeys = $this->getValuesFromTwoDimensionalArray($previousOptionArray);
            $indexArray=$this->handleCommaInOptions($indexArray);
            $indexArray = $this->unsetNonAvailableValues($availableKeys, $indexArray);
        }

        return $indexArray;
    }

    /**
     * Get array of array keys.
     *
     * @param array $array Filter array
     * @return array
     */
    public function getFilterNames($array): array
    {
        return array_keys($array);
    }

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
        $result = [];
        foreach ($array as $rowkey => $row) {
            if (!empty(array_filter($row, function ($a) {
                return !is_null($a);
            }))) {
                foreach($row as $colkey => $col){
                    $filterName = $array[0][$colkey];
                    if (substr($filterName, 0, strlen($prefix) ) === $prefix) {
                        $filterName = substr($filterName, strlen($prefix), strlen($filterName));
                        //use first element of each array as key
                        if($rowkey != 0) {
                            $result[$filterName][]=$col;
                        }
                    }
                }
            }
        }

        foreach ($result as $key=>$column) {
            //avoid displaying filter, which all available options has empty
            if (empty(array_filter($column, function ($a) {
                return !is_null($a);
            }))) {
                unset($result[$key]);
            }
        }

        return $result;
    }

    /**
     * Return multidimensional array with unique values of previous array as keys. Value of each key
     * contains all keys which were corresponding to new array key.
     *
     * @param array$array Array to convert
     * @return array[][]
     */
    private function getKeyArrayOfGivenValues(array $array): array
    {
        $result = [];
        foreach($array as $key=>$value) {
            $result[$value][] = $key;
        }
        return $result;
    }

    /**
     * Checks if any key of array contains two words separated by comma
     * and separate this element into two separated keys.
     *
     * @param array[][] $array Array to convert
     * @return array
     */
    private function handleCommaInOptions(array $array): array
    {
        foreach ($array as $key=>$value) {
            $explodedArray = explode(',', $key);
            if (count($explodedArray) > 1) {
                foreach ($explodedArray as $item) {

                    //merge to avoid overriding an existing key
                    $array[trim($item)] = array_merge($array[trim($item)], $value);
                }
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Returns array of values of each array contained in two-dimensional array
     *
     * @param array[][] $array Two-dimensional array
     * @return array
     */
    private function getValuesFromTwoDimensionalArray(array $array): array
    {
        $values = [];
        foreach ($array as $valuesArray) {
            $values = array_merge($values, $valuesArray);
        }

        return $values;
    }

    /**
     * Unsets non-available values from two-dimensional array, and unsets
     * keys which value is an empty array
     *
     * @param array $availableKeys Array of keys which are allowed in two-dimensional array
     * @param array[][] $array Two-dimensional array to unset values
     * @return array
     */
    private function unsetNonAvailableValues(array $availableKeys, array $array): array
    {
        foreach ($array as $indexKey=>$indexName) {
            foreach ($indexName as $key=>$option) {
                if(!in_array($option, $availableKeys)) {
                    unset($array[$indexKey][$key]);
                }
            }
            if (empty($array[$indexKey])) {
                unset($array[$indexKey]);
            }
        }

        return $array;
    }
}
