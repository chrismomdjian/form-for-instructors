<?php
// PDO login credentials
$server = "localhost";
$username = "root";
$password = "pythonisgreat123#";
$database = "test";

try {
  $conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
} catch (PDOException $e) {
  die("Could not connect: " . $e->getMessage());
}
