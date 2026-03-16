<?php
// Define o cabeçalho como JSON
header('Content-Type: application/json');
// Permite requisições de qualquer origem (CORS) - ajuste conforme necessidade de segurança
header('Access-Control-Allow-Origin: *');

// Obtém o CPF da query string (ex: api.php?cpf=12345678900)
// Se a chamada for via path (ex: api.php/123...), precisaria de outra lógica, 
// mas vamos ajustar o frontend para passar como parametro GET para compatibilidade máxima.
$cpf = isset($_GET['cpf']) ? $_GET['cpf'] : '';

// Remove caracteres não numéricos apenas para garantir
$cpf = preg_replace('/\D/', '', $cpf);

if (empty($cpf)) {
    http_response_code(400);
    echo json_encode(['error' => 'CPF não fornecido']);
    exit;
}

// URL da API externa fornecida
$apiUrl = "https://base1.sistemafull.site:80/api/cpf1?CPF=" . $cpf;

// Inicializa o cURL
$ch = curl_init();

// Configurações do cURL
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retornar o resultado como string
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redirecionamentos se houver
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignorar verificação SSL (útil para dev/apis sem cert válido)
curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Timeout de 15 segundos

// Executa a requisição
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

// Verifica erros de conexão
if ($response === false) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro ao conectar na API externa',
        'details' => $curlError
    ]);
    exit;
}

// Retorna a resposta da API externa exatamente como recebida
// O frontend já está preparado para tratar o JSON retornado por essa API
// (estrutura com campos NOME, CPF, NASC, etc.)
http_response_code($httpCode);
echo $response;
?>