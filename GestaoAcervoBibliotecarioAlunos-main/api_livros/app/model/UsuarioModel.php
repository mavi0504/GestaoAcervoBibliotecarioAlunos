<?php

class UsuarioModel {
    // chave de conexao ao DB
    private $db;

    public function __construct($db) {
        //pega a chave do DB ($db) e armazena no atributo $db
        $this->db= $db;
    }

    public function loginUser($email, $senha) {
        //receber email e senha decodificado do JSON
        $stmt = $this->db->prepare("
            SELECT * FROM usuarios
            WHERE email = :email AND senha = :senha
        ");
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $senha);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>