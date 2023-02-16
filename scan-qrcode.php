<?php
require __DIR__ . '/vendor/autoload.php';

use Zxing\QrReader;
$inputPdf = $argv[1];

$tempDir = __DIR__ . '/temp';
check_temp_directory('temp');

$random = bin2hex(random_bytes(10));

// Convert pdf to image using pdftocairo `pdftocairo -jpeg visa_example.pdf visa-1.jpg`
$command = sprintf('/usr/bin/pdftocairo -singlefile -jpeg "%s" "%s/%s"', $inputPdf, $tempDir, $random);
$escapedCommand = escapeshellcmd($command);
exec($escapedCommand, $output, $resultCode);

if ($resultCode !== 0) {
    // Something wrong, output error
    echo join('', $output);
    die();
}

$outputJpg = sprintf('%s/%s.jpg', $tempDir, $random);

if (file_exists($outputJpg)) {
    try {
        $result = readQrCodeFromImage($outputJpg);
        if ($result !== false) {
            echo $result;
        }
    } catch (Exception $e) {
        echo "Something when wrong reading qr code";
    } finally {
        unlink($outputJpg);
    }
}

function check_temp_directory($path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function readQrCodeFromImage($imagePath) {
    if ($imagePath) {
        $qrcode = new QrReader($imagePath);
        return $qrcode->text();
    }
}
