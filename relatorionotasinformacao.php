<?php
$db_host = 'localhost'; // Server Name
$db_user = 'root'; // Username
$db_pass = ''; // Password
$db_name = 'Fretes'; // Database Name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());	
}

$pedido = $cliente = $codcliente = $nota = $ano = $mes = $mensagem = '';

if(isset($_GET["pedido"])){$pedido = $_GET["pedido"];}
if(isset($_GET["nota"])){$nota = $_GET["nota"];}
if(isset($_GET["cliente"])){$cliente = $_GET["cliente"];}
if(isset($_GET["codcliente"])){$codcliente = $_GET["codcliente"];}
if(isset($_GET["mes"])){$ano = substr($_GET["mes"],0,4);$mes = substr($_GET["mes"],5,2); } 

if($ano != ''){$mensagem = ' Periodo: '.$mes.'/'.$ano;};

//sql base
 // MySQL Query to get data
$sql = "SELECT 
n.nfnumero as Nota,
max(i.nfiitem) as Itens,
DATE_FORMAT( n.nfemissao , '%d/%c/%Y' ) as Data, 
n.nfclientepedido as CLIENTE,
n.nftipofrete as MODO,  
n.nfcidade as CIDADE,
n.nfuf as UF,
c.contransportador as Transportador,
 Count(case when c.confrete is null then 1 end) as Fretes_Zerados,
 sum(i.nfitotal) as Valor_Enviado,
Cast(coalesce(sum((c.confrete)*(i.nfiporcentovalor/100)),0) as DECIMAL(7,2)) as Frete, 
Cast(coalesce(sum((c.contaxa)*(i.nfiporcentovalor/100)),0) as DECIMAL(7,2)) as Tarifa, 
Cast( SUM(coalesce((co.cobvalor+co.cobfretedestacado)*(i.nfiporcentoenvio/100),0)) as DECIMAL(7,2)) as Frete_Orcado,         
Cast( SUM(coalesce((co.cobvalor+co.cobfretedestacado)*(i.nfiporcentoenvio/100),0))
- coalesce(sum((c.confrete)*(i.nfiporcentovalor/100)),0) as DECIMAL(7,2)) as Diferenca
FROM notasfiscaisitens i 
	LEFT JOIN notasfiscais n on n.nfnumero = i.nfinota 
	LEFT JOIN conhecimentos c on c.connota = n.nfnumero 
    LEFT JOIN cobrancas co on co.cobpedido = i.nfipedido 
WHERE	
n.nfcodempresa not in (15,9551,5094)
and n.nftransportador not in (15,5160,4868,29262)";

//verifica o que foi preenchido e procede conforme
 
if($pedido != ''){ $sql = $sql." and i.nfipedido in (".$pedido.")";}
if($nota != ''){ $sql = $sql." and n.nfnumero in (".$nota.")";}
if($cliente != ''){ $sql = $sql." and n.nfclientepedido like ('%".$cliente."%')";}
if($codcliente != ''){ $sql = $sql." and n.nfcodempresa in (".$codcliente.")";}
if($mes != ''){ $sql = $sql." and n.nfemissao between '".$ano."-".$mes."-01' and '".$ano."-".$mes."-31' ";}

//finaliza o sql
$sql = $sql." group by Nota desc";

//echo $sql;
		
$query = mysqli_query($conn, $sql);

if (!$query) {
	die ('SQL Error: ' . mysqli_error($conn));
}
?>

<html>

	<title>Relatório de Pedidos Especificos</title>
	<h3 align="center">Relatório de Notas Fiscais Especificas - Desconsiderados envios via Gráfica/Pamavi/Viaje Seguro</h3>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">

	<table id="example" class="display compact cell-border custom" >
		<thead class="dt-center">
			<tr>	
				<th><div>NOTA</div></th>
				<th><div>ITENS</div></th>
				<th><div>DATA</div></th>
				<th><div>CLIENTE</div></th>
				<th><div>MODO</div></th>
				<th><div>CIDADE</div></th>
				<th><div>UF</div></th>
				<th><div>TRANSPORTADOR</div></th>
				<th><div>FRETES ZERADOS</div></th>
				<th><div>VALORES ENVIADOS</div></th>
				<th><div>CUSTO FRETE</div></th>
				<th><div>FRETE ORÇADO</div></th>
				<th><div>DIFERENÇA</div></th>
				<th><div>TARIFA</div></th>
			</tr>
		</thead>
		<tbody class="dt-center">
		<?php
		
		$total = $orcado = $custo = 0;
		while ($row = mysqli_fetch_array($query))
		{
			
			$valor_enviado  = $row['Valor_Enviado'] == 0 ? '' : number_format($row['Valor_Enviado'],2,',','');
			$frete  = $row['Frete'] == 0 ? '' : number_format($row['Frete'],2,',','');
			$tarifa  = $row['Tarifa'] == 0 ? '' : number_format($row['Tarifa'],2,',','');
			$frete_orcado  = $row['Frete_Orcado'] == 0 ? '' : number_format($row['Frete_Orcado'],2,',','');
			$diferenca  = $row['Diferenca'] == 0 ? '' : number_format($row['Diferenca'],2,',','');

			$tipofrete = utf8_encode($row['MODO']);
			switch ($tipofrete) {
				case 'EMITENTE':
					$tipofrete = 'CIF';
				break;
				case 'DESTINATÁRIO':
					$tipofrete = 'FOB';
				break;

			}	
			
			echo '<tr>
					<td>'.$row['Nota'].'</td>
					<td>'.$row['Itens'].'</td>
					<td>'.$row['Data'].'</td>
					<td>'.utf8_encode($row['CLIENTE']).'</td>
					<td>'.$tipofrete.'</td>
					<td>'.utf8_encode($row['CIDADE']).'</td>
					<td>'.$row['UF'].'</td>
					<td>'.utf8_encode($row['Transportador']).'</td>
					<td>'.$row['Fretes_Zerados'].'</td>
					<td>'.$valor_enviado.'</td>
					<td>'.$frete.'</td>
					<td>'.$frete_orcado.'</td>
					<td>'.$diferenca.'</td>
					<td>'.$tarifa.'</td>
				</tr>';
			$total += $row['Diferenca'];
			$orcado += $row['Frete_Orcado'];
			$custo += $row['Frete'];
		}?>
		</tbody>
	
	</table> 
	
	<span class="right"><font size="5" face="Calibri"><strong>Total de Frete: R$ <?=number_format($custo,2,',','')?> | Total Orçado: R$ <?=number_format($orcado,2,',','')?>
	 = RESULTADO: R$ <?=number_format($total,2,',','')?>   </strong></font></span>
		
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
$(document).ready(function() {
		 $.fn.dataTable.moment( 'DD/M/YYYY' );
	
    $('#example').DataTable( {
		"dom": '<"top"if>t',
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
			if(data[12]< "0"){
				$(row).find('td:eq(12)').css('color', 'red');
				$(row).find('td:eq(12)').css('font-weight', 'bold');
			}
			if(data[13]> "0"){
				$(row).find('td:eq(13)').css('color', 'white');
				$(row).find('td:eq(13)').css('font-weight', 'bold');
				$(row).find('td:eq(13)').css('background-color', 'red');
			}
			if(data[8] > "0"){
				$(row).find('td:eq(8)').css('font-weight', 'bold');
				$(row).find('td:eq(8)').css('background-color', '#ffff99');
			}
		}
		
    });
	
} );
</script>
</html>



