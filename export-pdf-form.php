<?php
require __DIR__ . '/vendor/autoload.php';

use mikehaertl\pdftk\Pdf;

$inputPdf = $argv[1];
$excelFile = $argv[2];

$tempDir = __DIR__ . '/temp';
check_temp_directory('temp');

$random = bin2hex(random_bytes(10));
$decryptedFile = decryptPdf($inputPdf, $tempDir, $random);

// Get pdf form data
$pdf = new Pdf($decryptedFile);
$data = $pdf->getDataFields();
if ($data === false) {
    echo $pdf->getError();
    die();
}

$formData = $data->__toArray();

//var_dump($formData);

$header = [];
$data = [];

foreach ($formData as $field) {
    // Skip if field name start with btn.
    if (strpos($field['FieldName'], 'btn') === 0) {
        continue;
    }

    // skip excluded fields
    if (in_array($field['FieldName'], exclude_fields())) {
        continue;
    }

    $header[] = $field['FieldName'];

    $fieldNameAlt = (isset($field['FieldNameAlt'])) ? $field['FieldNameAlt'] : '';
    $fieldValue = (isset($field['FieldValue'])) ? $field['FieldValue'] : '';
    $value = getOptionValue($field['FieldName'], $fieldValue);

    $data[$field['FieldName']] = $value;
}

writeExcel($excelFile, [array_values($data)], [$header]);




/**
 * Check temp directory exist else create it.
 *
 * @param $path
 *
 * @return void
 */
function check_temp_directory($path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

/**
 * Mapping the field options from the pdf form data.
 *
 * @param $fieldName
 * @param $value
 *
 * @return mixed|string
 */
function getOptionValue($fieldName, $value) {
    if ($value === null or $value === '' or $value === 'off') {
        return '';
    }

    switch ($fieldName) {
        case 'sex':
        case 'sponSex1':
        case 'sponSex2';
            $map = [
                '0' => '女',
                '1' => '男',
            ];

            return (isset($map[$value])) ? $map[$value] : 'ERROR';

        case 'maritalStat':
            $map = [
                '0' => '已婚',
                '1' => '喪偶',
                '2' => '離婚',
                '3' => '其他',
                '4' => '分居',
                '5' => '未婚',
            ];

            return (isset($map[$value])) ? $map[$value] : 'ERROR';

        case 'preReside':
        case 'appStay':
        case 'sponPerReside1':
        case 'sponPerReside2':
        case 'shortTermStudy':
        case 'chgName':
        case 'refEntry':
        case 'refVisa':
            $map = [
                '0' => '否',
                '1' => '是',
            ];

            return (isset($map[$value])) ? $map[$value] : 'ERROR';

        case 'status':
            $map = [
                '0' => '居留／受養人',
                '1' => '就業',
                '2' => '訪客',
                '3' => '其他',
            ];

            return (isset($map[$value])) ? $map[$value] : 'ERROR';

        case 'sponStatus2':
            $map = [
                '0' => '就業',
                '1' => '訪客',
                '2' => '其他',
                '3' => '學生',
                '4' => '居留',
            ];

            return (isset($map[$value])) ? $map[$value] : 'ERROR';

        case 'accommodation':
            $map = [
                '0' => '宿舍',
                '1' => '與親人居住',
                '2' => '租住樓宇',
            ];

            return (isset($map[$value])) ? $map[$value] : 'ERROR';

        case 'depRefEntry1':
        case 'depRefVisa1':
        case 'depChgName1':
        case 'depRefEntry2':
        case 'depRefVisa2':
            $map = [
                '0' => 'Y',
                '1' => 'N',
            ];

            return (isset($map[$value])) ? $map[$value] : 'ERROR';

        case 'depChgName2':
            $map = [
                '0' => 'Y',
            ];

            return (isset($map[$value])) ? $map[$value] : 'ERROR';
    }

    return $value;

}

/**
 * Write excel file
 *
 * @param $excelFile
 * @param $data
 * @param $header
 *
 * @return void
 * @throws \PhpOffice\PhpSpreadsheet\Exception
 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
 */
function writeExcel($excelFile, $data, $header) {
    // Check if excel file exists or a new one is created.
    if (! file_exists($excelFile)) {
        // Create new spreadsheet and add header.
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet1 = $spreadsheet->getSheet(0);
        $worksheet1->fromArray($header, null, 'A1');
    } else {
        // Load exist spreadsheet.
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($excelFile);
        $worksheet1 = $spreadsheet->getSheet(0);
    }

    $highestRow = $spreadsheet->getActiveSheet()->getHighestDataRow('A');
    $startCell = 'A' . ($highestRow + 1);
    $worksheet1->fromArray($data, null, $startCell);

    // Change the widths of the columns to be appropriately large for the content in them.
    $worksheets = [$worksheet1];

    foreach ($worksheets as $worksheet) {
        foreach ($worksheet->getColumnIterator() as $column) {
            $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }

    // Save to file.
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($excelFile);
}


/**
 * @return string[]
 */
function exclude_fields() {
    return [
        'undefined_4',
    ];
}


function decryptPdf($inputPdf, $tempDir, $random) {
    // qpdf --decrypt ID995A-filled.pdf ID995A-decrypted.pdf

    $decryptedFile = sprintf('%s/%s', $tempDir, $random . '-decrypted.pdf');

    $command = sprintf('/usr/bin/qpdf --decrypt "%s" "%s"', $inputPdf, $decryptedFile);
    $escapedCommand = escapeshellcmd($command);
    exec($escapedCommand, $output, $resultCode);

    if ($resultCode !== 0) {
        //something wrong
        echo join('', $output);
        die();
    }

    return $decryptedFile;
}

function cleanup() {
    //delete decrypted file

    //move successfully processed file to processed folder


}
