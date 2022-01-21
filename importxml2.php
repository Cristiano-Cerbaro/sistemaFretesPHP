<?php
$conn = mysql_connect("localhost","root","");
mysql_select_db("fretes",$conn);

//$xmlobj = simplexml_load_file("teste.xml");
//$ns = $teste->getNamespaces(true);
	
$dir    = 'conhecimentos';	
//$dir    = 'conhecimentos/teste';

$files = scandir($dir);
$erros = 0;
$conhecimentoscancelados = 0;
$duplicados = 0;
$complementos = 0;
$conhecimentosadicionados = 0;
$malotes = 0;
$mensagem_erro = '';
$arquivo = '';
$cancelar =[];

ini_set('max_execution_time', 300);

foreach ($files as $filename) {
    if (stristr($filename, ".xml") !== false) {
		
		$xmlobj = simplexml_load_file($dir.'/'.$filename);
		$arquivo++;
		echo $arquivo."º Arquivo - ".$filename;
	    
		$notaprincipal = $chavenota = $nota = $transportador = $conpeso = $convolumes = '';
		
		//Verifica se é um Conhecimento de Transporte
		IF (isset($xmlobj->CTe->infCte->ide->nCT)){
			
			$codigocte = $xmlobj->CTe->infCte->ide->nCT;
			$chave= $xmlobj->CTe->infCte[0]['Id']; //echo "<br>".$chave;
			$emissao = $xmlobj->CTe->infCte->ide->dhEmi;
			$remetentecnpj = $xmlobj->CTe->infCte->rem->CNPJ;
			$remetente = $xmlobj->CTe->infCte->rem->xNome;
			$remetenteuf = $xmlobj->CTe->infCte->rem->enderReme->UF;
			$cidadeorigem = $xmlobj->CTe->infCte->rem->enderReme->xMun;
			$destinatariocnpj = $xmlobj->CTe->infCte->dest->CNPJ;
			$destinatario = $xmlobj->CTe->infCte->dest->xNome;
			$destinatariouf = $xmlobj->CTe->infCte->dest->enderDest->UF;
			$cidadedestino = $xmlobj->CTe->infCte->dest->enderDest->xMun;
		
			if(isset($xmlobj->CTe->infCte->infCTeNorm->infCarga->proPred)){
				$conproduto = $xmlobj->CTe->infCte->infCTeNorm->infCarga->proPred;
			}else{
				$conproduto = '';
			}
			if(isset($xmlobj->CTe->infCte->infCTeNorm->infCarga->vCarga)){
				$convalorcarga = $xmlobj->CTe->infCte->infCTeNorm->infCarga->vCarga;
			}else{
				$convalorcarga = '';
			}
			
		
			$transportadorCNPJ = $xmlobj->CTe->infCte->emit->CNPJ;
			$cidadetrasportador = $xmlobj->CTe->infCte->emit->enderEmit->xMun;
		
			// Manipulando a data
			$data = $xmlobj->CTe->infCte->ide->dhEmi;
				//2015-08-11T19:06:14
				//$data = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);    //  ." ".substr($data,11,8);  
			echo "<strong>         Emissão: ".substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4)."-".substr($data,11,8)."</strong>";
			$data = substr($data,0,4)."-".substr($data,5,2)."-".substr($data,8,2);
		
			// Manipulando nome do Transportador
			switch($transportadorCNPJ){
				
				Case '95591723004530': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723000208': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723008101': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723003800': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723006311': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723014330': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723015655': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723005935': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723002162': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723011072': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723015221': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723008799': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723008284': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723012206': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				Case '95591723014098': $transportador = 'TNT';$transportadorcodigo = '2326';break;
				
				Case '00428307000600': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307000864': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307000511': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307000279': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307000350': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307000430': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307001240': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307000198': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307001160': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307000783': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307001089': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307001593': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307001321': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307001836': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;
				Case '00428307001755': $transportador = 'Sao Miguel';$transportadorcodigo = '1853';break;	
				
				Case '90394107000316': $transportador = 'Cadore';$transportadorcodigo = '208';break;
				Case '90394107000154': $transportador = 'Cadore';$transportadorcodigo = '208';break;
				Case '90394107001045': $transportador = 'Cadore';$transportadorcodigo = '208';break;
				Case '90394107000588': $transportador = 'Cadore';$transportadorcodigo = '208';break;
				Case '90394107000669': $transportador = 'Cadore';$transportadorcodigo = '208';break;
				
				Case '04176082000695': $transportador = 'Reunidas';$transportadorcodigo = '21700';break;
				Case '83083428000415': $transportador = 'Reunidas';$transportadorcodigo = '21700';break;
				Case '04176082000423': $transportador = 'Reunidas';$transportadorcodigo = '21700';break;
				Case '83083428000253': $transportador = 'Reunidas';$transportadorcodigo = '21700';break;
				
				Case '44914992002777': $transportador = 'Rodonaves';$transportadorcodigo = '2760';break;
				Case '44914992001371': $transportador = 'Rodonaves';$transportadorcodigo = '2760';break;
				Case '44914992000138': $transportador = 'Rodonaves';$transportadorcodigo = '2760';break;
				
				Case '02633583000113': $transportador = 'Leomar';$transportadorcodigo = '10371';break;
				Case '15404147000114': $transportador = 'Leomar';$transportadorcodigo = '10371';break;
				
				Case '82110818000121': $transportador = 'Alfa';$transportadorcodigo = '19234';break;
				Case '82110818000806': $transportador = 'Alfa';$transportadorcodigo = '19234';break;
				Case '82110818000393': $transportador = 'Alfa';$transportadorcodigo = '19234';break;
				
				Case '89317697000132': $transportador = 'TW';$transportadorcodigo = '1462';break;
				Case '26610194000130': $transportador = 'TW';$transportadorcodigo = '1462';break;
				Case '89317697003743': $transportador = 'TW';$transportadorcodigo = '1462';break;
				
				
				Case '87761342000102': $transportador = 'Sao Joao';$transportadorcodigo = '2345';break;
				Case '25088047000180': $transportador = 'Sao Joao';$transportadorcodigo = '2345';break;
				Case '88317847000730': $transportador = 'Transpaulo';$transportadorcodigo = '15060';break;
				Case '92528538000434': $transportador = 'HB';$transportadorcodigo = '17989';break;
				Case '88670104000154': $transportador = 'Cristofoli';$transportadorcodigo = '452';break;
				Case '07441985000210': $transportador = 'COTRAIBI';$transportadorcodigo = '18664';break;
				Case '90605759000190': $transportador = 'Petrymar';$transportadorcodigo = '9505';break;
				Case '04002624000107': $transportador = 'Prata Vera';$transportadorcodigo = '30917';break;
				Case '04002624000280': $transportador = 'Prata Vera';$transportadorcodigo = '30917';break;
				Case '01411363000182': $transportador = 'Ases';$transportadorcodigo = '19112';break;
				Case '11423942000360': $transportador = 'TJB';$transportadorcodigo = '19112';break;
				Case '13703795000146': $transportador = 'Ultralog';break;
				Case '27502214000112': $transportador = 'Brasifer';$transportadorcodigo = '34178';break;
				Case '02314317000128': $transportador = 'Pamavi';$transportadorcodigo = '4348';break;
				Case '94001641000104': $transportador = 'BRIX';$transportadorcodigo = '11238';break;
				Case '07856212000114': $transportador = 'Rapido Guapore';$transportadorcodigo = '348';break;
				Case '10863778000188': $transportador = 'Chiodi';$transportadorcodigo = '13404';break;
				Case '02463394000140': $transportador = 'CCA';$transportadorcodigo = '2296';break;
				Case '04002624000360': $transportador = 'Pratavera';$transportadorcodigo = '38967';break;
				
				
				Case '48740351011957': $transportador = 'Braspress';$transportadorcodigo = '2290';break;
				Case '48740351004403': $transportador = 'Braspress';$transportadorcodigo = '2290';break;
				Case '48740351008220': $transportador = 'Braspress';$transportadorcodigo = '2290';break;
				
				Case '26610194000130': $transportador = 'Chiodi';$transportadorcodigo = '13404';break;
				
				
			}
			echo "<br><strong> Transportador: ".$transportador."</strong>         Transportador CNPJ: ".$transportadorCNPJ; 
			if($transportador == ''){ 
			echo "<font color=red>VERIFICAR ERRO: NÃO ENCONTROU TRANSPORTADOR -> ".$xmlobj->CTe->infCte->emit->xNome."</font>";
			$transportador = $xmlobj->CTe->infCte->emit->xNome;
			}
			
			$frete = $xmlobj->CTe->infCte->vPrest->vTPrest;
			
			// Localizar Taxas de TDA/TDE
			$i = 0;
			$contaxa = 0;
			while ($i < 12){
				//echo $filename;
				//echo $i;
				//echo $xmlobj->CTe->infCte->vPrest->Comp[$i]->xNome;
				if(isset($xmlobj->CTe->infCte->vPrest->Comp[$i]->xNome)){
					$descricao = $xmlobj->CTe->infCte->vPrest->Comp[$i]->xNome;
					if(($descricao == 'TDE/TDA') || ($descricao == 'TRT') || ($descricao == 'AREA RESTRITA') || ($descricao == 'TDA/TDE'))
					{
						$contaxa = $xmlobj->CTe->infCte->vPrest->Comp[$i]->vComp;
						//echo "<br> Achou Taxa";
					}

					$i++;
				}else{
					$i = $i+12;  // Sai do Laço
				}
			}
			
			IF(isset($xmlobj->CTe->infCte->ide->toma03->toma)){
				$tipofrete = $xmlobj->CTe->infCte->ide->toma03->toma;
			} 
			else if(isset($xmlobj->CTe->infCte->ide->toma3->toma)) {
				$tipofrete = $xmlobj->CTe->infCte->ide->toma3->toma;
			}
			else if(isset($xmlobj->CTe->infCte->ide->toma4->toma)) {
				$tipofrete = $xmlobj->CTe->infCte->ide->toma4->toma;
			} 
			else {
				echo "<br><strong><font size=4>VERIFICAR: NÃO ACHOU TIPO DO FRETE</strong></font>";
			}
			
			switch ($tipofrete) {
				case 0:
					$tipofrete = 'CIF';
				break;
				case 3:
					$tipofrete = 'FOB';
				break;
				case 4:
					$tipofrete = 'TER';
					echo "<br><strong><font size=4>VERIFICAR ERRO: FREEEETEEE TERCEIROS - NãO TRATEI</strong></font>";
				break;
			}	
			
			
			
			//Motivo Operação - Apenas TNT funciona
			//echo "Operação: ".$xmlobj->CTe->infCte->compl->xCaracAd;
		
			//descobrir o peso e volume
			$i = 0;
			while ($i < 10){
				//echo $filename;
				//echo $i;
				//echo $xmlobj->CTe->infCte->infCTeNorm->infCarga->infQ[$i]->tpMed;
				if(isset($xmlobj->CTe->infCte->infCTeNorm->infCarga->infQ[$i]->tpMed)){
					$descricao = $xmlobj->CTe->infCte->infCTeNorm->infCarga->infQ[$i]->tpMed;
					if(($descricao == 'PESO BRUTO') || ($descricao == 'PESO') || ($descricao == 'PESO CUBADO') ||
					($descricao== 'PESO DECLARADO') ||	($descricao == 'PESO REAL') ||	($descricao == 'PESO BASE DE CALCULO'))
					{
						$conpeso = $xmlobj->CTe->infCte->infCTeNorm->infCarga->infQ[$i]->qCarga;
						//echo "<br> Achou Peso";
					}
					if(($descricao == 'VOLUMES') ||	($descricao == 'QTDE DE VOLUMES') || ($descricao == 'UNIDADE')|| ($descricao == 'UNIDADES')
						|| ($descricao == 'QUANTIDADE DE VOLUME') || ($descricao == 'CAIXAS E ETC'))
					{
						$convolumes = $xmlobj->CTe->infCte->infCTeNorm->infCarga->infQ[$i]->qCarga;
						//echo "<br> Achou Volume";
					}
					$i++;
				}else{
					$i = $i+10;  // Sai do Laço
				}
			}
			If ($conpeso == ''){ echo "<br><strong><font color=red>VERIFICAR ERRO: NÃO ACHOU PESO</strong></font>";}
			If ($convolumes == ''){ 
				if(($transportador != 'COTRAIBI') && ($transportador != 'Sao Joao')) {
					echo "<br><strong><font color=red>VERIFICAR ERRO: NÃO ACHOU VOLUMES</strong></font>";
				}
			}
		
			// variavel que informa a quantidade de notas que passou
			$qtdnotas = 0;
		
			// xml nota modelo 1
			foreach ($xmlobj->CTe->infCte->rem->infNFe  as $chavenota){
			
				Echo $qtdnotas." Quantidade de notas tipo 1<br>";
				$nota = substr($xmlobj->CTe->infCte->rem->infNFe[$qtdnotas]->chave, 28,6);
				echo "<br><strong>NF: ".$nota."</strong>";
			
				if($qtdnotas == '0'){
				
					//define a nota principal que terá mais informações
					$notaprincipal = $nota;
				
					//echo $notaprincipal." nota principal";
				
					$sql="INSERT INTO conhecimentos(concodigocte,connota,confrete,confilename,contipofrete,conremetente,conremetentecnpj,
					condestinatario,condestinatariocnpj,condata,conproduto,convolumes,conpeso,convalorcarga,conremetenteUF,
					conemissao,concidadetransportador,concidadedestino,condestinatariouf,contransportador,contransportadorcnpj,concidadeorigem,conid,contaxa)
					VALUES ('".$codigocte."',
					'".$nota."',
					'".$frete."',
					'conhecimentos/".$filename."',
					'".$tipofrete."',
					'".$remetente."',
					'".$remetentecnpj."',
					'".addslashes($destinatario)."',
					'".$destinatariocnpj."',
					'".$data."',
					'".$conproduto."',
					'".$convolumes."',
					'".$conpeso."',
					'".$convalorcarga."',
					'".$remetenteuf."',
					'".$emissao."',
					'".$cidadetrasportador."',
					'".addslashes($cidadedestino)."',
					'".$destinatariouf."',
					'".$transportador."',
					'".$transportadorCNPJ."',
					'".$cidadeorigem."',
					'".$chave."',
					'".$contaxa."'
					)";
					
					//echo "SQL - Nota ".$qtdnotas."<br>".$sql."<br>";
				}else{
					
					echo "<br><strong><font color=purple>NOTA PRINCIPAL -> ".$notaprincipal."</strong></font>";
					$sql="INSERT INTO conhecimentos(concodigocte,connota,confilename,contipofrete,conremetente,conremetentecnpj,
						condestinatario,condestinatariocnpj,condata,conproduto,confretedividido,conremetenteUF,
						conemissao,concidadetransportador,concidadedestino,condestinatariouf,contransportador,contransportadorcnpj,concidadeorigem,conid)
						VALUES ('".$codigocte."',
						'".$nota."',
						'conhecimentos/".$filename."',
						'".$tipofrete."',
						'".$remetente."',
						'".$remetentecnpj."',
						'".addslashes($destinatario)."',
						'".$destinatariocnpj."',
						'".$data."',
						'".$conproduto."',
						'".$notaprincipal."',
						'".$remetenteuf."',
						'".$emissao."',
						'".$cidadetrasportador."',
						'".addslashes($cidadedestino)."',
						'".$destinatariouf."',
						'".$transportador."',
						'".$transportadorCNPJ."',
						'".$cidadeorigem."',
						'".$chave."'
						)";
						
					//echo "SQL - Nota ".$qtdnotas."<br>".$sql."<br>";
				}			
				
				if(mysql_query($sql)){
					$conhecimentosadicionados++;
						
						
						If($transportadorcodigo != ''){
						//ATUALIZA TABELA NF COM O NOME DO TRANSPORTADOR CORRETO
						$sql="update notasfiscais n SET n.nftransportador = '".$transportadorcodigo."',
						n.nftransportadornome = '".$transportador."' where n.nfnumero = '".$nota."'";
						//echo $sql;
							if(mysql_query($sql)){}else{
								$erros++;
								echo '<br> <font color=red>'.$erros.'º ERRO ATUALIZAÇÃO TRANSPORTADOR - '.mysql_error().' '.$filename.' || </font>';
							}	
						}
					
					
				}else{$erros++;
				echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' '.$filename.' || </font>';
				if(strpos(mysql_error(),'Registro_Unico')){
					$duplicados++;
				}
					// Atualização forçada do transportador mesmo se já tiver conhecimento já cadastrado
					If($transportadorcodigo != ''){
						//ATUALIZA TABELA NF COM O NOME DO TRANSPORTADOR CORRETO
						$sql="update notasfiscais n SET n.nftransportador = '".$transportadorcodigo."',
						n.nftransportadornome = '".$transportador."' where n.nfnumero = '".$nota."'";
						//echo $sql;
							if(mysql_query($sql)){}else{
								$erros++;
								echo '<br> <font color=red>'.$erros.'º ERRO ATUALIZAÇÃO TRANSPORTADOR - '.mysql_error().' '.$filename.' || </font>';
							}	
						}
				
				}
				
				//echo $filename;
				
				//Mostrar Mensagem do SQL
				echo "<br><font size=-3>SQL - ".$sql."<br></font>";
			
				$qtdnotas++;
				
				
			}
				
			/*  */
			
			// xml nota modelo 2
			foreach ($xmlobj->CTe->infCte->infCTeNorm->infDoc->infNFe  as $chavenota){
				
				//Echo $qtdnotas." Quantidade de notas tipo 2<br>";
				$nota = substr($xmlobj->CTe->infCte->infCTeNorm->infDoc->infNFe[$qtdnotas]->chave, 28,6);
				echo "<br><strong>NF: ".$nota."</strong>";
				
				if($qtdnotas == '0'){
					//define a nota principal que terá mais informações
					$notaprincipal = $nota;
					
					//echo $notaprincipal." nota principal";
					
					$sql="INSERT INTO conhecimentos(concodigocte,connota,confrete,confilename,contipofrete,conremetente,conremetentecnpj,
					condestinatario,condestinatariocnpj,condata,conproduto,convolumes,conpeso,convalorcarga,conremetenteUF,
					conemissao,concidadetransportador,concidadedestino,condestinatariouf,contransportador,contransportadorcnpj,concidadeorigem,conid,contaxa)
					VALUES ('".$codigocte."',
					'".$nota."',
					'".$frete."',
					'conhecimentos/".$filename."',
					'".$tipofrete."',
					'".$remetente."',
					'".$remetentecnpj."',
					'".addslashes($destinatario)."',
					'".$destinatariocnpj."',
					'".$data."',
					'".$conproduto."',
					'".$convolumes."',
					'".$conpeso."',
					'".$convalorcarga."',
					'".$remetenteuf."',
					'".$emissao."',
					'".$cidadetrasportador."',
					'".addslashes($cidadedestino)."',
					'".$destinatariouf."',
					'".$transportador."',
					'".$transportadorCNPJ."',
					'".$cidadeorigem."',
					'".$chave."',
					'".$contaxa."'
					)";
					
					//echo "SQL - Nota ".$qtdnotas."<br>".$sql."<br>";
				}else{
					
					echo "<br><strong><font color=purple>NOTA PRINCIPAL -> ".$notaprincipal."</strong></font>";
					$sql="INSERT INTO conhecimentos(concodigocte,connota,confilename,contipofrete,conremetente,conremetentecnpj,
					   condestinatario,condestinatariocnpj,condata,conproduto,confretedividido,conremetenteUF,
						conemissao,concidadetransportador,concidadedestino,condestinatariouf,contransportador,contransportadorcnpj,concidadeorigem,conid)
						VALUES ('".$codigocte."',
						'".$nota."',
						'conhecimentos/".$filename."',
						'".$tipofrete."',
						'".$remetente."',
						'".$remetentecnpj."',
						'".addslashes($destinatario)."',
						'".$destinatariocnpj."',
						'".$data."',
						'".$conproduto."',
						'".$notaprincipal."',
						'".$remetenteuf."',
						'".$emissao."',
						'".$cidadetrasportador."',
						'".addslashes($cidadedestino)."',
						'".$destinatariouf."',
						'".$transportador."',
						'".$transportadorCNPJ."',
						'".$cidadeorigem."',
						'".$chave."'
						)";
						
					//echo "SQL - Nota ".$qtdnotas."<br>".$sql."<br>";
				}			
				
				
				//Mostrar Mensagem do SQL
				echo "<br><font size=-3>SQL - ".$sql."<br></font>";
				
				
				if(mysql_query($sql)){
					$conhecimentosadicionados++;
					
					If($transportadorcodigo != ''){
					//ATUALIZA TABELA NF COM O NOME DO TRANSPORTADOR CORRETO
						$sql="update notasfiscais n SET n.nftransportador = '".$transportadorcodigo."', 
						n.nftransportadornome = '".$transportador."' where n.nfnumero = '".$nota."'";
						//echo $sql;
						if(mysql_query($sql)){}else{
							$erros++;
							echo '<br> <font color=red>'.$erros.'º ERRO ATUALIZAÇÃO TRANSPORTADOR - '.mysql_error().' '.$filename.' || </font>';}
						}	
					}
				else{$erros++;
				echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' '.$filename.' || </font>';
				if(strpos(mysql_error(),'Registro_Unico')){
					$duplicados++;
				}
					// Atualização forçada do transportador mesmo se já tiver conhecimento já cadastrado
					If($transportadorcodigo != ''){
						//ATUALIZA TABELA NF COM O NOME DO TRANSPORTADOR CORRETO
						$sql="update notasfiscais n SET n.nftransportador = '".$transportadorcodigo."',
						n.nftransportadornome = '".$transportador."' where n.nfnumero = '".$nota."'";
						//echo $sql;
							if(mysql_query($sql)){}else{
								$erros++;
								echo '<br> <font color=red>'.$erros.'º ERRO ATUALIZAÇÃO TRANSPORTADOR - '.mysql_error().' '.$filename.' || </font>';
							}	
						}
				
				}
				
				//echo $filename;
				$qtdnotas++;
	  
			}

			IF( $qtdnotas > 1){ 
				Echo "<br><strong><font color=orange>".$qtdnotas." Quantidade de Notas Embutidas <br></strong></font>";
				
				$sql="Update conhecimentos c SET c.confretedividido ='".$notaprincipal."' where c.connota = '".$notaprincipal."'";
				
				// Atualiza nota base informando que ela possui frete dividido
				if(mysql_query($sql)){
					$sql="update conhecimentos c SET c.confrete = '".(floatval($frete)/$qtdnotas)."', c.contaxa= '".(floatval($contaxa)/$qtdnotas)."'  where c.connota = '".$notaprincipal."'
					or c.confretedividido = '".$notaprincipal."'";
					
					// Atualiza Notas pertencentes com o valor do Frete Rateado
					if(mysql_query($sql)){
						Echo "<strong><font color=green>Valor do Frete Total: ".$frete." - 
						Valor do Frete Dividido Entre Notas: ".(floatval($frete)/$qtdnotas)."  <br>
						Valor de Taxa Total: ".$contaxa." - Valor de Taxa Dividido entre Notas: ".(floatval($contaxa)/$qtdnotas)." 
						<br></strong></font>";
					}else{$erros++; echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' '.$filename.' || </font>';}
					
				}else{$erros++; echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' '.$filename.' || </font>';}
				
			}	
			
			// Não há numero de Nota
			
			//Verifica se é malote na estrutura diferente do xml
			IF($xmlobj->CTe->infCte->infCTeNorm->infDoc->infOutros->descOutros == 'MALOTE'){
				$conproduto = 'MALOTE';
			}
			
			//Verifica se é MALOTE
			
			if(( $nota == '') && ($conproduto == 'MALOTE')){
				//echo 'ENTROUUUU';
				echo "<strong><br><font color=green>MALOTE</font></strong>";
				
				$nota = "mal".$codigocte."_".substr($data,8,2)."".substr($data,5,2)."".substr($data,0,4);
				
				$sql="INSERT INTO conhecimentos(concodigocte,connota,confrete,confilename,contipofrete,conremetente,conremetentecnpj,
					condestinatario,condestinatariocnpj,condata,conproduto,convolumes,conpeso,convalorcarga,conremetenteUF,
					conemissao,concidadetransportador,concidadedestino,condestinatariouf,contransportador,contransportadorcnpj,concidadeorigem,conid)
					VALUES ('".$codigocte."',
					'".$nota."',
					'".$frete."',
					'conhecimentos/".$filename."',
					'".$tipofrete."',
					'".$remetente."',
					'".$remetentecnpj."',
					'".addslashes($destinatario)."',
					'".$destinatariocnpj."',
					'".$data."',
					'".$conproduto."',
					'".$convolumes."',
					'".$conpeso."',
					'".$convalorcarga."',
					'".$remetenteuf."',
					'".$emissao."',
					'".$cidadetrasportador."',
					'".addslashes($cidadedestino)."',
					'".$destinatariouf."',
					'".$transportador."',
					'".$transportadorCNPJ."',
					'".$cidadeorigem."',
					'".$chave."'
					)";
				
				echo "<br><font size=-3>SQL - ".$sql."<br></font>";
				
				if(mysql_query($sql)){$conhecimentosadicionados++;$malotes++;}else{$erros++;
				echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' '.$filename.' || </font>';
				if(strpos(mysql_error(),'Registro_Unico')){
					$duplicados++;
				}}
				
			}
			
			// VERIFICA SE É COBRANÇA ADICIONAL SOBRE CTE	
			if($nota == ''){
				$i = 0;
				while ($i < 6){
					//echo $filename;
					//echo $i;
					//echo $xmlobj->CTe->infCte->compl->ObsCont[$i]->xTexto;
					if(isset($xmlobj->CTe->infCte->compl->ObsCont[$i]->xTexto)){
						$descricao = $xmlobj->CTe->infCte->compl->ObsCont[$i]->xTexto;
						if(strpos($descricao, 'CTE COMP AO CTE') !== false)
						{
							//$conpeso = $xmlobj->CTe->infCte->infCTeNorm->infCarga->infQ[$i]->qCarga;
							echo "<br><strong><font size=4>VERIFICAR: ACHOU COMPLEMENTO</strong></font>";
							$nota = substr($descricao, 26,6);
							
							
							Echo "<br><strong><font size=4 color=BLUE>ALTERAR ITENS DA NF ".$nota."<br>AJUSTAR % ENVIO PARA ABSORVER TAXA DE R$ ".$contaxa."<br>REFERENTE AO CTE COMPLEMENTAR: ".$codigocte." <br></strong></font>";

							$sql="INSERT INTO conhecimentos(concodigocte,connota,confrete,confilename,contipofrete,conremetente,conremetentecnpj,
							condestinatario,condestinatariocnpj,condata,conproduto,convolumes,conpeso,convalorcarga,conremetenteUF,
							conemissao,concidadetransportador,concidadedestino,condestinatariouf,contransportador,contransportadorcnpj,concidadeorigem,conid,contaxa)
							VALUES ('".$codigocte."',
							'".$nota."',
							'".$frete."',
							'conhecimentos/".$filename."',
							'".$tipofrete."',
							'".$remetente."',
							'".$remetentecnpj."',
							'".addslashes($destinatario)."',
							'".$destinatariocnpj."',
							'".$data."',
							'".$conproduto."',
							'".$convolumes."',
							'".$conpeso."',
							'".$convalorcarga."',
							'".$remetenteuf."',
							'".$emissao."',
							'".$cidadetrasportador."',
							'".addslashes($cidadedestino)."',
							'".$destinatariouf."',
							'".$transportador."',
							'".$transportadorCNPJ."',
							'".$cidadeorigem."',
							'".$chave."',
							'".$contaxa."'
							)";
							
							echo "<br><font size=-3>SQL - ".$sql."<br></font>";
							
							if(mysql_query($sql)){$conhecimentosadicionados++;$complementos++;}else{$erros++;
							echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' '.$filename.' || </font>';
							if(strpos(mysql_error(),'Registro_Unico')){
								$duplicados++;
							}}
							
						}
						$i++;
					}else{
						$i = $i+6;  // Sai do Laço
					}
				}
			}
			
		}
		
		// VERIFICAR SE É XML DE EVENTO
			
			IF (isset($xmlobj->infEvento->chCTe)){
				$chave = $xmlobj->infEvento->chCTe;
				Echo "<br><strong><font color=DarkMagenta>".($conhecimentoscancelados+1)."º CANCELAMENTO CTE - ".$chave."</strong></font>";
				array_push($cancelar, $filename);  // adiciona a ID ao array para cancelar depois
			}
			
			IF (isset($xmlobj->eventoCTe->infEvento->chCTe)){ //Cancelamento Modelo Cadore
				$chave = $xmlobj->eventoCTe->infEvento->chCTe;
				Echo "<br><strong><font color=DarkMagenta>".($conhecimentoscancelados+1)."º CANCELAMENTO CTE CADORE- ".$chave."</strong></font>";
				array_push($cancelar, $filename);  // adiciona a ID ao array para cancelar depois
			}
			
		echo "<hr align='left' width='600' size='1' color=blue>"; //Pula Linha
	}
}
	echo "<hr align='left' width='600' size='5' color=black>"; //Pula Linha
	
	// efetuar cancelamentos

	echo "<strong><font size=20 color=DarkViolet>EFETUANDO CANCELAMENTOS</font></strong>";
	$i = 0;
	While($i < count($cancelar)){
		echo "<br> Arquivo: ".$cancelar[$i];
		$filename = $cancelar[$i];
		$xmlobj2 = simplexml_load_file($dir.'/'.$filename);
		IF ((isset($xmlobj2->infEvento->chCTe) || isset($xmlobj2->eventoCTe->infEvento->chCTe))){
			
			if(isset($xmlobj2->infEvento->chCTe)){ $chave = $xmlobj2->infEvento->chCTe; }  //Modelo Normal
			if(isset($xmlobj2->eventoCTe->infEvento->chCTe)){ $chave = $xmlobj2->eventoCTe->infEvento->chCTe;  }  // Modelo Cadore
			
			//Verifica se Existe na Base
			$sql1 = "Select * from conhecimentos where conid Like '%".$chave."%'";
			//echo $sql1;
			if($consulta = mysql_query($sql1)){
				while($registro = mysql_fetch_assoc($consulta)){
					echo '<br><strong><font color=DarkMagenta>Conhecimento Cancelado: '.$registro["concodigocte"];
					echo ' - Nota: '.$registro["connota"];
					$data = $registro["condata"];
					echo " - Emissão: ".substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
					echo ' - Transportador: '.$registro["contransportador"]."</strong></font>";
					$conhecimentoscancelados++;	
				}
			}else{
					$erros++;	echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' '.$filename.' || </font>';
			}
			// Deleta				
			$sql2 = "Delete from conhecimentos where conid like '%".$chave."%'";
			//echo $sql2;
			if(mysql_query($sql2)){		
				echo "<strong><br><mark>CONHECIMENTO ".$registro["concodigocte"]." CANCELADO</strong></mark>";
			}else{
				$erros++;	echo '<br> <font color=red>'.$erros.'º ERRO DO SQL - '.mysql_error().' '.$filename.' || </font>';
			}
			echo "<hr align='left' width='600' size='1' color=black>"; //Pula Linha
			$i++;
		}
	}
echo "<hr align='left' width='600' size='5' color=black>"; //Pula Linha
echo "<br><strong> Quantidade de Arquivos Lidos: ".$arquivo;
echo "<br> Quantidade de Conhecimentos Adicionados: ".$conhecimentosadicionados;
echo "<br> Quantidade de Malotes: ".$malotes;
echo "<br> Quantidade de Conhecimentos Cancelados: ".$conhecimentoscancelados;
echo "<br> Quantidade de Conhecimentos Duplicados: ".$duplicados;
if($complementos > 0){ 
		echo "<hr align='left' width='600' size='10' color=black>"; //Pula Linha
		echo "<br><font color='DarkViolet' size=10> Quantidade de Conhecimentos de Complementos de Tarifa: </font>".$complementos;  
	}else{
		echo "<br> Quantidade de Conhecimentos de Complementos de Tarifa:".$complementos;
	}

echo "<hr align='left' width='600' size='1' color=crimson>"; //Pula Linha
echo "<br><strong><font size=4 color=blue>PESQUISAR PALAVRA --> VERIFICAR <-- PARA LOCALIZAR INCONSISTÊNCIAS</strong></font>";
echo "<br> Quantidade de Erros: ".$erros;
echo "<br><font size=4 color=crimson>CONFERIR NA PESQUISA SE QUANTIDADE DE ERROS 'REGISTRO_UNICO' É IGUAL A QUANTIDADE DE ERROS</font>";
echo "<br><font size=4 color=crimson>CONFERIR TAMBEM A MENSAGEM 'You have an error in your SQL syntax'</font>";
?>