<?php
if(isset($_GET['montar_tabela']) && $_GET['montar_tabela'] === true){
	// Montagem da tabela
	if(!isset($_SESSION['nome'])){
		header('Location: proibido.php');
		exit;
	}
	$pagina = $_GET['pagina'];
	$parametros = $_GET['parametros'];
	$oculta = $_GET['oculta'];
	$id_tabela = $_GET['id_tabela'];
	$temPaginacao = ($_GET['tem_paginacao']) ? ('true') : ('false');
	$limite = $_GET['limite'];
	
	if($oculta === true){
		$classe_tabela = 'tabelaaberta oculta';
	} else {
		$classe_tabela = 'tabelaaberta';
	}
	if(!empty($id_tabela)){
		$atributo_id = "id='$id_tabela'";
	} else {
		$atributo_id = '';
	}
	
	$jogo_lista = (isset($_GET['jogo_lista'])) ? ($_GET['jogo_lista']) : ('');
	?>
	<div <?php echo $atributo_id ?> class="<?php echo $classe_tabela ?>" data-pagina="<?php echo $pagina ?>"
		data-parametros="<?php echo $parametros ?>" data-ordenacao="1" data-filtragem="asc" data-paginacao="<?php echo $temPaginacao ?>"
		data-limite="<?php echo $limite ?>">
		<button type="button" id="novo_registro" onclick="jForm('termo_dicionario_form.php', 'jogo=<?php echo $jogo_lista ?>')" class="btn btn-primary pull-right">
			<i class="fa fa-plus fa-lg"></i>
			Novo
		</button>
		<table>
			<thead>
				<tr>
					<th>Termo Original</th>
					<th>Termo Traduzido</th>
					<th width="110" class="acoes">Ações</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<th colspan="3">Total de termos de dicionário para este jogo: <span class="total_consulta"></span></th>
				</tr>
			</tfoot>
		</table>
		<div class="acoes">
			<button type="button" title="Editar" data-onclick="jForm('termo_dicionario_form.php', 'id={id}')">
				<i class="fa fa-edit fa-lg"></i>
			</button>
			<button type="button" title="Excluir" data-onclick="apagaRegistro('termo_dicionario_crud.php', {id}, 'Excluir este termo de dicionário?', true)" class="btn btn-danger">
				<i class="fa fa-trash fa-lg"></i>
			</button>
		</div>
	</div>
	<?php
} else {
	// Obtenção dos dados da tabela, via Ajax
	session_start();
	if(!isset($_SESSION['nome'])){
		$array = array("error"=>"expired");
		echo json_encode($array);
		exit;
	}
	require_once '../../utils/autoload.php';

	$termoDicionario = new termoDicionario();

	$jogo_lista = (isset($_GET['jogo_lista'])) ? ($_GET['jogo_lista']) : ('');
	$termo_original_lista = (isset($_GET['termo_original_lista'])) ? ($_GET['termo_original_lista']) : ('');

	$total_geral = $termoDicionario->getTotal();
	$total_consulta = $termoDicionario->getTotalByListagem($jogo_lista, $termo_original_lista);

	$dados_requisicao = tabela::interpretarDados($_GET, $total_consulta);

	$numero_de_pesquisas = $dados_requisicao['numero_de_pesquisas'];
	$colunas = $dados_requisicao['colunas'];
	$ordenacao = $dados_requisicao['ordenacao'];
	$filtragem = $dados_requisicao['filtragem'];
	$limit = $dados_requisicao['limit'];
	$offset = $dados_requisicao['offset'];

	// Consulta de pesquisa da tabela
	$termoDicionario_rs = $termoDicionario->getByListagem($jogo_lista, $termo_original_lista, $ordenacao, $filtragem, $limit, $offset);

	// Colunas da tabela a serem retornadas via array para o componente (para ações)
	$dados_coluna = array('id');

	// Formatação dos dados da pesquisa na tabela, para o formato do componente
	$dados_tabela = tabela::formatarDadosTabela($termoDicionario_rs, $colunas, $dados_coluna);

	// Exibição dos dados de retorno, encodados em JSON
	echo tabela::encodarRetornoJSON($numero_de_pesquisas, $total_geral, $total_consulta, $dados_tabela, $ordenacao, $filtragem, $limit, $offset);
}
?>