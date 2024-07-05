<?php

require_once 'Workday.php';

$workday = new Workday();
$filename = 'report.csv';

$report = fopen($filename, 'w');
fputcsv($report, ['#', 'Arrived at', 'Leaved at', 'Required work off', 'Worked off']);
foreach ($workday->getWorkDayList() as $day) {
    fputcsv($report, $day);
}

fclose($report);

if (file_exists($filename)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    exit;
}
?>