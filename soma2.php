<?php
ini_set('max_execution_time', 10000000);

$soma = "0.00";
$estudantilgrande = "2.49";
$estudantilpequena = "0.21";
$infantil = "0.3";
$executiva = "0.18";

$ciclos = 0;

for($i = 1; $i <= 1244 ; $i++){
   For($j = 1; $j <= 2960; $j++){
	    For($k = 1; $k <= 2220; $k++){
			For($l = 1; $l <= 4440; $l++){
									$ciclos++;
									echo $ciclos ." Estudantil Grande:".$i." de 1244<br>";
									$soma = ($i*$estudantilgrande) + ($j*$estudantilpequena) + ($k*$infantil) + ($l*$executiva);
									//Echo $soma;
									if($soma == "4364.36"){
										echo $soma.' -> Estudantil Grande R$ 8,75 = '.$i.'  Estudantil Pequena R$ 10,27 = '.$j.'  Infantil R$ 15,71= '.$k.'  
										Executiva = '.$l.'
										<br>' ;
										break;
									}					
								}
							}
						}
					}				
?>