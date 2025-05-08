<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

var_dump($_SERVER);
die();

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "JSON inválido"]);
    exit;
}

$requeridos = ['suelo1', 'suelo2', 'humedad', 'temperatura'];
foreach ($requeridos as $campo) {
    if (!isset($data[$campo])) {
        http_response_code(422);
        echo json_encode(["status" => "error", "message" => "Falta el campo: $campo"]);
        exit;
    }
}

// Guardar en log.txt
$registro = date("Y-m-d H:i:s") . ";";
$registro .= $data['suelo1'] . ";";
$registro .= $data['suelo2'] . ";";
$registro .= $data['humedad'] . ";";
$registro .= $data['temperatura'] . "\n";

file_put_contents("log.txt", $registro, FILE_APPEND);

http_response_code(200);
echo json_encode(["status" => "ok", "mensaje" => "Datos guardados", "datos" => $data]);
?>
