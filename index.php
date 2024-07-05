<?php

declare(strict_types=1);

ini_set('display_errors', 1);

require_once 'Workday.php';

$workDay = new Workday();
require_once 'view.php';

if (!empty($_POST)) {
    if (isset($_POST['arrived_at'])) {
        $result = $workDay->totalReport($_POST);
        if ($result['status'] === 'failed') {
            echo "<div class='text-center text-danger'>{$result['message']}</div>";
        } elseif ($result['status'] === 'success') {
            echo "<div class='text-center text-success'>{$result['message']}</div>";
        }
    }
}
if (isset($_POST['done'])) {
    $daily_id = (int)$_POST['done'];
    $result = $workDay->update($daily_id);
    if ($result['status'] === 'failed') {
        echo "<div class='text-center text-danger'>{$result['message']}</div>";
    } elseif ($result['status'] === 'success') {
        echo "<div class='text-center text-success'>{$result['message']}</div>";
    }
}
?>