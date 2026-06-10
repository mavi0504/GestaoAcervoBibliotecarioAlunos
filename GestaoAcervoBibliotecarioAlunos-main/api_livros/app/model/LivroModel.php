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

    public function getLivrosPeloTitulo($titulo){
        $stmt = $this->db->prepare("
            select * 
            from livros
            join Estoque on Estoque.id_livro = Livros.id_livro
            where livros.titulo LIKE :titulo
        ");
        $stmt->bindValue(':titulo', '%' . $titulo . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //[Sprint9] Implementa Editar
    public function getLivroPeloId($id){
        $stmt = $this->db->prepare("
        SELECT 
        livros.id_livro,
        livros.titulo,
        livros.descricao,
        livros.autor,
        estoque.id_estoque,
        estoque.quantidade_atual as estoque 
        FROM livros
        JOIN estoque on estoque.id_livro = livros.id_livro 
        WHERE Livros.id_livro = :id;
        ");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);   
    }

    //[SPRINT8] Implementar novo Livro
    public function createLivro($titulo, $autor, $descricao) {
        $stmt = $this->db->prepare("
            INSERT INTO Livros (TITULO, AUTOR, DESCRICAO)
            VALUES (:titulo, :autor, :descricao)
        ");
        $stmt->bindValue(':titulo', $titulo);
        $stmt->bindValue(':autor', $autor);
        $stmt->bindValue(':descricao', $descricao);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        };
        return false;
    }
}

?>