<script>
	top.location="./";
</script>
<?php

$data=file_get_contents("info.txt");
$data=str_replace("\n","",$data);
$lineas=explode("@",$data);

$arr=explode('|',$lineas[0]);
$info=$arr[0];

$arr=explode('|',$lineas[1]);
$contados=0;
$total=$arr[1];

$votos=file_get_contents("data.txt");
$votos=str_replace("\n","",$votos);
$lineas=explode("@",$votos);

$vot['apr']['tot']=0;
$vot['rec']['tot']=0;
$vot['nob']['tot']=0;
$vot['obj']['tot']=0;
$vot['tot']['tot']=0;

$vot['apr']['por']='0.00';
$vot['rec']['por']='0.00';
$vot['nob']['por']='0.00';


foreach($lineas AS $l){
	
	$arr=explode('|',$l);
	
	if(!empty($arr[5])){
		$vot[$arr[5]]['apr']=$arr[1];
		$vot[$arr[5]]['rec']=$arr[2];
		$vot[$arr[5]]['nob']=$arr[3];
		$vot[$arr[5]]['obj']=$arr[4];
		$vot[$arr[5]]['tot']=$arr[1]+$arr[2]+$arr[3];
		
		$contados+=$vot[$arr[5]]['tot'];
		
		$vot['apr']['tot']+=intval($arr[1]);
		$vot['rec']['tot']+=intval($arr[2]);
		$vot['nob']['tot']+=intval($arr[3]);
		$vot['obj']['tot']+=intval($arr[4]);
		$vot['tot']['tot']+=intval($arr[1]+$arr[2]+$arr[3]);
	}

}

if($vot['tot']['tot']>0){
	$vot['apr']['por']=number_format((float)(($vot['apr']['tot']/$vot['tot']['tot'])*100),2);
	$vot['rec']['por']=number_format((float)(($vot['rec']['tot']/$vot['tot']['tot'])*100),2);
	$vot['nob']['por']=number_format((float)(($vot['nob']['tot']/$vot['tot']['tot'])*100),2);
}


if($total!=0)
	$porcentaje=($contados/$total)*100;
else
	$porcentaje=0;
	






?>
								<div class="message"><b>ESTADO ACTUAL:</b> <?php echo $info; ?></div>
								<div class="bar-body"><div class="bar-inside" style="width:<?php echo $porcentaje; ?>%"></div></div>
								<div id="cero">0</div>
								<div id="contando"><?php echo $contados; ?> voto<?php if($contados!=1) echo 's';?> contado<?php if($contados!=1) echo 's';?></div>
								<div id="total"><?php echo $total; ?> voto<?php if($total!=1) echo 's';?> total</div>
								<div style="clear:both"></div>
								<div id="torta">
									<b>RESULTADO PARCIAL</b><br/><br/>
									<div style="width:371px;height:171px;"><img src="http://participa.ceeinf.cl/images/graficos/graphpastel.php?dat=<?php echo $vot['apr']['tot']; ?><?php if($vot['rec']['tot']!=0) echo ','.$vot['rec']['tot']; ?><?php if($vot['nob']['tot']!=0) echo ','.$vot['nob']['tot']; ?>" /> </div>
								</div>
								<div id="detalle">
									<img src="http://participa.ceeinf.cl/images/graficos/graphref.php?ref=5&typ=1&dim=5&bkg=FFFFFF" /> <?php echo $vot['apr']['tot']; ?> Acepto (<?php echo $vot['apr']['por']; ?>%)<br />
									<img src="http://participa.ceeinf.cl/images/graficos/graphref.php?ref=8&typ=1&dim=5&bkg=FFFFFF" /> <?php echo $vot['rec']['tot']; ?> Rechazo (<?php echo $vot['rec']['por']; ?>%)<br />
									<img src="http://participa.ceeinf.cl/images/graficos/graphref.php?ref=11&typ=1&dim=5&bkg=FFFFFF" /> <?php echo $vot['nob']['tot']; ?> Null/Blanco (<?php echo $vot['nob']['por']; ?>%)<br />
								</div>
								<div style="clear:both"></div>
								<b>POR CARRERA</b><br/>&nbsp;
								<table id="porcarrera">
									<tr id="columnas">
										<td>Carrera</td>
										<td>Acepto</td>
										<td>Rechazo</td>
										<td>Null/Blanco</td>
										<td>Objetado</td>
										<td>Total</td>
									</tr>
									<tr>
										<td>Informática</td>
										<td><?php echo $vot['INF']['apr']; ?></td>
										<td><?php echo $vot['INF']['rec']; ?></td>
										<td><?php echo $vot['INF']['nob']; ?></td>
										<td><?php echo $vot['INF']['obj']; ?></td>
										<td><?php echo $vot['INF']['tot']; ?></td>
									</tr>
									<tr>
										<td>Mecánica</td>
										<td><?php echo $vot['MEC']['apr']; ?></td>
										<td><?php echo $vot['MEC']['rec']; ?></td>
										<td><?php echo $vot['MEC']['nob']; ?></td>
										<td><?php echo $vot['MEC']['obj']; ?></td>
										<td><?php echo $vot['MEC']['tot']; ?></td>
									</tr>
									<tr>
										<td>Química</td>
										<td><?php echo $vot['QUI']['apr']; ?></td>
										<td><?php echo $vot['QUI']['rec']; ?></td>
										<td><?php echo $vot['QUI']['nob']; ?></td>
										<td><?php echo $vot['QUI']['obj']; ?></td>
										<td><?php echo $vot['QUI']['tot']; ?></td>
									</tr>
									<tr>
										<td>Plan Común</td>
										<td><?php echo $vot['COM']['apr']; ?></td>
										<td><?php echo $vot['COM']['rec']; ?></td>
										<td><?php echo $vot['COM']['nob']; ?></td>
										<td><?php echo $vot['COM']['obj']; ?></td>
										<td><?php echo $vot['COM']['tot']; ?></td>
									</tr>
									<tr>
										<td>Eléctrica</td>
										<td><?php echo $vot['ELE']['apr']; ?></td>
										<td><?php echo $vot['ELE']['rec']; ?></td>
										<td><?php echo $vot['ELE']['nob']; ?></td>
										<td><?php echo $vot['ELE']['obj']; ?></td>
										<td><?php echo $vot['ELE']['tot']; ?></td>
									</tr>
									<tr>
										<td>Civil</td>
										<td><?php echo $vot['CIV']['apr']; ?></td>
										<td><?php echo $vot['CIV']['rec']; ?></td>
										<td><?php echo $vot['CIV']['nob']; ?></td>
										<td><?php echo $vot['CIV']['obj']; ?></td>
										<td><?php echo $vot['CIV']['tot']; ?></td>
									</tr>
									<tr>
										<td><b>Total</b></td>
										<td><b><?php echo $vot['apr']['tot']; ?></b></td>
										<td><b><?php echo $vot['rec']['tot']; ?></b></td>
										<td><b><?php echo $vot['nob']['tot']; ?></b></td>
										<td><b><?php echo $vot['obj']['tot']; ?></b></td>
										<td><b><?php echo $vot['tot']['tot']; ?></b></td>
									</tr>
								</table>