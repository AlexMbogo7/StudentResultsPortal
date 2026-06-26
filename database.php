<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$db_server = "localhost";
$db_username = "root"; 
$db_password = "";     
$db_name = "studentsresultportal";
$conn = "";


$conn =mysqli_connect($db_server, $db_username, $db_password, $db_name);

?>
