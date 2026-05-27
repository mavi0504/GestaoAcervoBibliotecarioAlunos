<?php

require_once __DIR__ . '/../model/LivroModel.php';
require_once __DIR__ . '/../view/LivroView.php';

class LivroController{
    private $modelLivro;
    private $viewLivro;
    
    public function __construct($db){
        //conectar no DB e consultar Livros existentes
        $this->modelLivro = new LivroModel($db);
        //Exibir os Livros para o Front-end
        $this->viewLivro = new LivroView();
    }

    public function getLivros(){
        $livros = $this->modelLivro->buscarLivros();
        $this->viewLivro->sendResponse($livros);
    }
}
?>