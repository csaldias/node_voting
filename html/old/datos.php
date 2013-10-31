<?php
$info=file_get_contents("info.txt");

$arr=explode('|',$lineas[1]);
$contados=0;
$total=2153;

$vot['si']['tot']=intval(file_get_contents("listam.txt"));
$vot['no']['tot']=intval(file_get_contents("listaz.txt"));
$vot['null']['tot']=intval(file_get_contents("null.txt"));
$vot['blanco']['tot']=intval(file_get_contents("blanco.txt"));
$vot['tot']['tot']=$vot['si']['tot']+$vot['no']['tot']+$vot['blanco']['tot']+$vot['null']['tot'];

$vot['si']['por']='0.00';
$vot['no']['por']='0.00';
$vot['null']['por']='0.00';
$vot['blanco']['por']='0.00';

if($vot['tot']['tot']>0){
	$vot['si']['por']=number_format((float)(($vot['si']['tot']/$vot['tot']['tot'])*100),2);
	$vot['no']['por']=number_format((float)(($vot['no']['tot']/$vot['tot']['tot'])*100),2);
	$vot['null']['por']=number_format((float)(($vot['null']['tot']/$vot['tot']['tot'])*100),2);
	$vot['blanco']['por']=number_format((float)(($vot['blanco']['tot']/$vot['tot']['tot'])*100),2);
	$vot['tot']['por']='100.00';
}

if($total!=0)
	$porcentaje=number_format((float)(($vot['tot']['tot']/$total)*100),2);
else
	$porcentaje=0;
	

$color="red";
$msjq='';
if($porcentaje>=30){
	$color="rgb(146,208,80)";
}else{
  //if($porcentaje>0)
  //  $msjq='No se ha alcanzado el Qu&oacute;rum m&iacute;nimo.';
}




?>
								<div class="message"><p><b>ESTADO ACTUAL:</b> <?php echo $info; ?></p><?php if(!empty($msjq)){ ?><p><?php echo $msjq; ?></p><?php } ?></div>
								<div class="bar-body"><div class="bar-inside" style="width:<?php echo $porcentaje; ?>%;background:<?php echo $color; ?>"></div></div>
								<div id="contando">Quorum <?php echo $porcentaje; ?>% (<?php echo $vot['tot']['tot']; ?>/2153)</div>
								<div style="clear:both"></div>
								<br/>
								<b>RESULTADO FINAL</b><br/><br/>
								<div id="torta" style="text-align:center;width:331px;height:141px;">
									<?php if($vot['tot']['tot']>0){ ?><img width="331" height="141" src="/graficos/graphpastel.php?wdt=330&hgt=120&dat=<?php echo $vot['si']['tot']; ?><?php if($vot['no']['tot']>0) echo ','.$vot['no']['tot']; ?><?php if($vot['blanco']['tot']>0) echo ','.$vot['blanco']['tot']; ?><?php if($vot['null']['tot']>0) echo ','.$vot['null']['tot']; ?>" /> <?php }else{ ?><img width="331" height="141" src="/graficos/graphpastel.php?wdt=330&hgt=120&dat=0,0,0,0,0,1" /><?php }?>
								</div>
								<br />
								<table style="width:100%">
									<tr>
										<td style="width:1px;">
											<img src="/graficos/graphref.php?ref=5&typ=1&dim=5&bkg=FFFFFF" width="11" height="11" />
										</td>
										<td style="">
											<b>PARO</b>
										</td>
										<td style="text-align:right;width:120px;">
											<?php echo $vot['si']['tot']; ?> voto<?php if($vot['si']['tot']!=1) echo 's'; ?>
										</td>
										<td style="text-align:right;width:120px;">
											<?php echo $vot['si']['por']; ?>%
										</td>
									</tr>
									<tr>
										<td>
											<img src="/graficos/graphref.php?ref=8&typ=1&dim=5&bkg=FFFFFF" width="11" height="11" />
										</td>
										<td>
											<b>CLASES</b>
										</td>
										<td style="text-align:right;width:120px;">
											<?php echo $vot['no']['tot']; ?> voto<?php if($vot['no']['tot']!=1) echo 's'; ?>
										</td>
										<td style="text-align:right;width:120px;">
											<?php echo $vot['no']['por']; ?>%
										</td>
									</tr>
									<tr>
										<td>
											<img src="/graficos/graphref.php?ref=14&typ=1&dim=5&bkg=FFFFFF" width="11" height="11" />
										</td>
										<td>
											<b>Blancos</b>
										</td>
										<td style="text-align:right;width:120px;">
											<?php echo $vot['blanco']['tot']; ?> voto<?php if($vot['blanco']['tot']!=1) echo 's'; ?>
										</td>
										<td style="text-align:right;width:120px;">
											<?php echo $vot['blanco']['por']; ?>%
										</td>
									</tr>
									<tr>
										<td>
                      <img src="/graficos/graphref.php?ref=11&typ=1&dim=5&bkg=FFFFFF" width="11" height="11" />
										</td>
										<td>
											<b>Nulos</b>
										</td>
										<td style="text-align:right;width:120px;">
											<?php echo $vot['null']['tot']; ?> voto<?php if($vot['null']['tot']!=1) echo 's'; ?>
										</td>
										<td style="text-align:right;width:120px;">
                      <?php echo $vot['null']['por']; ?>%
										</td>
									</tr>
								</table>