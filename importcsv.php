<?php
//include the following 2 files
require 'PHPExcel/Classes/PHPExcel.php';
require_once 'PHPEXCEL/Classes/PHPExcel/IOFactory.php';
$conn = mysql_connect("localhost","root","");
mysql_select_db("fretes",$conn);

//$path = pathinfo($_FILES['file']['tmp_name']);
//"teste.xls";
//echo $path;

$nota = 0;
$notaadicionada = 0;
$itemadicionado = 0;
$erros = 0;
$item = 1;
$notaantiga = '';
$erroexcel = 0;
$porcentovalor = '';
$numeropedido = '';

//Carrega o Excel
$objPHPExcel = PHPExcel_IOFactory::load($_FILES['file']['tmp_name']);

//Lê o Excel
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;
	$highestRow = $highestRow-1;
}

//Popula o Array
for ($row = 5; $row <= $highestRow; ++ $row) {   //lendo a Linha
	$val=array();
	for ($col = 0; $col < $highestColumnIndex; ++ $col) { //lendo os itens da coluna
		$cell = $worksheet->getCellByColumnAndRow($col, $row);
		$val[] = utf8_decode($cell->getValue());
	}		
			
	if(($val[0] != '') && ($erroexcel==0)){	
		//SE POSSUI NRO DE NOTA É BASE - MESMA LINHA
		$nfvalortotal = $val[12];
		$nota= $val[0];
		$item = 1;
		$data = $val[1];
		$data = substr($data,6,4)."-".substr($data,3,2)."-".substr($data,0,2);
		
		If(isset($val[27])){ //verifica se o csv está com os campos corretos
			echo "<br><strong><font size=5 color=red>VOLUMES EM BRANCO, VERIFICAR EXCEL COLUNA K</strong></font><br>";
			$erroexcel = 1;
			break;
		}
		
		$transportadorcodigo = $val[8];
		Switch ($transportadorcodigo){
			Case '2326': $transportador = 'TNT';break;
			Case '1853': $transportador = 'Sao Miguel';break;
			Case '2136': $transportador = 'Correio';break;
			Case '208': $transportador = 'Cadore';break;
			Case '21700': $transportador = 'Reunidas';break;
			Case '2760': $transportador = 'Rodonaves';break;
			Case '10371': $transportador = 'Leomar';break;
			Case '19234': $transportador = 'Alfa';break;
			Case '1462': $transportador = 'TW';break;
			Case '2345': $transportador = 'Sao Joao';break;
			Case '15060': $transportador = 'Transpaulo';break;
			Case '17989': $transportador = 'HB';break;
			Case '452': $transportador = 'Cristofoli';break;
			Case '18664': $transportador = 'COTRAIBI';break;
			Case '9505': $transportador = 'Petrymar';break;
			default: $transportador = $val[21];
		}
		
			
		$sql="INSERT INTO notasfiscais(nfnumero,nfemissao,	nfdestinatario,
		nfcodempresa,nfcidade,nfuf,nftipofrete,nfvolumes,nfpeso,nfvalortotal,nftransportador,nfcep,nftransportadornome,nffretenota,nfclientepedido)
		VALUES ('".$nota."',
		'".$data."',
		'".addslashes($val[5])."',
		'".$val[4]."',
		'".addslashes($val[6])."',
		'".$val[7]."',
		'".$val[9]."',
		'".$val[10]."',
		'".$val[11]."',
		'".$val[12]."',
		'".$val[8]."',
		'".$val[20]."',
		'".$transportador."',
		'".$val[22]."',
		'".addslashes($val[25])."')";
		
		echo "<strong><font color=DarkBlue>".$row."ª Linha - Incluindo NF: ".$nota."</strong></font>";
		echo "<br><font size=2>".$sql."</font>";
			
		
		//INSERE A NOTA FISCAL	
		if(mysql_query($sql)){
			
			echo "<hr align='left' width='600' size='1' color=blue>"; //Pula Linha
			
			$notaadicionada++;
			
			$qtdremetida = $val[17];

			$qtd = $val[13];
			
			// Ajuste pedido fracionado
			If($val[26] != ''){
				$qtdremetida = $val[17]/$val[26];
				$qtd = $qtdremetida;
			}
			
			//calcula % envio  Envio*100/faturado
			If($val[15] != ''){ //verifica tem ligação com pedido
				$porcentoenvio = ($qtdremetida*100)/$val[15];
			}else{
				$porcentoenvio = '';
			}  //  echo "<font size=9> Porcento Envio:".$porcentoenvio."</font>";
			
			//calcula % valor  total Unitario*100/Total Nota
			if($nfvalortotal > 0){
				$porcentovalor = ($val[19]*100)/$nfvalortotal;
				//echo "<font size=6> Porcento Valor:".$porcentovalor."% - Total Item: ".$val[19]." - Total Nota: ".$nfvalortotal."</font>";
			}
			
			
			// Ajuste pedido conjunto ( obra e Cia )
			If($val[24] != ''){
				$numeropedido = $val[24];
			}else{
				$numeropedido = $val[2];
			}
			
			
			$sql="INSERT INTO notasfiscaisitens(nficfop,nfipedido,nfiqtd,
			 nfidescricao,nfipedqtd,nfipedqtdfat,
			 nfipedqtdrem,nfivlrunit,nfitotal,nfinota,nfiitem,nfiporcentoenvio,nfiporcentovalor)
			VALUES ('".$val[3]."',
			'".$numeropedido."',
			'".$qtd."',
			'".addslashes($val[14])."',
			'".$val[15]."',
			'".$val[16]."',
			'".$qtdremetida."',
			'".$val[18]."',
			'".$val[19]."',
			'".$nota."',
			'".$item."',
			'".$porcentoenvio."',
			'".$porcentovalor."'
			)";
			
			echo "<strong><font color=green>".$row."ª Linha - Incluindo Itens da NF Base: ".$nota."</strong></font>";
			echo "<br><font size=2>".$sql."</font>";
			
			//INSERE OS ITENS DA NOTA FISCAL
			if(mysql_query($sql)){$itemadicionado++;}else{$erros++; echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' - LINHA'.$row.' </font>';}
		
			echo "<hr align='left' width='600' size='1' color=green>"; //Pula Linha
		
		}else{$erros++; echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' - LINHA'.$row.' </font>';
			  echo "<hr align='left' width='600' size='1' color=blue>";} //Pula Linha
		
	}else{
		
		if($erroexcel == 0){
			$item++;
			
			//calcula % envio  Envio*100/faturado
			If($val[15] != ''){ //verifica tem ligação com pedido
				$porcentoenvio = ($val[17]*100)/$val[15];
			}else{
				$porcentoenvio = '';
			}   // echo "<font size=9> Porcento Envio:".$porcentoenvio."</font>";
			
			//calcula % valor  total Unitario*100/Total Nota
			if($nfvalortotal > 0){
				$porcentovalor = ($val[19]*100)/$nfvalortotal;
				//echo "<font size=6> Porcento Valor:".$porcentovalor."% - Total Item: ".$val[19]." - Total Nota: ".$nfvalortotal."</font>";
			}
			
			// Ajuste pedido conjunto ( obra e Cia )
			If($val[24] != ''){
				$numeropedido = $val[24];
			}else{
				$numeropedido = $val[2];
			}
			
			$sql="INSERT INTO notasfiscaisitens(nficfop,nfipedido,nfiqtd,
				 nfidescricao,nfipedqtd,nfipedqtdfat,
				 nfipedqtdrem,nfivlrunit,nfitotal,nfinota,nfiitem,nfiporcentoenvio,nfiporcentovalor)
			VALUES ('".$val[3]."',
			'".$numeropedido."',
			'".$val[13]."',
			'".addslashes($val[14])."',
			'".$val[15]."',
			'".$val[16]."',
			'".$val[17]."',
			'".$val[18]."',
			'".$val[19]."',
			'".$nota."',
			'".$item."',
			'".$porcentoenvio."',
			'".$porcentovalor."'
			)";
				
			echo "<strong><font color=Limegreen>".$row."ª Linha - Incluindo Itens da NF: ".$nota."</strong></font>";
			echo "<br><font size=2>".$sql."</font>";
					
			if(mysql_query($sql)){$itemadicionado++;}else{$erros++; echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' - LINHA'.$row.' </font>';}
		}
		
	echo "<hr align='left' width='600' size='1' color=green>"; //Pula Linha
	}
		
	}

	
echo "<hr align='left' width='600' size='5' color=DarkBlue>"; //Pula Linha

echo "<hr align='left' width='600' size='5' color=black>"; //Pula Linha
echo "<br><strong> Quantidade de Linhas Lidas: ".($highestRow-5);
echo "<br> Quantidade de Notas Adicionadas: ".$notaadicionada;
echo "<br> Quantidade de Itens Adicionadas: ".$itemadicionado;
echo "<hr align='left' width='600' size='1' color=crimson>"; //Pula Linha
echo "<br> Quantidade de Erros: ".$erros;
echo "<br><font size=4 color=crimson>CONFERIR NA PESQUISA SE QUANTIDADE DE ERROS 'Duplicate' É IGUAL A QUANTIDADE DE ERROS</font>";
?>