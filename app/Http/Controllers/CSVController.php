<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class CSVController extends Controller
{
    public function processCSV(Request $request){
        $csvRaw = $request->file('file');
        $euroExchangeRate = $request->euroExchangeRate;

        $data = $this->getCSVData($csvRaw);

        //Process data, except the first row (header)
        foreach($data as $key => $row){
            if($key > 1){
                //calculate charged days from start-end dates
                $startDate = Carbon::createFromFormat('Y.m.d', $row['ChargeStartDate']);
                $endDate = Carbon::createFromFormat('Y.m.d', $row['ChargeEndDate']);

                $data[$key]['chargedDays'] = $startDate->diffInDays($endDate);
            }
        }

        dd($data);
    }

    /**
     * Convert csv file into an array, which contains the data as: $row => array ($dataFieldName => $data)
     *
     * @return array
     */
    public function getCSVData($file){
        $csvData = array();

        //Fill $csv with data from the file
        $row = 1;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                for ($c=0; $c < count($data); $c++) {
                    //The first row contains the header (field names)
                    if($row <= 1){
                        $csvData[$row][$c] = $data[$c];
                    }else{
                        //Set the header (1st row) values as keys if the current row is NOT the header
                        $key = $csvData[1][$c];
                        $csvData[$row][$key] = $data[$c];
                    }
                }
                $row++;
            }
            fclose($handle);
        }

        return $csvData;
    }
}
