<?php

namespace Controller\CsvController;

use Core\Controller\ICoreController;
use Core\Injectable\Injectable;
use Services\CsvService\CsvService;

class CsvController extends Injectable implements ICoreController

{
    public const CSV_PATH = '../../assets/';

    static public function config()
    {
        return [
            'metadata' => false,
        ];
    }

    static public function inject()
    {
        return [
            'CsvService' => [
                'class' => CsvService::class,
            ]
        ];
    }

    static public function routes()
    {
        return [
            '/read' => [
                'GET' => [
                    'controller' => CsvController::class,
                    'handler' => 'read_csv'
                ]
            ]
        ];
    }

    public function read_csv()
    {
        $handle = fopen(__DIR__ . "/../../assets/ref.csv", "r");
        $csv = [];
        while (($row = fgetcsv($handle)) !== FALSE) {
            array_push($csv, $row);
        }

        $headers = $csv[0];

        array_shift($csv);

        fclose($handle);

        foreach ($csv as $key => $value) {
            $csv[$key][4] = $value[3][0] === '-' ? 'negative' : 'positive';
        }

        $totals = $this->inject['CsvService']->total($csv);

        return [
            "view" => "transactions.php",
            "message" => "User created successfully",
            'headers' => $headers,
            'csv' => $csv,
            'total' => $totals['total'],
            'total_positive' => $totals['total_positive'],
            'total_negative' => $totals['total_negative']
        ];
    }
}
