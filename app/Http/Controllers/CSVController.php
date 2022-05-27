<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class CSVController extends Controller
{
    /**
     * The columns that should be hidden for view.
     *
     * @var array<int, string>
     */
    protected $hiddenColumns = [
        'Reseller',
        'CustomerName',
        'chargedDays'
    ];

    public function data(Request $request){
        $euroExchangeRate = $request->euroExchangeRate;

        //Calculate billingDays from billingStart and billingEnd date
        $billingDays = Carbon::createFromFormat('Y-m-d', $request->billingStartDate)
            ->diffInDays(Carbon::createFromFormat('Y-m-d', $request->billingEndDate)) + 1;

        //Calculate the daily price of the Ms product in EUR (the price is calculated with the given billing range)
        $unitPrices = array();

        //Set unitPrices and originalPrices (from input)
        $unitPrices['Microsoft 365 Business Basic'] = [
            'orig_price' => $request->MBB,
            'u_price' => $request->MBB / $billingDays
        ];
        $unitPrices['Microsoft 365 Business Standard'] = [
            'orig_price' => $request->MBS,
            'u_price' => $request->MBS / $billingDays
        ];
        $unitPrices['Exchange Online (Plan 1)'] = [
            'orig_price' => $request->EO,
            'u_price' => $request->EO / $billingDays
        ];

        //Get data from the uploaded CSV file
        $csvRaw = $request->file('file');
        $data = $this->getCSVData($csvRaw);

        //Process data, except the first row (header) and filter by ChargeType
        foreach($data as $key => $row){
            if($key > 1){
                //calculate charged days from start-end dates
                $startDate = Carbon::createFromFormat('Y.m.d', $row['ChargeStartDate']);
                $endDate = Carbon::createFromFormat('Y.m.d', $row['ChargeEndDate']);
                $data[$key]['chargedDays'] = $startDate->diffInDays($endDate) + 1;

                //Select the price for the current row from the unitPrices array based on the product's name
                $priceArray = 0;
                foreach($unitPrices as $priceKey => $unitPrice){
                    if($priceKey == $row['SkuName']){
                        $priceArray = $unitPrice;
                        break;
                    }
                }

                //calculate total from chargedDays, qunatity and unitPrice for subscription base billing
                if($row['ChargeType'] == 'Cycle instance prora'){
                    $data[$key]['total'] = $data[$key]['chargedDays'] * $row['Quantity_'] * $priceArray['u_price'];
                }else{//... and for any other billing type
                    $data[$key]['total'] = $row['Quantity_'] * $priceArray['orig_price'];
                }



                //Push 'new' field names to the header row if they are not there yet
                if (!in_array('chargedDays', $data[1])){
                    array_push($data[1], 'chargedDays');
                }
                if (!in_array('total', $data[1])){
                    array_push($data[1], 'total');
                }
            }
        }

        //Calculate ms_total, own_total and sub_totals
        $totals = $this->calculateTotals($data);

        //Modify data array for view
        $dataForView = $this->modifyForView($data);

        return view('data', ['data' => $dataForView, 'totals' => $totals, 'euroExchangeRate' => $euroExchangeRate]);
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


    /**
     * Modify data array for view
     *
     * @return array
     */
    public function modifyForView($data){
        //Hide columns that are in the $hiddenColumns array
        foreach($data as $key => $row){
            //Remove column header
            if($key <= 1){
                foreach($this->hiddenColumns as $hiddenName){
                    $keyToRemove = array_search($hiddenName, $data[1]);
                    unset($data[1][$keyToRemove]);
                }
            }else{ // remove the values from each row
                foreach($this->hiddenColumns as $hiddenName){
                    unset($data[$key][$hiddenName]);
                }
            }
        }

        return $data;
    }


    /**
     * Modify data array for view
     *
     * @return array
     */
    public function calculateTotals($data){
        $totals['ms_total'] = 0;
        $totals['own_total'] = 0;
        $totals['CIP_total'] = 0;
        $totals['CF_total'] = 0;

        foreach($data as $key => $row){
            if($key > 1){
                $totals['ms_total'] += $row['Total_'];
                $totals['own_total'] += $row['total'];

                if($row['ChargeType'] == "Cycle instance prora"){
                    $totals['CIP_total'] += $row['total'];
                }else{
                    $totals['CF_total'] += $row['total'];
                }
            }
        }

        return $totals;
    }
}
