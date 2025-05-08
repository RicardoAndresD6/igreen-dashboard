<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// include_once './src/config/config.php';
// include_once './src/config/firebaseRDB.php';

// $firebase = new firebaseRDB(FIREBASE_RDB['url']);
// $data = $firebase->retrieve("igreen");

// if ($data) {
//     echo json_encode($data);
// } else {
//     echo json_encode(["error" => "No data"]);
// }

header('Content-Type: application/json');
echo json_encode(["suelo1" => 50, "suelo2" => 60, "humedad" => 70, "temperatura" => 25, "fecha" => date("Y-m-d H:i:s")]);
?>