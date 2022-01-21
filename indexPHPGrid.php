<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) ."/Fretes/phpGrid_lite/conf.php"); // absolute path to conf.php
require_once("phpGrid_Lite/conf.php");

$dg = new C_DataGrid("SELECT * FROM Conhecimentos", "concodigo", "conhecimentos");

// change column titles
$dg->set_col_title("concodigocte", "CTE");
$dg->set_col_title("connota", "Nota");

 
// hide a column
$dg -> set_col_hidden("concodigo,conEmissao");

// change default caption
$dg -> set_caption("Conhecimentos");
$dg -> set_col_link("confilename");

 
$dg -> display();


?>
