<?php  
require '../config_default.php';

$servername = DB_HOST; 
$username = DB_USERNAME;
$password = DB_PASSWORD;
$schema = DB_NAME; 

try {
  $conn = new PDO("mysql:host=$servername;dbname=$schema", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";

} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>
