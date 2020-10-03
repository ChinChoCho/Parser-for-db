<?php

/**
 *    ИНИЦИАЛИЗАЦИЯ ПОДКЛЮЧЕНИЯ К БД
 */

$dblocation = "127.0.0.1";
$dbname = "test";
$dbuser = "root";
$dbpassword = "root";

//соединение с бд
$db = mysqli_connect($dblocation, $dbuser, $dbpassword);

//проверка на ошибку
if (!$db) {
   echo "Ошибка доступа к Mysql";
   exit();
}

// Устанавливает кодировку по умолчанию для текущего соединения
mysqli_set_charset($db, "utf8");

if (!mysqli_select_db($db, $dbname)) {
   echo "Ошибка доступа к базе данных: ($dbname)";
   exit();
}