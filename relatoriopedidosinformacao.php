<?php
$db_host = 'localhost'; // Server Name
$db_user = 'root'; // Username
$db_pass = ''; // Password
$db_name = 'Fretes'; // Database Name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());	
}

$pedido = $cliente = $codcliente = $estoque = $transportador = $ano = $mes = $mensagem = $titulo ='';

if(isset($_GET["pedido"])){$pedido = $_GET["pedido"];}
if(isset($_GET["cliente"])){$cliente = $_GET["cliente"];}
if(isset($_GET["codcliente"])){$codcliente = $_GET["codcliente"];}
if(isset($_GET["estoque"])){$estoque = $_GET["estoque"];}
if(isset($_GET["transportador"])){$transportador = $_GET["transportador"];}
if(isset($_GET["mes"])){$ano = substr($_GET["mes"],0,4);$mes = substr($_GET["mes"],5,2); } 


if($ano != ''){$mensagem = $mensagem.' Periodo: '.$mes.'/'.$ano;};

$estoque2 = $estoque;  //erro estoque não identificava

// faz SQL que abastece variavel com os pedidos adicionados como estoque no banco, separados por virgula

IF($estoque =! ''){
	$pedidosestoque = '';
	
	$sql = "Select * From Estoque";
	
	$query = mysqli_query($conn, $sql);
	
	$numResults = mysqli_num_rows($query);
	$counter = 0;
	
	while ($row = mysqli_fetch_array($query)){
		if (++$counter == $numResults) {
			// last row
			$pedidosestoque = $pedidosestoque.''.$row['estpedido']."";
		} else {
			// not last row
			$pedidosestoque = $pedidosestoque.''.$row['estpedido'].",";
		}

	}
}
 
//sql base
 
// MySQL Query to get data
$sql = "SELECT
i.nfipedido as PEDIDO,

Case i.nfipedido When
0 then 'Diversos'
else i.nfidescricao
END as MATERIAL,

Case i.nfipedido When
0 then 'Diversos'
else n.nfclientepedido
END as CLIENTE,

COUNT(DISTINCT n.nfnumero) as QTD_Notas,

Count(case when c.confrete is null then 1 end) as Fretes_Zerados,

sum(i.nfitotal) as Valor_Enviado,

c.contransportador as Transportador,

Cast(coalesce(sum((c.confrete)*(i.nfiporcentovalor/100)),0) as DECIMAL(7,2)) as Frete,

Cast(SUM(i.nfiporcentoenvio)as DECIMAL(5,2)) as Porcento_Envio,

Cast(coalesce(((co.cobvalor+co.cobfretedestacado)*sum(i.nfiporcentoenvio)/100),0) as DECIMAL(7,2)) as Frete_Orcado,

Cast(coalesce(((coalesce(co.cobvalor+co.cobfretedestacado,0)*SUM(i.nfiporcentoenvio)/100)
- coalesce(sum((c.confrete)*(i.nfiporcentovalor/100)),0)),0) as DECIMAL(7,2))
as Diferenca

FROM notasfiscaisitens i
LEFT JOIN notasfiscais n on n.nfnumero = i.nfinota
LEFT JOIN conhecimentos c on c.connota = n.nfnumero
LEFT JOIN cobrancas co on co.cobpedido = i.nfipedido
WHERE
n.nfcodempresa not in (15,9551,5094)
and i.nfipedido > 0 ";

if($pedido != ''){ $sql = $sql." and i.nfipedido in (".$pedido.")";}
if($cliente != ''){ $sql = $sql." and n.nfclientepedido like ('%".$cliente."%')";}
if($codcliente != ''){ $sql = $sql." and n.nfcodempresa in (".$codcliente.")";}

switch ($estoque2) { 
    case '':
		$sql = $sql." and i.nfipedido not in (".$pedidosestoque.")";
        //echo "NAO";
        break;
		
	case 'Somente':
		$sql = $sql." and i.nfipedido in (".$pedidosestoque.")";
        //echo "NAO";
        break;
}

switch ($transportador) { 
    case '':
		$sql = $sql." and n.nftransportador not in (15,5160,4868,29262)";
        //echo "SIM";
        break;
}

if($mes != ''){ $sql = $sql." and n.nfemissao between '".$ano."-".$mes."-01' and '".$ano."-".$mes."-31' ";}

//finaliza o sql
$sql = $sql." group by Pedido desc";

//echo $sql;
		
$query = mysqli_query($conn, $sql);

if (!$query) {
	die ('SQL Error: ' . mysqli_error($conn));
}

if($estoque2 == "S"){ $titulo = $titulo.' COM Estoque ';}
if($estoque2 == ""){ $titulo = $titulo.' SEM Estoque ';}
if($estoque2 == "Somente"){ $titulo = $titulo.' SOMENTE PEDIDOS DE ESTOQUE ';}
if($transportador == ""){ $titulo = $titulo.', SEM envios via Gráfica/Favero/Viaje Seguro';}
if($transportador == "S"){ $titulo = $titulo.', COM Todos Transportadores';}
?>

<html>

	<title>Relatório de Pedidos</title>
	<h3 align="center">Relatório de Pedidos - <font face='Calibri' color='blue'><?=$titulo?></font></h3>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">

	<table id="example" class="display compact cell-border custom" >
		<thead class="dt-center">
			<tr>	
				<th><div>PEDIDO</div></th>
				<th><div>CLIENTE</div></th>
				<th><div>MATERIAL</div></th>
				<th><div>QTD NOTAS</div></th>
				<th><div>FRETES ZERADOS</div></th>
				<th><div>VALORES ENVIADOS</div></th>
				<th><div>CUSTO FRETE</div></th>
				<th><div>% ENVIO</div></th>
				<th><div>FRETE ORÇADO</div></th>
				<th><div>DIFERENÇA</div></th>
			</tr>
		</thead>
		<tbody class="dt-center">
		<?php
		
		$total = $orcado = $custo = 0;
		while ($row = mysqli_fetch_array($query))
		{
			
			$valor_enviado  = $row['Valor_Enviado'] == 0 ? '' : number_format($row['Valor_Enviado'],2,',','');
			$frete  = $row['Frete'] == 0 ? '' : number_format($row['Frete'],2,',','');
			$porcento  = $row['Porcento_Envio'] == 0 ? '' : number_format($row['Porcento_Envio'],2,',','');
			$frete_orcado  = $row['Frete_Orcado'] == 0 ? '' : number_format($row['Frete_Orcado'],2,',','');
			$diferenca  = $row['Diferenca'] == 0 ? '' : number_format($row['Diferenca'],2,',','');

			
			echo '<tr>
					<td>'.$row['PEDIDO'].'</td>
					<td>'.utf8_encode($row['CLIENTE']).'</td>
					<td>'.utf8_encode($row['MATERIAL']).'</td>
					<td>'.$row['QTD_Notas'].'</td>
					<td>'.$row['Fretes_Zerados'].'</td>
					<td>'.$valor_enviado.'</td>
					<td>'.$frete.'</td>
					<td>'.$row['Porcento_Envio'].'</td>
					<td>'.$frete_orcado.'</td>
					<td>'.$diferenca.'</td>
				</tr>';
			$total += $row['Diferenca'];
			$orcado += $row['Frete_Orcado'];
			$custo += $row['Frete'];
		}?>
		</tbody>
	
	</table> 
	
	<span class="right"><font size="5" face="Calibri"><strong>Total de Frete: R$ <?=number_format($custo,2,',','')?> | Total Orçado: R$ <?=number_format($orcado,2,',','')?> = RESULTADO: R$ <?=number_format($total,2,',','')?>   </strong></font></span>
	
	<span class="left"><font size="5" face="Calibri"><strong><?=$mensagem?>   </strong></font></span>​	
	
	<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>
	
	<style type="text/css">
		.custom {
			font-size: 14px;
			font-family: Calibri;
		}
		.right{
			float:right;
		}

		.left{
			float:left;
		}
		h3 {
			font-size: 23px;
			margin:0
		}

	</style>
	
	<script>
	var newwindow;
	var t = window.screen.availWidth/1.15;
	var cem = 100.00;
	
	
	function createPop(url, name)
	{    
		newwindow=window.open(url,name,'width='+t+',height=500,toolbar=0,menubar=0,location=0,resizable=no');  
		if (window.focus) {newwindow.focus()}
	}
$(document).ready(function() {
	 $.fn.dataTable.moment( 'DD/M/YYYY' );
	
    $('#example').DataTable( {
		"dom": '<"top"if>t',
		//Para imprimir deixar os dois campos abaixo como comentario
		scrollY:        '78vh',  
        scrollCollapse: true,
		"order": [[ 0, "desc" ]],
		"paging":   false,

        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Portuguese-Brasil.json",
	   },
	   "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
		rowCallback: function(row, data, index){
			if(data[9]< "0"){
				$(row).find('td:eq(9)').css('color', 'red');
				$(row).find('td:eq(9)').css('font-weight', 'bold');
			}
			if(data[4]> "0"){
				$(row).find('td:eq(4)').css('font-weight', 'bold');
				$(row).find('td:eq(4)').css('background-color', '#ffff99');
			}
			if(data[7] > cem){
				$(row).find('td:eq(7)').css('font-weight', 'bold');
				$(row).find('td:eq(7)').css('background-color', 'LightSalmon ');
				$(row).find('td:eq(7)').css('font-size', '18');
			}
			if(data[7] == cem){
				$(row).find('td:eq(7)').css('font-weight', 'bold');
				$(row).find('td:eq(7)').css('background-color', '#cce6ff');
			}

			$(row).find('td:eq(0)').css('font-weight', 'bold');
			$(row).find('td:eq(0)').css('font-size', '18');
			$(row).find('td:eq(9)').css('font-weight', 'bold');
			$(row).find('td:eq(9)').css('font-size', '16');
		}
		
    });
	
		
	 //CLicar no Pedido
	var table = $('#example').DataTable();
 
		$('#example tbody').on( 'click', 'td', function () {
			if ($(this).index() != 0 ) { // provide index of your column in which you prevent row click here is column of 4 index
             return;
         }{
			//alert( table.cell( this ).data() );
			createPop('relatorionotasinformacao.php?pedido='+table.cell( this ).data(),'Pedido: '+table.cell( this ).data());
			//createPop('relatoriopedidosespecifico.php','Pedido: ');
		 }
		} );
} );
</script>
</html>



