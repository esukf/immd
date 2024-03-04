<?php
require __DIR__ . '/vendor/autoload.php';

use mikehaertl\pdftk\Pdf;


// Get data
$pdf = new Pdf('filled-test_1_flatten.pdf');
$data = $pdf->getData();
if ($data === false) {
    $error = $pdf->getError();
}

// Get form data fields
$pdf = new Pdf('filled-test_1_flatten.pdf');
$data = $pdf->getDataFields();
if ($data === false) {
    $error = $pdf->getError();
}

// Get data as string
echo $data;
$txt = (string) $data;
$txt = $data->__toString();

// Get data as array
$arr = (array) $data;
$arr = $data->__toArray();

var_dump($arr);
