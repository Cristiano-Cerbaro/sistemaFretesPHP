<?php
//include the following 2 files
require 'PHPExcel/Classes/PHPExcel.php';
require_once 'PHPEXCEL/Classes/PHPExcel/IOFactory.php';
$conn = mysql_connect("localhost","root","");
mysql_select_db("fretes",$conn);

//$path = pathinfo($_FILES['file']['tmp_name']);
//"teste.xls";
//echo $path;

$estorno = $pamavi = $viajeseguro = 0;
$erros = 0;
$erroexcel = 0;

//Carrega o Excel
$objPHPExcel = PHPExcel_IOFactory::load("cobrancas/ajustes/ajustes.xls");

//Lê o Excel
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;
	$highestRow = $highestRow;
}



//Popula o Array
for ($row = 2; $row <= $highestRow; ++ $row) {   //lendo a Linha
	$val=array();
	for ($col = 0; $col < $highestColumnIndex; ++ $col) { //lendo os itens da coluna
		$cell = $worksheet->getCellByColumnAndRow($col, $row);
		$val[] = utf8_decode($cell->getValue());
	}	
	
	
	// 1ª Coluna - PAMAVI		
	if($val[0] != '') {	
		$sql= "update notasfiscais n SET n.nftransportador = '4868', n.nftransportadornome = 'Favero' 
			   where n.nfnumero = '".$val[0]."'";
		
		echo "<strong><font color=DarkGreen>".$row."ª Linha - Ajustando FAVERO: NF: ".$val[0]."</strong></font>";
		echo "<br><font size=2>".$sql."</font>";
			
		//Ajusta PAMAVI
		if(mysql_query($sql)){$pamavi++;}else{
			$erros++;
			echo '<br> <font color=red>'.$erros.'º ERRO ATUALIZAÇÃO FAVERO - '.mysql_error().' '.$filename.' || </font>';
			}
		echo "<hr align='left' width='600' size='1' color=blue>"; //Pula Linha
		
	}	
	
	// 2ª Coluna - Viaje Seguro	
	if($val[1] != '') {	
		$sql= "update notasfiscais n SET n.nftransportador = '29262',n.nftransportadornome = 'Cleber - Viaje Seguro' 
			   where n.nfnumero = '".$val[1]."'";
		
		echo "<strong><font color=blue>".$row."ª Linha - Ajustando Viaje Seguro: NF: ".$val[1]."</strong></font>";
		echo "<br><font size=2>".$sql."</font>";
			
		//Ajusta PAMAVI
		if(mysql_query($sql)){$viajeseguro++;}else{
			$erros++;
			echo '<br> <font color=red>'.$erros.'º ERRO ATUALIZAÇÃO VIAJE SEGURO - '.mysql_error().' '.$filename.' || </font>';
			}
		echo "<hr align='left' width='600' size='1' color=blue>"; //Pula Linha
		
	}
	
	// 3ª Coluna - ESTORNADAS	
	if($val[2] != '') {	
		$sql= "update notasfiscaisitens i SET i.nfiporcentoenvio = 0
			   where i.nfinota = '".$val[2]."'";
		
		echo "<strong><font color=Orange>".$row."ª Linha - Ajustando ESTORNO: NF: ".$val[2]."</strong></font>";
		echo "<br><font size=2>".$sql."</font>";
			
		//Ajusta PAMAVI
		if(mysql_query($sql)){$estorno++;}else{
			$erros++;
			echo '<br> <font color=red>'.$erros.'º ERRO ATUALIZAÇÃO ESTORNO - '.mysql_error().' '.$filename.' || </font>';
			}
		echo "<hr align='left' width='600' size='1' color=blue>"; //Pula Linha
		
	}
	
}

echo "<hr align='left' width='600' size='5' color=DarkBlue>"; //Pula Linha

echo "<hr align='left' width='600' size='5' color=black>"; //Pula Linha
echo "<br><strong> Quantidade de Linhas Lidas: ".($highestRow);
echo "<br> Quantidade de Notas Ajustadas para FAVERO: ".$pamavi;
echo "<br> Quantidade de Notas Ajustadas para VIAJE SEGURO: ".$viajeseguro;
echo "<br> Quantidade de Notas Ajustadas com ESTORNO: ".$estorno;
echo "<hr align='left' width='600' size='1' color=crimson>"; //Pula Linha
echo "<br> Quantidade de Erros: ".$erros;
echo "<br><font size=4 color=crimson>CONFERIR NA PESQUISA SE QUANTIDADE DE ERROS 'Duplicate' É IGUAL A QUANTIDADE DE ERROS</font>";
?>