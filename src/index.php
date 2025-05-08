<?php

header("Content-Type: application/json");

// Conexão com o banco de dados MySQL
$host = 'mysql';
$user = 'meu_usuario';
$pass = 'minha_senha';
$db = 'meu_banco';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => "Erro na conexão: " . $conn->connect_error]));
}

// Obtém a requisição
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Ajusta a rota com base na posição do script
$scriptName = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$apiPath = array_slice($requestUri, count($scriptName));

// Rotas da API
if ($method == 'GET' && count($apiPath) == 1 && $apiPath[0] == 'users') {
    $result = $conn->query("SELECT * FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[$row['id']] = $row;
    }
    echo json_encode($users);

} elseif ($method == 'POST' && count($apiPath) == 1 && $apiPath[0] == 'users') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = uniqid();
    $stmt = $conn->prepare("INSERT INTO users (id, name, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $id, $input['name'], $input['email']);
    $stmt->execute();
    echo json_encode(["message" => "Usuário criado", "id" => $id]);

} elseif ($method == 'GET' && count($apiPath) == 2 && $apiPath[0] == 'users') {
    $id = $apiPath[1];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo $result->num_rows > 0 ? json_encode($result->fetch_assoc()) : json_encode(["error" => "Usuário não encontrado"]);

} elseif ($method == 'PUT' && count($apiPath) == 2 && $apiPath[0] == 'users') {
    $id = $apiPath[1];
    $input = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sss", $input['name'], $input['email'], $id);
    $stmt->execute();
    echo $stmt->affected_rows ? json_encode(["message" => "Usuário atualizado"]) : json_encode(["error" => "Usuário não encontrado"]);

} elseif ($method == 'DELETE' && count($apiPath) == 2 && $apiPath[0] == 'users') {
    $id = $apiPath[1];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    echo $stmt->affected_rows ? json_encode(["message" => "Usuário deletado"]) : json_encode(["error" => "Usuário não encontrado"]);

} else {
    echo json_encode(["error" => "Rota não encontrada"]);
}

$conn->close();
?>
