<?
Header( "Content-type: image/png");
//Header( "Content-type: image/jpeg");
require("grab_globals.lib.php");

if($bkg == "") $bkg="FFFFFF";
if($wdt == "") $wdt=30;

// determina las posiciones de los lados del cubo
($wdt <= 30)?$equis1 = 30:$equis1 = $wdt / 2;
$equis2 = $equis1 + $wdt;

// determina el acho total de la imagen
$ladoDrch = $equis2 + 55 + ($equis1/2);

/* crea imagen */
$image = imagecreate($ladoDrch,236);

// librerias de Colores y Funciones
include('libcolores.php');
include('libfunciones.php');

// ajusta los valores recibidos
if ($fil > 100) $fil = 100;
if ($fil < 0) $fil = 0;

ImageColorAllocate($image,0,0,0);

sscanf($bkg, "%2x%2x%2x", $rr, $gg, $bb);
$colorbkg = ImageColorAllocate($image,$rr,$gg,$bb);

// crea bkg con color recibido
ImageFilledRectangle($image,0,0,$ladoDrch,236,$colorbkg);

	// color de base
	$puntos = array();
	$puntos = CargaEsquinas(24, 211, 0, 235, $ladoDrch - 24, 235, $ladoDrch, 211);
	imagefilledpolygon($image, $puntos, 4, $colores[3]);


	// lineas y porcentajes
	for ($lineas = 11,$porciento=100; $lineas<= 211; $lineas+=20,$porciento-=10) {
	ImageLine($image,24,$lineas,$ladoDrch,$lineas,$colores[3]);
	switch($porciento){
		case 100:$color=$colores[5];break;
		case 0:$color=$colores[12];break;
		default:$color=$colores[2];}
	ImageString($image,2,$equis2 + 20,$lineas,$porciento." %",$color);
	}

	// contorno de la imagen
	$puntos[0] = 24;
	$puntos[1] = 0;
	$puntos[2] = 24;
	$puntos[3] = 211;
	$puntos[4] = 0;
	$puntos[5] = 235;
	$puntos[6] = $ladoDrch - 25;
	$puntos[7] = 235;
	$puntos[8] = $ladoDrch - 1;
	$puntos[9] = 211;
	$puntos[10] = $ladoDrch - 1;
	$puntos[11] = 0;
	imagepolygon($image, $puntos, 6, $colores[0]);

	// convierte el total fil a porcentaje
	$porcentajefil = (($fil)*200)/100;
	$total = 100;
	$porcentajetotal = (($total)*200)/100;

	// contorno gris parte de atras
	ImageRectangle($image,$equis1+15,11+(200-$porcentajetotal),$equis2+15,210,$colores[2]);

	// verde superior, claro
	$puntos = CargaEsquinas($equis1 + 15, 11+(200-$porcentajefil), $equis1+1, 25+(200-$porcentajefil), $equis2+1, 25+(200-$porcentajefil), $equis2 + 15, 11+(200-$porcentajefil));
	imagefilledpolygon($image, $puntos, 4, $colores[13]);

	// rellena lados del cubo siempre cuando el porcentaje no sea cero
	if ($fil != 0) {
		// verde costado derecho, oscuro
		$puntos = CargaEsquinas($equis2, 26+(200-$porcentajefil), $equis2, 224, $equis2 + 15, 210, $equis2 + 15, 11+(200-$porcentajefil));
		imagefilledpolygon($image, $puntos, 4, $colores[12]);

		// verde frontal
		ImageFilledRectangle($image,$equis1,26+(200-$porcentajefil),$equis2,224,$colores[11]);
	} else {
		// contorno gris de base
		$puntos = CargaEsquinas($equis1 + 15, 210, $equis1, 225, $equis2, 225, $equis2 + 15, 210);
		imagepolygon($image, $puntos, 4, $colores[2]);
	}

	// contorno gris frontal
	ImageRectangle($image,$equis1,26+(200-$porcentajetotal),$equis2,225,$colores[2]);

	// contorno gris del lado oscuro
	$puntos = CargaEsquinas($equis2, 26+(200-$porcentajetotal), $equis2, 225, $equis2 + 15, 210, $equis2 + 15, 11+(200-$porcentajetotal));
	imagepolygon($image, $puntos, 4, $colores[2]);

	// contorno gris superior, el tope
	$puntos = CargaEsquinas($equis1 + 15, 11+(200-$porcentajetotal), $equis1, 26+(200-$porcentajetotal), $equis2, 26+(200-$porcentajetotal), $equis2 + 15, 11+(200-$porcentajetotal));
	imagepolygon($image, $puntos, 4, $colores[2]);

/*  render image */
ImagePNG($image);
//ImageJPEG($image);

/* cleanup memory */
ImageDestroy($image);
?>