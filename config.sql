create database cadastroFrete;

use cadastroFrete;

create table usuario (
    id int auto_increment primary key,
    nome varchar(40) not null,
    email varchar(50) not null,
    senha varchar(20) not null
);