<?php
require __DIR__ . '/vendor/autoload.php';

use mikehaertl\pdftk\Pdf;

$pdf = new Pdf('ID995A-decrypted.pdf');

$result = $pdf->fillForm([
  'engSurname' => 'Doeä',
  'engName' => 'John',
  'chnName' => '無名氏 no name',
  'sex' => '1',
  'dobDay' => '01',
  'dobMth' => '10',
  'dobYr' => '1990',
  'national' => 'Kong Kong',
  'hkidAlpha' => 'K',
  'hkidDigit' => '234567',
  'hkidChkD' => '8',
  'mainlandID' => '000000',
  'travelDocType' => 'Passport',
  'travelDocNo' => '000000',
  'appEmail' => 'test@example.com',
])
//->replacementFont('/usr/share/fonts/truetype/noto/NotoSansMono-Regular.ttf')
->needAppearances()
//->flatten()
->saveAs('filled-test_3.pdf');

// Always check for errors
if ($result === false) {
  $error = $pdf->getError();

  var_dump($error);
}
