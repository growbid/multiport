<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'multiport';

$db = new mysqli($host, $username, $password, $database);

if($db->connect_error) {die('Connection failed: ' . $db->connect_error);}
?>