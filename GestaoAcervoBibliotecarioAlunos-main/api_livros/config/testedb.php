<?php

require_once 'db.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    if ($db) {
        echo "Sucesso: A ponte com o Banco de Dados funcionou";
    }
} catch (Exception $e) {
    echo "Erro: Algo deu errado na tentativa de conexao.";
}

?>