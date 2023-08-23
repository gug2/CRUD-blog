<?php

try
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = mysqli_connect($hostname, $user, $password);
    $dbConnectionStatus = mysqli_select_db($db, $database);
}
catch (Exception $ex)
{
    echo 'Ошибка подключения к базе данных! Обратитесь к администрации.';
    echo '<br>Код ошибки: '. $ex . '<br>';
    echo mysqli_error($db);
}
?>