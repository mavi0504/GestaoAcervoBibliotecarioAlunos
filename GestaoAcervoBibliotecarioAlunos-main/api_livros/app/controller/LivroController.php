<?php

require_once "../app/model/LivroModel.php";
require_once "../app/view/LivroView.php";
require_once "../app/model/EstoqueModel.php";

class LivroController{
    private $modelLivro;
    private $viewLivro;
    private $modelEstoque;
    private $db;
    
    public function __construct($db){
        $this->db = $db;
        //conectar no DB e consultar Livros existentes
        $this->modelLivro = new LivroModel($db);
        //Exibir os Livros para o Front-end
        $this->viewLivro = new LivroView();
        //[SPRINT8] Implementar Criar Livro
        $this->modelEstoque = new EstoqueModel($db);
    }

    public function getLivros(){
        $livros = $this->modelLivro->buscarLivros();
        $this->viewLivro->sendResponse($livros);
    }

    //[SPRINT7] Implementa Filtro Livros
    public function getLivrosPeloTitulo(){
        $titulo = $_GET['titulo'];
        if (isset($titulo)){
            $data = $this->modelLivro->getLivrosPeloTitulo($titulo);
            $this->viewLivro->sendResponse($data, 200);
        }else {
            $this->viewLivro->sendResponse([
                'message' => 'Título inválido.'
            ] , 400);
        }
    }

    //[Sprint9] Implementa o Editar Livro
    public function getLivrosPeloId(){
        $id = $_GET['id'] ?? null;
        if (isset($id)){
            $livro = $this->modelLivro->getLivroPeloId($id);
            $this->viewLivro->sendResponse($livro, 200);
        }else{
            $this->viewLivro->sendResponse(
                ['message' => 'Id invalido'],
                400
            );
        }
    }

    //[SPRINT8] Implementa Novo Livro
    public function createLivro() {
        $data = json_decode(file_get_contents("php://input"), true);

        if ( isset($data['titulo']) && 
            isset($data['descricao']) && 
            isset($data['autor']) ) {
            
            try {
                $this->db->beginTransaction();
                $idLivro = $this->modelLivro->createLivro(
                    $data['titulo'],
                    $data['autor'],
                    $data['descricao']
                );

                if (!$idLivro){
                    throw new Exception('Nao foi possivel inserir o Livro');
                }
                
                $estoqueCriado = $this->modelEstoque->createEstoque($idLivro, 0);

                if (!$estoqueCriado){
                    throw new Exception('Nao foi possivel inserir o Estoque inicial do Calori!');
                }

                $this->db->commit();

                //[Sprint8] inserido codigo HTTP_RESPONSE 201 - Registro criado com sucesso
                $this->viewLivro->sendResponse([
                    'message' => 'Livro criado com sucesso!',
                    'id_livro' => $idLivro
                ], 201);
                
            } catch(Throwable $e){
                if ($this->db->inTransaction()){
                    $this->db->rollback();
                }

                $this->viewLivro->sendResponse([
                    'message' => 'Erro ao cadastrar Novo Livro',
                    'detalhe' => $e->getMessage()
                ], 400);
            }
        } else {
            $this->viewLivro->sendResponse(
                ['message' => 'Dados invalidos!'],
                400
            );
        } 
    }

    //[SPRINT9]
    public function updateLivro(){
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id']) && isset($data['titulo']) && isset($data['autor']) && isset($data['descricao'])){
            $result = $this->modelLivro->updateLivro($data['id'], $data['titulo'], $data['autor'], $data['descricao']);
            $this->viewLivro->sendResponse([
                'message' => 'Livro atualizado com sucesso'
            ], 200);
        }else{
            $this->viewLivro->sendResponse([
                'message' => 'Erro: Confira se os campos foram enviados corretamente.'
            ], 400);
        }
    }

    public function deleteLivro(){
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])){
            $this->modelLivro->deleteLivro($data['id']);
            $this->viewLivro->sendResponse([
                'message' => 'Livro Deletado!'
            ], 200);
        }else{
            $this->viewLivro->sendResponse([
                'message' => 'Erro ao Deletar Livro. Confira se os campos foram preenchidos'
            ], 400);
        }
    }

}
?>