<!-- 

	TODOS OS REDIRECIONAMENTOS A PARTIR DO "WINDOW.LOCATION" NA PARTE DO JAVASCRIPT SÃO EXCLUSIVAMENTE
	PLANEJADOS, QUANDO O USUÁRIO ESTIVER LOGADO, ELE NÃO VAI REDIRECIONAR PARA A PÁGINA DE LOGIN, JÁ QUE
	VAI EXISTIR SESSION ATIVA, E ISSO FAZ COM QUE O USUÁRIO SEJA REDIRECIONADO AUTOMATICAMENTE PARA SEU
	INDEX AO OCORRER O CADASTRO DE UM NOVO USUÁRIO ADMINISTRADOR.

-->

<?php
	// Conexão com o banco de dados
  	include 'conexao.php';
  	
	$login = $_POST['login'];
	$email = $_POST['email'];
	$completo = $_POST['completo'];
	$senha = $_POST['senha'];
	$reSenha = $_POST['reSenha'];
	$idade = $_POST['idade'];
	$cpf = $_POST['cpf'];
	$cidade = $_POST['cidade'];
	$uf = $_POST['uf'];
	$superuser = $_POST['superuser'];
	// Pegando os dados do formulário via POST[]

	if ((strcmp($senha, $reSenha)) != 0) {
		// Verificando se os campos de senha conferem
		?>
			<script>
				alert("Senhas não conferem!");
				window.location="cadastro.php";
			</script>
		<?php
	} else {
		if (strlen($senha) < 6 || strlen($senha) > 14) {
			// Validação da senha 
			?>
				<script>
					alert("Sua senha deve ter entre 6 e 14 caracteres!");
					window.location="cadastro.php";
				</script>
			<?php
		} else {
			if (strlen($cpf) != 14) {
				// Validação do CPF
				?>
					<script>
						alert("CPF inválido!");
						window.location="cadastro.php";
					</script>
				<?php
			} else {
				if ($idade < 16) {
					// Validação da idade
					?>
						<script>
							alert("Você deve ter mais que 16 anos");
							window.location="cadastro.php";
						</script>
					<?php
				} elseif ($idade > 100) {
					// Validação da idade
					?>
						<script>
							alert("Você provavelmente não tem essa idade...");
							window.location="cadastro.php";
						</script>
					<?php
				} else {
					$nome_arquivo = $_FILES['arquivo']['name'];
                
	                $extensao = strtolower(substr($nome_arquivo, -5));
		            // Pegando a extensão da imagem que foi enviada, por exemplo ".jpg"
		            // Pegando as 5 últimas letras e não as 4 porque podem existir arquivos com extensão ".jpeg"
		            // E se algum arquivo for "foto.jpg", por exemplo, o "o" que vier antes não influenciará no acesso à imagem,
		            // mas apenas adicionará um caractere a mais no nome.

	                $novo_nome = md5(time()).$extensao; // md5() para criptografia do nome da imagem

	                $diretorio = "upload/";

	                $query = "insert into Login (login, email, nomeCompleto, admin, senha, foto, uf, idade, cpf, cidade) 
								values (\"$login\",\"$email\",\"$completo\", $superuser, \"$senha\",
								\"$novo_nome\", $uf, $idade, \"$cpf\", \"$cidade\");";
					$resultado = mysqli_query($connection,$query);
					// Fazendo a requisição ao banco de dados

	                if ($resultado) { // Verifica se o banco de dados recebeu a requisição
	                    move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.$novo_nome);
	                    // Transferir o arquivo da foto para a pasta de uploads
	                    ?>
							<script>
								alert("Inserido com sucesso!");
								window.location="login.php";
							</script>
						<?php
	                    // Antes de fazer isso, verificar as permissões da pasta 'upload'
	                } else {
	                    // Prevendo algum erro de banco de dados
	                    ?>
	                        <script type="text/javascript">
	                            alert("Ocorreu o seguinte erro: " + "<?php echo mysqli_error($connection) ?>");
								window.location="cadastro.php";
	                        </script>
	                    <?php
	                }
				}
			}
		}
	}
?>