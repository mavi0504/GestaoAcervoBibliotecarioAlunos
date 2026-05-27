create database livro_db;
use livro_db_v2;

create table Usuarios
(
id_usuario INT primary key auto_increment,
nome varchar(100) NOT NULL,
sobrenome varchar(100) NOT NULL,
email varchar(150) NOT NULL UNIQUE,
senha varchar(255) NOT NULL
);
