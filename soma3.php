<?php
ini_set('max_execution_time', 10000000);

$soma = "0.00";
$brochura = "8.55";
$espiral = "7.96";

$ciclos = 0;

for($i = 1; $i <= 11260 ; $i++){
   For($j = 700; $j <= 1505; $j++){

		$ciclos++;
		echo $ciclos ." Brochura:".$j." de 1505<br>";
		$soma = ($j*$brochura) + ($i*$espiral);
		//Echo $soma;
		if($soma == "69463.35"){
			echo $soma.' -> Brochura R$ 8,55 = '.$i.'  Espiral R$ 7,96 = '.$j.'<br>' ;
		break;
		}					
	}
}	
?>