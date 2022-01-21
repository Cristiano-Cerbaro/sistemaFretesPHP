<?php
$db_host = 'localhost'; // Server Name
$db_user = 'root'; // Username
$db_pass = ''; // Password
$db_name = 'Fretes'; // Database Name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());	
}


// MySQL Query to get data
$sql = "SELECT 

DATE_FORMAT( MAX(n.nfemissao) , '%d/%c/%Y' ) as DATA_ULTIMA_TARIFA,

n.nfcodempresa as CODIGO_CLIENTE,
    
n.nfclientepedido as CLIENTE,
    
n.nfcidade as CIDADE,

n.nfuf as UF,

Count(DISTINCT n.nfnumero) as TOTAL_ENVIOS,

sum(i.nfitotal) as TOTAL_ENVIADO,

Cast(coalesce(sum((c.confrete)*(i.nfiporcentovalor/100)),0) as DECIMAL(7,2)) as TOTAL_FRETE, 

Cast(coalesce(sum((c.contaxa)*(i.nfiporcentovalor/100)),0) as DECIMAL(7,2)) as TOTAL_TARIFA

FROM notasfiscaisitens i 
	LEFT JOIN notasfiscais n on n.nfnumero = i.nfinota 
	LEFT JOIN conhecimentos c on c.connota = n.nfnumero 
    LEFT JOIN cobrancas co on co.cobpedido = i.nfipedido 
WHERE	
n.nfcodempresa not in (15,9551,5094)
and n.nftransportador not in (15,5160,4348,29262)
and c.contaxa > 0
Group By CODIGO_CLIENTE
Order by TOTAL_TARIFA desc
";
//echo $sql;
		
$query = mysqli_query($conn, $sql);

if (!$query) {
	die ('SQL Error: ' . mysqli_error($conn));
}
?>

<html>

	<title>Relatório de Tarifas</title>
	<h3 align="center">Relatório de Tarifas - Desconsiderados envios via Gráfica/Pamavi/Viaje Seguro</h3>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">

	<table id="example" class="display compact cell-border custom" >
		<thead class="dt-center">
			<tr>	
				<th><div>ULTIMA TARIFA</div></th>
				<th><div>CODIGO CLIENTE</div></th>
				<th><div>CLIENTE</div></th>
				<th><div>CIDADE</div></th>
				<th><div>UF</div></th>
				<th><div>TOTAL ENVIOS</div></th>
				<th><div>VALOR ENVIADO</div></th>
				<th><div>TOTAL FRETE</div></th>
				<th><div>TOTAL TARIFA</div></th>
			</tr>
		</thead>
		<tbody class="dt-center">
		<?php
		
		$total = $orcado = $custo = 0;
		while ($row = mysqli_fetch_array($query))
		{
			
			$valor_enviado  = $row['TOTAL_ENVIADO'] == 0 ? '' : number_format($row['TOTAL_ENVIADO'],2,',','');
			$frete  = $row['TOTAL_FRETE'] == 0 ? '' : number_format($row['TOTAL_FRETE'],2,',','');
			$tarifa  = $row['TOTAL_TARIFA'] == 0 ? '' : number_format($row['TOTAL_TARIFA'],2,',','');	
			
			echo '<tr>
					<td>'.$row['DATA_ULTIMA_TARIFA'].'</td>
					<td>'.$row['CODIGO_CLIENTE'].'</td>
					<td>'.utf8_encode($row['CLIENTE']).'</td>
					<td>'.utf8_encode($row['CIDADE']).'</td>
					<td>'.$row['UF'].'</td>
					<td>'.$row['TOTAL_ENVIOS'].'</td>
					<td>'.$valor_enviado.'</td>
					<td>'.$frete.'</td>
					<td>'.$tarifa.'</td>
				</tr>';
			$total += $row['TOTAL_TARIFA'];
		}?>
		</tbody>
	
	</table> 
	
	<span class="right"><font size="5" face="Calibri"><strong>Total de Tarifas: R$ <?=number_format($total,2,',','')?>  </strong></font></span>
	<!-- <span class="left"><font size="5" face="Calibri"><strong>Periodo de: <?=$anteriormente?> a <?=$hoje?>   </strong></font></span>​	 !-->
		
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



