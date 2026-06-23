<?php
require_once '../app/model/EstoqueModel.php';
require_once '../app/view/EstoqueView.php';

class EstoqueController{
    private $modelEstoque;
    private $viewEstoque;

    public function __construct($db){
        $this->modelEstoque = new EstoqueModel($db);
        $this->viewEstoque = new EstoqueView();

    }

    public function atualizarSaldo() {
        $data = json_decode(file_get_contents("php://input"), true);
        if(
            isset($data['id_livro']) &&
            isset($data['id_usuario']) &&
            isset($data['estoque']) &&
            isset($data['quantidade']) &&
            isset($data['tipo']) &&
            isset($data['data'])
        ){
            $nova_quantidade = $this->calculoQuantidade(
                $data['quantidade'], 
                $data['estoque'], 
                $data['tipo']
            );

            //efetiva a atualizacao da Tabela Estoque no BD
            $this->modelEstoque->updateEstoque($data['id_livro'], $nova_quantidade);
            //Regra de Negocio: Alerta de Estoque menor ou igual a 5
            if ($nova_quantidade <= 5){
                $this->viewEstoque->sendResponse([
                    'message' => 'ATENCAO: Nova quantidade do Estoque menor que o minimo(5)!'
                ], 200);
            }else{
                $this->viewEstoque->sendResponse([
                    'message' => 'Estoque atualizado com sucesso!'
                ], 200);
            }
        }else{
            $this->viewEstoque->sendResponse([
                'message' => 'Erro: Confira se os campos foram enviados corretamente!'
            ], 400);
        }
    }


    public function calculoQuantidade($quantidade, $estoque, $tipo){
        //evitar Estoque negativo
        if($tipo === 'saida' && $quantidade > $estoque){
            $this->viewEstoque->sendResponse([
                'message' => 'Saldo insuficiente!'
            ], 400);
            exit;
        }

        switch ($tipo) {
            case 'entrada':
                $estoque += $quantidade;
                break;
            case 'saida':
                $estoque -= $quantidade;
                break;
        }
        return $estoque;
    }

}
?>