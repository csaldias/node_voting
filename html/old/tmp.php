<?php
$servidor="localhost";
$usuario="ceeinf";
$contrasena="is9faiVu";
$db="ceeinf01";
$link = mysql_connect($servidor, $usuario, $contrasena);
mysql_select_db($db, $link);
mysql_query("INSERT INTO tmp_visitas (ip) VALUES ('".$_SERVER['REMOTE_ADDR']."')");
echo "INSER INTO tmp_visitas (ip) VALUES ('".$_SERVER['REMOTE_ADDR']."')";
?>