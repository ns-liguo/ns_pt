<?php
$servername = "8.218.80.95";
$username = "ns_pt";
$password = "cGStxxsrm3PHLpHG";
$dbname = "ns_pt";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}?>