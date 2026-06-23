<?php

// configuracoes de erro
error_reporting(E_ALL);
ini_set('display_errors', 1);

// cabecalho da API:
// definicao para retorno (API) arquivo o JSON
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*'; // API recebe requisicao de qualquer dominio
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    http_response_code(204); //requisicao OK, sem conteudo
    exit;
}

//importacao de codigos
require_once '../config/db.php';
require_once '../app/controller/UsuarioController.php';
require_once '../app/controller/LivroController.php';
//[Sprint 11] Implementa Gestao de Estoque
require_once '../app/controller/EstoqueController.php';

$database = new Database();
$db = $database->getConnection();

// recuperar URL, limpa a URL, e prepara para rota configuracao de rota
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); //limpa URL
$route = basename($path); //captura a rota (/login)
$method = $_SERVER['REQUEST_METHOD']; //captura metodo HTTP (POST)

$livroController = new LivroController($db);

//[Sprint 11] Implementa Gestao de Estoque
$estoqueController = new EstoqueController($db);

try {
    switch ($route) {
        case 'health':
            echo json_encode([
                'status'=>'ok - Sistema Online!'
                ]);
            break;
            http_response_code(200);
        case 'login':
            if ($method === 'POST') {
                //chamar Controller do Usuario para realizar Login
                $usuarioController = new UsuarioController($db);
                $usuarioController->loginUsuario();
            }
            break;
        case 'livro':
            if ($method === 'GET') {
                $livroController->getLivros();
                exit;
            }
            //[Sprint8] removido break
            //break;

            //[SPRINT8] Implementa Criar Novo Livros
            if ($method === 'POST') {
                $livroController->createLivro();
                exit;
            }
            //[Sprint9]
            if ($method === 'PUT'){
                $livroController->updateLivro();
                exit;
            }
            //[Sprint10] Implementa Excluir
            if ($method === 'DELETE'){
                $livroController->deleteLivro();
                exit;
            }
            //[Sprint8] inserirdo mensagem de metodo nao reconhecido
            http_response_code(405); //nao reconhece o metodo
            echo json_encode([
                'error' => "Método não permitido em /livro"
            ]);
            break;
        
        //[SPRINT7] Implementa Filtro Livros
        case 'livroTitulo':
            if ($method === 'GET'){
                $livroController = new LivroController($db);
                $livroController->getLivrosPeloTitulo();
                exit;
            }
            http_response_code(405); //nao reconhece o metodo
            echo json_encode([
                'error' => "Método não permitido!"
            ]);
            break;
        
        //[Sprint9] Implementa Editar Livro
        case 'livroId':
            if ($method === 'GET'){
                //$livroController = new LivroController($db);
                $livroController->getLivrosPeloId();
                exit;
            }
            http_response_code(405); //nao reconhece o metodo
            echo json_encode([
                'error' => "Método não permitido!"
            ]);
            break;
        
        //[Sprint 11] Implementa Gestao de Estoque
        case 'estoque':
            if ($method === 'PUT'){
                $estoqueController->atualizarSaldo();
                exit;
            }
            http_response_code(405); //nao reconhece o metodo
            echo json_encode([
                'error' => "Método não permitido!"
            ]);
            break;

    }
} catch (Throwable $e) {
    http_response_code(500); //Internal Server Error
    echo json_encode([
        'error' => 'Erro interno do servidor',
        'detalhe' => $e->getMessage()
        ]);
}

?>