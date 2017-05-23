<?php

if ($_POST) {
    // Validation
    $error = false;
    if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/', $_POST['year-and-month']))
    {
        $error = 'The input is in incorrect format.';
    }

    if (!$error) {
        require_once 'app/Calculator.php';

        $url = 'https://gist.githubusercontent.com/yonbergman/7a0b05d6420dada16b92885780567e60/raw/114aa2ffb1c680174f9757431e672b5df53237eb/data.csv';
        $yearAndMonth = $_POST['year-and-month'];

        $calculator = new Calculator($url);

        $revenue = $calculator->getRevenue($yearAndMonth);
        $totalCapacity = $calculator->getTotalCapacityOfUnreservedOffices($yearAndMonth);
    }
}

require_once 'views/index.php';