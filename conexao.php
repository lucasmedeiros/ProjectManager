<?php
	// Este arquivo é importado em todas as páginas do site, já que em todas haverá conexão com o banco

	header('Content-Type: text/html; charset=utf-8');
	// Informa qual o conjunto de caracteres que será usado.

	// Estabelecendo a conexão com o banco de dados MySQL...
    header ('Content-type: text/html; charset=UTF-8');

	$con = "localhost"; // Aí está a conexão local, se quiser mudar, basta adicionar o IP
	$id = "root"; // Seu id do MySQL
	$pass = ""; // Sua senha do MySQL
	$db = "projectmanager";
	// Base de dados (se executou o script que foi enviado neste projeto, deve-se chamar 'projectmanager' mesmo)
	// O script está no arquivo 'script.sql'

	$connection = mysqli_connect($con,$id,$pass,$db); // Estabelecer a conexão

	// Pegar todos os caracteres possíveis, passando pra UTF-8
    mysqli_set_charset($connection,"utf8");

?>