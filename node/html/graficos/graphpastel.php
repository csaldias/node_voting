<?php
header( "Content-type: image/png");
//Header( "Content-type: image/jpeg");
require("grab_globals.lib.php");

if($bkg == "") $bkg="FFFFFF";
if($wdt == "") $wdt=200;
if($hgt == "") $hgt=100;

	$por = array();
	$dato = array();
	$dato = split(",", str_replace(" ","",$dat));

	/* crea imagen */
	$image = imagecreate($wdt +1,$hgt+21);

	// librerias de Colores y Funciones
	include('libcolores.php');
	include('libfunciones.php');

	sscanf($bkg, "%2x%2x%2x", $rr, $gg, $bb);
	$colorbkg = ImageColorAllocate($image,$rr,$gg,$bb);

	// crea bkg blanco
	ImageFilledRectangle($image,0,0,$wdt +1,$hgt+21,$colorbkg);

	$nvars = 0;
	foreach ($dato as $valor) {
  		$total += $dato[$i];
		$nvars++;
	}

	for ($i = 0;$i < $nvars;$i++)
		$total += $dato[$i];

	for ($i = 0;$i < $nvars;$i++)
		$por[$i] = ($dato[$i] * 360) / $total;

	$inicio = 0;
	$final = 0;

	for ($j = ($hgt/2)+15;$j > $hgt/2;$j--) {
		for ($i = 0, $c = 6;$i < $nvars;$i++,$c+=3) {
			$final += $por[$i];
			imagefilledarc ($image, $wdt/2, $j, $wdt, $hgt, $inicio, $final, $colores[$c], IMG_ARC_PIE);
			$inicio = $final;
		}
	}

	$inicio = 0;
	$final = 0;

	for ($i = 0, $c = 5;$i < $nvars;$i++, $c+=3) {
		$final += $por[$i];
		imagefilledarc ($image, $wdt/2, $hgt/2, $wdt, $hgt, $inicio, $final, $colores[$c], IMG_ARC_PIE);
		$inicio = $final;
	}

/* Realiza la generacion del grafico */
ImagePNG($image);
//ImageJPEG($image,'',100);

/* Vacia la memoria */
ImageDestroy($image);
?>