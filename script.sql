drop schema projectmanager;
create schema projectmanager;
use projectmanager;

# AUTOR: LUCAS DE MEDEIROS NUNES FERNANDES
# MATRÍCULA: 20131104010113

# Criação das tabelas e seus respectivos atributos
create table Uf (
	codUf int primary key auto_increment,
	sigla varchar(2) not null
) auto_increment = 10, ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

create table Login (
	codigo int primary key auto_increment,
    admin int not null,
    email varchar(60) unique not null,
    login varchar(100) unique not null,
    nomeCompleto varchar(100) not null,
    senha varchar(14) not null,
    foto varchar(50) not null,
    uf int not null,
    idade int not null,
    cpf varchar(14) unique not null,
    cidade varchar(50) not null,
    foreign key (uf) references Uf(codUf)
) auto_increment = 20160, ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

create table TipoProjeto (
	codTipo int primary key auto_increment,
    descr varchar(30) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

create table Projeto (
	codProj int primary key auto_increment,
    nome varchar(200) not null,
    tipo int not null,
    dtInicio date not null,
    dtFim date not null,
    descr varchar(1500) not null,
    foreign key(tipo) references TipoProjeto (codTipo)
) auto_increment = 1000,ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

create table Participante (
	codPart int primary key auto_increment,
    codLogin int not null,
    coordenador int not null,
    projeto int not null,
    foreign key (projeto) references Projeto(codProj),
    foreign key (codLogin) references Login(codigo)
) auto_increment = 2000, ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

create table Objetivo (
	codObjetivo int primary key auto_increment,
    codProjeto int not null,
    codParticipante int not null,
    descricao varchar(200) not null,
    tempoEntrega int not null,
    horaInicio datetime not null,
    foreign key(codProjeto) references Projeto(codProj),
    foreign key(codParticipante) references Participante(codPart)
) auto_increment = 10000, ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

create table Notificacao (
	codNot int primary key auto_increment,
    codigoPart int not null,
    ativo int not null,
    msg varchar(500) not null,
    dataNot datetime not null,
    foreign key(codigoPart) references Participante(codPart)
) auto_increment = 1, ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Adicionando UF's
insert into Uf (sigla) value ("AC");
insert into Uf (sigla) value ("AL");
insert into Uf (sigla) value ("AP");
insert into Uf (sigla) value ("AM");
insert into Uf (sigla) value ("BA");
insert into Uf (sigla) value ("CE");
insert into Uf (sigla) value ("DF");
insert into Uf (sigla) value ("ES");
insert into Uf (sigla) value ("GO");
insert into Uf (sigla) value ("MA");
insert into Uf (sigla) value ("MT");
insert into Uf (sigla) value ("MS");
insert into Uf (sigla) value ("MG");
insert into Uf (sigla) value ("PA");
insert into Uf (sigla) value ("PB");
insert into Uf (sigla) value ("PR");
insert into Uf (sigla) value ("PE");
insert into Uf (sigla) value ("PI");
insert into Uf (sigla) value ("RJ");
insert into Uf (sigla) value ("RN");
insert into Uf (sigla) value ("RS");
insert into Uf (sigla) value ("RO");
insert into Uf (sigla) value ("RR");
insert into Uf (sigla) value ("SC");
insert into Uf (sigla) value ("SP");
insert into Uf (sigla) value ("SE");
insert into Uf (sigla) value ("TO");

# Adicionando tipos de projeto
insert into TipoProjeto(descr) value ("Pesquisa");
insert into TipoProjeto(descr) value ("Extensão");
insert into TipoProjeto(descr) value ("Integrador");
insert into TipoProjeto(descr) value ("Desenvolvimento tecnológico");
insert into TipoProjeto(descr) value ("Outro");

# Administrador do sistema 
# [ESTES SÃO O LOGIN E SENHA PRA ENTRAR NO SISTEMA COMO ADMIN PELA PRIMEIRA VEZ]
insert into Login (login, email, nomeCompleto, admin, senha, foto, uf, idade, cpf, cidade) 
	values ("root","root@admin.com","Usuário Administrador", 1, "123456",
			"foto_admin.jpg", 20, 18, "111.111.111-11", "Caicó");

create view getUfs as select * from Uf;
create view getTipos as select * from TipoProjeto;
create view getProjetos as select * from Projeto;