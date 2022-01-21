<?php
$db_host = 'localhost'; // Server Name
$db_user = 'root'; // Username
$db_pass = ''; // Password
$db_name = 'Fretes'; // Database Name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());	
}

// Definição de Data
$ano = substr($_GET["mes"],0,4);
$mes = substr($_GET["mes"],5,2);
	
// MySQL Query to get data

$sql = "SELECT c.connota as Nota, DATE_FORMAT( c.condata , '%d/%c/%Y' ) as Data,  c.confilename as arquivo,c.contransportador as Transportador, c.conremetente as Remetente, c.confrete as Frete,c.convalorcarga as Valor_Nota, c.conremetenteuf as UF 
FROM conhecimentos c WHERE c.condestinatariocnpj = '90393687000165' and c.contipofrete = 'FOB' and c.condata between 
'".$ano."-".$mes."-01' and '".$ano."-".$mes."-31' and c.connota >0
 order by Data
";
//echo $sql;


		
$query = mysqli_query($conn, $sql);

if (!$query) {
	die ('SQL Error: ' . mysqli_error($conn));
}
?>

<html>

	<title>Relatório de Compras FOB</title>
	<h3 align="center">Relatório de Compras na Modalidade FOB</h3>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">

	<table id="example" class="display compact cell-border custom" >
		<thead class="dt-center">
			<tr>	
				<th><div>NOTA</div></th>
				<th><div>DATA</div></th>
				<th><div>REMETENTE</div></th>
				<th><div>UF</div></th>
				<th><div>TRANSPORTADOR</div></th>
				<th><div>VALOR DO MATERIAL</div></th>
				<th><div>FRETE</div></th>
				<th><div>ARQUIVO</div></th>
			</tr>
		</thead>
		<tbody class="dt-center">
		<?php
		
		$total = $enviado	= 0;
		while ($row = mysqli_fetch_array($query))
		{
			
			$valor_enviado  = $row['Valor_Nota'] == 0 ? '' : number_format($row['Valor_Nota'],2,',','');
			$frete  = $row['Frete'] == 0 ? '' : number_format($row['Frete'],2,',','');

			
			echo '<tr>
					<td>'.$row['Nota'].'</td>
					<td>'.$row['Data'].'</td>
					<td>'.utf8_encode($row['Remetente']).'</td>
					<td>'.$row['UF'].'</td>
					<td>'.utf8_encode($row['Transportador']).'</td>
					<td>'.$valor_enviado.'</td>
					<td>'.$frete.'</td>
					<td>'.$row['arquivo'].'</td>
				</tr>';
			$total += $row['Frete'];
			$enviado += $row['Valor_Nota'];
		}?>
		</tbody>
	</table>  
	
	<span class="right"><font size="5" face="Calibri"><strong>Mercadoria Recebida: R$ <?=number_format($enviado,2,',','')?> | Frete Gasto: R$ <?=number_format($total,2,',','')?>   </strong></font></span>	
	<span class="left"><font size="5" face="Calibri"><strong>Mês de Referência: <?=$mes?>/<?=$ano?>   </strong></font></span>​	

		
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
		
		  //para imprimir em folhas, deixar os campos abaixo em comentario
		"dom": '<"top"if>t',
		scrollY:        '81vh',
        scrollCollapse: true,
		//"order": [[ 1, "desc" ]],
		
		 // para imprimir em folhas, deixar os campos acima em comentario
		
		"paging":   false,
		
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Portuguese-Brasil.json",
	   },
	   "columnDefs": [
        {"className": "dt-center", "targets": "_all"},
		  {
                "targets": [ 7 ],  // Remove a coluna Arquivo
                "visible": false,
                "searchable": false
            }, 
			{
            targets: [ 1 ],
            orderData: [ 0, 1 ]
        }, {
            targets: [ 0 ],
            orderData: [ 1, 0 ]
        }
			/* Adiciona Botao
			{
            "targets": -1,
			"data": null,
            "defaultContent": "<button>XML</button>"
        } */
      ],
		
    });
	
	/* Clicar no botao
	$('#example tbody').on( 'click', 'button', function () {
		 var data = table.row($(this).parents('tr')).data();
		 alert("CLICOU" );
        
        alert("'s salary is: "+ data[ 1 ] );
    } );
	
	 CLicar na Nota
	var table = $('#example').DataTable();
 
		$('#example tbody').on( 'click', 'td', function () {
			if ($(this).index() != 0 ) { // provide index of your column in which you prevent row click here is column of 4 index
             return;
         }{
			alert( table.cell( this ).data() );
		 }
		} ); */
} );
</script>
</html>



