<?php
header("Content-Type: application/json");
echo json_encode([
    "status" => "healthy",
    "message" => "Souls is running",
    "timestamp" => date("Y-m-d H:i:s"),
    "version" => "2.0.0"
]);
?>