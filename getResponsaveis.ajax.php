<?php
	header( 'Cache-Control: no-cache' );
	header( 'Content-type: application/xml; charset="utf-8"', true );

	include 'restrito.php';
	// Incluindo o arquivo que garante a restrição da página apenas a usuários logados
	include 'conexao.php';
	// Incluindo o arquivo de conexão ao banco de dados

	$proj = mysqli_real_escape_string($connection, $_REQUEST['proj']);

	$pts = array();

	$sql = "select pt.codPart, l.nomeCompleto
			from Participante pt, Login l
			where pt.projeto = $proj
			and pt.codLogin = l.codigo
			order by l.nomeCompleto;";

	$res = mysqli_query($connection, $sql);
	while(($row = mysqli_fetch_array($res)) != null) {
        $pts[] = array(
			'responsavel'	=> $row['codPart'],
			'nome'			=> $row['nomeCompleto'],
		);
    }

	echo( json_encode( $pts ) );