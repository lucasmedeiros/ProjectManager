<?php

	/* Arquivo que vai incluir o código de permissão para o usuário */

	include 'conexao.php';

	function permitir($codigoP, $codigoN) {
		global $connection;
		// Pega a variável global importada do arquivo conexao.php

		$pode = true;
		// valor que será retornado

		$query = "select n.*
				from Participante pt, Notificacao n, Login l
				where pt.codLogin = l.codigo
				and n.codigoPart = pt.codPart
				and n.codNot = $codigoN
				and l.login=\"$codigoP\";";

		$result = mysqli_query($connection, $query);

		$contador = mysqli_num_rows($result); // Criando para contar o número de ocorrências

		if ($contador == 0) {
			$pode = false;
		}
		
		return $pode;
	}
?>