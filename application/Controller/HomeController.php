<?php

namespace Mini\Controller;

use GuzzleHttp\Exception\GuzzleException;
use Mini\Model\Project;
use SSP;

class HomeController
{

    public function index()
    {
        $projectModel = new Project();
        $allProjects = $projectModel->getAllProjects();

        $chartData = [
            'Less than 500' => 0,
            '500-1000' => 0,
            '1000-5000' => 0,
            'More than 5000' => 0
        ];

        foreach ($allProjects as $project) {
            if ($project->budget && $project->currency) {
                $budget = $this->exchange('UAH', $project->currency, $project->budget);

                switch ($budget) {
                    case $budget <= 500:
                        $chartData['Less than 500']++;
                        break;
                    case $budget > 500 && $budget <= 1000:
                        $chartData['500-1000']++;
                        break;
                    case $budget > 1000 && $budget <= 5000:
                        $chartData['1000-5000']++;
                        break;
                    case $budget > 5000:
                        $chartData['More than 5000']++;
                        break;
                }

            }

        }

        $dataForChart = [];

        foreach ($chartData as $key => $value) {
            $dataForChart[] = [
                'label' => $key,
                'y' => $value
            ];
        }


        // load views
        require APP . 'view/_templates/header.php';
        require APP . 'view/home/index.php';
        require APP . 'view/_templates/footer.php';
    }


    public function getProjects()
    {
        $table = 'projects';

        $primaryKey = 'id';

        $columns = [
            ['db' => 'name', 'dt' => 0,
                'formatter' => function ($d, $row) {
                    return '<a href="' . $row['link'] . '" target="blank">' . $d . '</a>';
                }],
            ['db' => 'budget', 'dt' => 1],
            ['db' => 'currency', 'dt' => 2],
            ['db' => 'user_name', 'dt' => 3],
            ['db' => 'user_login', 'dt' => 4],
            ['db' => 'link', 'dt' => 6],
            ['db' => 'skills', 'dt' => 7],

        ];

        $sql_details = array(
            'user' => DB_USER,
            'pass' => DB_PASS,
            'db' => DB_NAME,
            'host' => DB_HOST
        );

        require 'SSP.php';

        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
        );

    }

    private function exchange(string $baseCurrency, string $currency, int $value)
    {
        if ($baseCurrency == $currency) {
            return $value;
        }

        //Make Russian currency match
        if ($currency == 'RUB') {
            $currency = 'RUR';
        }

        $rates = $this->getCurrencyRate();

        $result = 0;

        foreach ($rates as $rate) {
            if ($rate['ccy'] == $currency && $rate['base_ccy'] == $baseCurrency) {
                $result = round($value * $rate['buy'], 2);
                break;
            }
        }

        return $result;
    }

    private function getCurrencyRate()
    {
        $responseData = [];
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5', [
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            $page = new \Mini\Controller\ErrorController();
            $page->index($e->getMessage());
        }

        return $responseData;
    }
}
