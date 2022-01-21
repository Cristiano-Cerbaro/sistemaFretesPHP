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
$erros = 0;
$erroexcel = 0;

//Carrega o Excel
$objPHPExcel = PHPExcel_IOFactory::load("cobrancas/cobrancas.xls");

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
			
	if($val[0] != '') {	
		//SE POSSUI NRO DE NOTA É BASE - MESMA LINHA
		
		$sql="INSERT INTO cobrancas(cobpedido,cobvalor,cobfretedestacado)
		VALUES ('".$val[0]."',
		'".$val[1]."',
		'".$val[2]."')";
		
		echo "<strong><font color=DarkBlue>".$row."ª Linha - Incluindo Cobrança: ".$val[0]."</strong></font>";
		echo " ---> <font size=2>".$sql."</font>";
			
		//INSERE A NOTA FISCAL	
		if(mysql_query($sql)){$notaadicionada++;}else{
			 echo '<br> <font color=darkgreen> Ja Cadastrado - Atualizar </font>';
				$sql2="update cobrancas c SET c.cobvalor = '".$val[1]."', 
				c.cobfretedestacado = '".$val[2]."' where c.cobpedido = '".$val[0]."'";
				echo " ---> <font size=2>".$sql2."</font>";
				if(mysql_query($sql2)){}else{
					$erros++;
					echo '<br> <font color=red>'.$erros.'º ERRO ATUALIZAÇÃO COBRANÇA  || </font>';
				}	
			}
			
		if($val[5] == 'S') {	
			echo '<br> <font color=orange> Estoque Identificado - Inserindo </font>';
				
				$sql3=	$sql="INSERT INTO estoque(estpedido) VALUES ('".$val[0]."')";
			
				echo " ---> <font size=2>".$sql3."</font>";
				if(mysql_query($sql3)){}else{
					$erros++;
					echo '<br> <font color=red>'.$erros.'º ERRO INSERÇÃO ESTOQUE || </font>';
				}
		}
			
		echo "<hr align='left' width='600' size='1' color=blue>"; //Pula Linha
		
	}	
}

echo "<hr align='left' width='600' size='5' color=DarkBlue>"; //Pula Linha

echo "<hr align='left' width='600' size='5' color=black>"; //Pula Linha
echo "<br><strong> Quantidade de Linhas Lidas: ".($highestRow);
echo "<br> Quantidade de Cobranças Adicionadas: ".$notaadicionada;
echo "<hr align='left' width='600' size='1' color=crimson>"; //Pula Linha
echo "<br> Quantidade de Erros: ".$erros;
echo "<br><font size=4 color=crimson>CONFERIR NA PESQUISA SE QUANTIDADE DE ERROS 'Duplicate' É IGUAL A QUANTIDADE DE ERROS</font>";
?>