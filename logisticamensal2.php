<?php
$db_host = 'localhost'; // Server Name
$db_user = 'root'; // Username
$db_pass = ''; // Password
$db_name = 'Fretes'; // Database Name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
die ('Failed to connect to MySQL: ' . mysqli_connect_error());	
}

// Definição de Variaveis
$freteorcadototal = $malotes = $qtdcortesias =  $freteorcadotransportadores = $totalgastoexterno = $pamavi = $qtdpamavi =
$qtdinterno = $freteorcadointerno = $combustivel = $envios = $diversos = $manutencao = $mes = $ano = $motoboy = $sempedido =
$freteorcadopamavi = $qtdtransportadores = $qtdconsertos = $transportadores = $freteconsertos = $fretecortesia = $viaje_seguro =
$freteorcadoviajeseguro = $qtdviajeseguro = $correios = $qtdcorreios = $freteorcadocorreios = 0;

$ano = substr($_GET["mes"],0,4);
$mes = substr($_GET["mes"],5,2);

//echo "Ano ".$ano." Mes ".$mes;



// Quantidade de Envios via transportador ( Sem Serafinense - Sem Pamavi, sem Viaje_Seguro, sem cortesias e sem consertos )
$sql = "SELECT 
n.nfnumero as Nota,

coalesce(sum((c.confrete)*(i.nfiporcentovalor/100)),0) as Frete, 

Cast( SUM(coalesce((co.cobvalor+co.cobfretedestacado)*(i.nfiporcentoenvio/100),0)) as DECIMAL(7,2)) as Frete_Orcado

FROM notasfiscaisitens i 
LEFT JOIN notasfiscais n on n.nfnumero = i.nfinota 
LEFT JOIN conhecimentos c on c.connota = n.nfnumero 
LEFT JOIN cobrancas co on co.cobpedido = i.nfipedido 
WHERE	
n.nfcodempresa not in (15,9551,5094)
and n.nftransportador not in (15,2136,5160,4348,4868,29262,0)
and n.nftipofrete in ('EMITENTE')
and n.nfemissao between '".$ano."-".$mes."-01' and '".$ano."-".$mes."-31'
and i.nficfop not in (5910,6910,5915,6915)
and i.nfipedido > 0
group by Nota desc";
$query = mysqli_query($conn, $sql);
if (!$query) { die ('SQL Error: ' . mysqli_error($conn));}
while ($row = mysqli_fetch_array($query))
{
$transportadores = $transportadores + $row['Frete'];
$qtdtransportadores++;
$freteorcadotransportadores = $freteorcadotransportadores + $row['Frete_Orcado'];
}

// Quantidade de Envios via transportador SEM PEDIDO/OP ( Sem Serafinense - Sem Pamavi, Sem Viaje_Seguro sem cortesias e sem consertos )
$sql = "SELECT 
n.nfnumero as Nota,

coalesce(sum((c.confrete)*(i.nfiporcentovalor/100)),0) as Frete, 

Cast( SUM(coalesce((co.cobvalor+co.cobfretedestacado)*(i.nfiporcentoenvio/100),0)) as DECIMAL(7,2)) as Frete_Orcado

FROM notasfiscaisitens i 
LEFT JOIN notasfiscais n on n.nfnumero = i.nfinota 
LEFT JOIN conhecimentos c on c.connota = n.nfnumero 
LEFT JOIN cobrancas co on co.cobpedido = i.nfipedido 
WHERE	
n.nfcodempresa not in (15,9551,5094)
and n.nftransportador not in (15,2136,5160,4348,4868,4368,29262)
and n.nftipofrete in ('EMITENTE')
and n.nfemissao between '".$ano."-".$mes."-01' and '".$ano."-".$mes."-31'
and i.nficfop not in (5910,6910,5915,6915)
and i.nfipedido = 0
group by Nota desc";
$query = mysqli_query($conn, $sql);
if (!$query) { die ('SQL Error: ' . mysqli_error($conn));}
while ($row = mysqli_fetch_array($query))
{
$sempedido = $sempedido + $row['Frete'];
}


// Quantidade de Envios Cortesias e Consertos (diversos)
$sql = "SELECT 
count(n.nfnumero) as QTD_Notas,

i.nficfop as CFOP,

coalesce(sum((c.confrete)*(i.nfiporcentovalor/100)),0) as Frete 

FROM notasfiscaisitens i 
LEFT JOIN notasfiscais n on n.nfnumero = i.nfinota 
LEFT JOIN conhecimentos c on c.connota = n.nfnumero 
WHERE	
n.nfcodempresa not in (15,9551,5094)
and n.nftransportador not in (15,2136,5160,4348,4868,29262)
and n.nftipofrete in ('EMITENTE')
and n.nfemissao between '".$ano."-".$mes."-01' and '".$ano."-".$mes."-31'
and i.nficfop in (5910,6910,5915,6915)
group by CFOP";
$query = mysqli_query($conn, $sql);
if (!$query) { die ('SQL Error: ' . mysqli_error($conn));}
while ($row = mysqli_fetch_array($query))
{
Switch ($row['CFOP']){
Case '5910': $qtdcortesias= $qtdcortesias + $row['QTD_Notas'];$fretecortesia =$fretecortesia + $row['Frete'];break;
Case '6910': $qtdcortesias= $qtdcortesias + $row['QTD_Notas'];$fretecortesia =$fretecortesia + $row['Frete'];break;
Case '5915': $qtdconsertos= $qtdconsertos + $row['QTD_Notas'];$freteconsertos =$freteconsertos + $row['Frete'];break;
Case '6915': $qtdconsertos= $qtdconsertos + $row['QTD_Notas'];$freteconsertos =$freteconsertos +  $row['Frete'];break;
}
}	


// Quantidade de Malotes
$sql = "SELECT SUM(c.confrete) as Malotes, Count(c.confrete) as QTD_notas FROM conhecimentos c where c.conproduto = 'MALOTE' and condata between '".$ano."-".$mes."-01' and '".$ano."-".$mes."-31'";
$query = mysqli_query($conn, $sql);
if (!$query) { die ('SQL Error: ' . mysqli_error($conn));}
while ($row = mysqli_fetch_array($query))
{
$malotes = $row['Malotes'];
$qtdmalotes = $row['QTD_notas'];
}

// Custos
$sql = "SELECT SUM(c.cusvalor) as Valor, c.custipo as TIPO FROM custos c WHERE c.cusmes = '".$mes."' and c.cusano = '".$ano."' group by TIPO";
$query = mysqli_query($conn, $sql);
if (!$query) { die ('SQL Error: ' . mysqli_error($conn));}
while ($row = mysqli_fetch_array($query))
{
Switch ($row['TIPO']){
Case 'Combustivel': $combustivel = $row['Valor'];break;
Case 'Envios': $envios = $row['Valor'];break;
Case 'Diversos': $diversos = $row['Valor'];break;
Case 'Manutencao': $manutencao = $row['Valor'];break;
Case 'Pamavi': $pamavi = $row['Valor'];break;
Case 'Motoboy': $motoboy = $row['Valor'];break;
Case 'Viaje_Seguro': $viaje_seguro = $row['Valor'];break;
}
}	





$totalgastoexterno = $transportadores + $pamavi + $viaje_seguro + $correios + $envios;	

?>


<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relatório de Logistica Mensal</title>
    <link rel="stylesheet" href="style2.css">
  </head>
  <body>
    <h1 align="center">Relatório de Logistica </h1>
    <h2 align="center">Periodo de Referência:<?php echo " ".$mes."/".$ano;?></h2>
    <hr>
    <h3> Logistica Externa:</h3>
    <ul type="square"> 
      <li><?php echo $qtdtransportadores;?> Envios via Transportador: <?php echo "R$ ".number_format($transportadores,2,',','');?> <u><strong><font size=3><?php echo "    |Orçado: R$ ".number_format($freteorcadotransportadores,2,',','')." ";?> </font></u></strong></li>
	</ul>
    <hr>

    <h3> Envios sem Cobrança Orçada:    </h3> 
    <ul>
      <li><?php echo $qtdmalotes;?> Envios de Malotes/Provas: <?php echo "R$ ".number_format(($malotes),2,',','');?> </li>
      <li><?php echo $qtdcortesias;?> Envios de Cortesias: <?php echo "R$ ".number_format($fretecortesia,2,',','');?></li>
      <li><?php echo $qtdconsertos;?> Envios de Consertos: <?php echo "R$ ".number_format($freteconsertos,2,',','');?></li>
      <li>Envios de Materiais Sem Pedido/OP: <?php echo "R$ ".number_format($sempedido,2,',','');?></li>
    </ul>
    &emsp;<strong>Total Despesas: </strong><?php echo "R$ ".number_format($malotes+$fretecortesia+$freteconsertos+$motoboy+$sempedido,2,',','');?>

    <hr>
    <h2> <mark>&ensp;Resultado&ensp;</mark></h2>
    <ul type="square"> 
      <li><strong>Total Frete Orçado Via Transportador: <?php echo "R$ ".number_format($freteorcadotransportadores,2,',','');?></strong></li>
      <ul>
        <li> - Transportadores: <?php echo "R$ ".number_format($transportadores,2,',','');?><!--<u> <font size=3><?php// echo "    |Orçado: R$ ".number_format($freteorcadotransportadores,2,',','')." ";?> </font></u></li>  !-->
    	 </ul>
      <strong>&emsp;Total Parcial: </strong> <?php echo "R$ ".number_format($freteorcadotransportadores-$transportadores,2,',','');?>
     <hr align=left width=400>
<li> - Envios sem Cobrança Orçada: <?php echo "R$ ".number_format(($malotes+$fretecortesia+$freteconsertos+$sempedido),2,',','');?></li>

<hr align=left width=400> 
<mark><strong><font size=5>&ensp;Total Geral: <?php echo "R$ ".number_format($freteorcadotransportadores-($transportadores+$malotes+$fretecortesia+$freteconsertos+$sempedido),2,',','');?>&ensp; </font></strong> </mark>


<p align="right">
  <font size="2"> Gerado em: <strong><?php echo date('d-m-Y');?></strong></font> 
</p>
</body>
</html>