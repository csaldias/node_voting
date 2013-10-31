<html>
	<head>
		<title>Conteo de votos Paralización de la #UTFSM Campus San Joaquín | CEEINF</title>
		<link rel="stylesheet" type="text/css" media="all" href="style.css" />
		<script src="jquery.min.js"></script>
    <meta property="og:title" content="Conteo de votos Paralización de la #UTFSM Campus San Joaquín" /> 
    <meta property="og:image" content="https://dl.dropboxusercontent.com/u/26047121/CEEINF/ceeinf_logo_cuadrado.png" /> 
    <meta property="og:description" content="¡Sigue en directo, desde las 18:30 hrs del miércoles 26 de junio de 2013, el conteo de votos para la paralización de actividades por 5 días de la UTFSM Campus San Joaquín!" /> 
    <meta property="og:url" content="http://votacion.ceeinf.cl/">
	</head>
	<body>
	
		<table id="conteo-table">
			<tr>
				<td>
					<table id="logo-info">
						<tr>
							<td id="logo">
								<p class="big">Paralización de Actividades<br />27 de junio al 3 de julio<br />UTFSM Campus San Joaquín</p>
							</td>
							<td id="info">
								<header>CONTEO DE VOTOS</header>
								<div id="load"></div>
							</td>
						</tr>
					</table>
					<footer><b><a href="http://www.ceeinf.cl/?home">Ir a la página principal de CEEINF >></a></b></footer>					
				</td>
			</tr>
		</table>
	<script>
		function reload(){
			$.get('datos.php',{},function(data){
				pre = data;
        $('#load').html(pre);
			});
			//setTimeout('reload()',5000);
		}
		reload();</script>
	</body>
</html>