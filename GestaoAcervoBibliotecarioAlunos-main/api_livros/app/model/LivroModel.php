<?php

class LivroModel {
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function buscarLivros() {
        $stmt = $this->db->query("
            select 
                l.id_livro, l.titulo, l.descricao, l.autor,
                e.quantidade_atual
            as estoque
            from livros l 
            inner join estoque e 
            on l.id_livro = e.id_livro
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>