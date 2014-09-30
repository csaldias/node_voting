//votaciones_ceeinf: Votaciones en tiempo real.

var express = require('express'),
	app = express(),
    server = require('http').createServer(app),
    io = require('socket.io').listen(server);

server.listen(8080);

app.use(express.static(__dirname + '/html'));

//Definimos todas las variables que vamos a usar.

//Total de alumnos de Campus Santiago San Joaquín. (Actualizar pls)
var totalAlumnos = 2135;
//Cantidad total de votos contabilizados.
var totalVotos = 0;
// Cantidad de clientes conectados.
var viewers = 0;
//Cantidad de votos a favor de PARO.
var votosParo = 0;
//cantidad de votos a favor de CLASES.
var votosClases = 0;
//Cantidad de votos NULOS.
var votosNulos = 0;
//Cantidad de votos BLANCOS.
var votosBlanco = 0;
//Estado personalizado.
var estado = '';

//Porcentajes de cada opcion con respecto al total.
var porcentajeParo = 0;
var porcentajeClases = 0;
var porcentajeNulo = 0;
var porcentajeBlanco = 0;
var porcentajeVotos = 0;

function updatePorcentajes () {
	totalVotos = votosParo + votosClases + votosNulos + votosBlanco;

	porcentajeParo = ((votosParo*100)/totalVotos).toFixed(2);
	porcentajeClases = ((votosClases*100)/totalVotos).toFixed(2);
	porcentajeNulo = ((votosNulos*100)/totalVotos).toFixed(2);
	porcentajeBlanco = ((votosBlanco*100)/totalVotos).toFixed(2);
	porcentajeVotos = ((totalVotos*100)/totalAlumnos).toFixed(2);
}

io.sockets.on('connection', function (socket) {

	//Cuando el controlador actualiza el conteo:
	socket.on('updatevoto', function (data, valor) {
		// Actualizamos el contador dependiendo del voto elegido.
		if (data == 'paro') {
			//Actualizamos la cantidad de votos PARO.
			votosParo = parseInt(valor);
			//Actualizamos el porcentaje de la opción sobre el total
			updatePorcentajes();
			//Y ahora hacemos "push" de los nuevos datos a todos los clientes conectados.
			socket.broadcast.emit('updatecuenta', 'paro', votosParo);
			socket.broadcast.emit('updateporcentajes', porcentajeParo, porcentajeClases, porcentajeNulo, porcentajeBlanco);
			socket.broadcast.emit('updatequorum', totalAlumnos, totalVotos, porcentajeVotos);
			socket.broadcast.emit('plot', votosParo, votosClases, votosBlanco, votosNulos);
		} else if (data == 'clases') {
			//Actualizamos la cantidad de votos CLASES.
			votosClases = parseInt(valor);
			//Actualizamos el porcentaje de la opción sobre el total
			updatePorcentajes();
			//Y actualizamos el conteo de todos los viewers.
			socket.broadcast.emit('updatecuenta', 'clases', votosClases);
			socket.broadcast.emit('updateporcentajes', porcentajeParo, porcentajeClases, porcentajeNulo, porcentajeBlanco);
			socket.broadcast.emit('updatequorum', totalAlumnos, totalVotos, porcentajeVotos);
			socket.broadcast.emit('plot', votosParo, votosClases, votosBlanco, votosNulos);
		} else  if (data == 'nulo') {
			//...Ya es como obvio el flujo a estas alturas.
			votosNulos = parseInt(valor);
			updatePorcentajes();
			socket.broadcast.emit('updatecuenta', 'nulo', votosNulos);
			socket.broadcast.emit('updateporcentajes', porcentajeParo, porcentajeClases, porcentajeNulo, porcentajeBlanco);
			socket.broadcast.emit('updatequorum', totalAlumnos, totalVotos, porcentajeVotos);
			socket.broadcast.emit('plot', votosParo, votosClases, votosBlanco, votosNulos);
		} else {
			//...Ya es como obvio el flujo a estas alturas.
			votosBlanco = parseInt(valor);
			updatePorcentajes();
			socket.broadcast.emit('updatecuenta', 'blanco', votosBlanco);
			socket.broadcast.emit('updateporcentajes', porcentajeParo, porcentajeClases, porcentajeNulo, porcentajeBlanco);
			socket.broadcast.emit('updatequorum', totalAlumnos, totalVotos, porcentajeVotos);
			socket.broadcast.emit('plot', votosParo, votosClases, votosBlanco, votosNulos);
		}
	});

	//Cuando el controlador solicita reiniciar el contador:
	socket.on('reset', function() {
		//Reiniciamos todos los valores...
		totalVotos = 0;
		VotosParo = 0;
		votosClases = 0;
		votosNulos = 0;
		votosBlanco = 0;
		estado = "";
		porcentajeToma = 0;
		porcentajeClases = 0;
		porcentajeNulo = 0;
		porcentajeBlanco = 0;
		quorum = 0;

		//Y enviamos los nuevos valores a todos los clientes conectados.
		io.sockets.emit('updatecuenta', 'paro', VotosParo);
		io.sockets.emit('updatecuenta', 'clases', votosClases);
		io.sockets.emit('updatecuenta', 'nulo', votosNulosBlancos);
		io.sockets.emit('updatecuenta', 'blanco', votosBlanco);
		io.sockets.emit('updateporcentajes', porcentajeParo, porcentajeClases, porcentajeNulo, porcentajeBlanco);
		io.sockets.emit('updatequorum', totalAlumnos, totalVotos, porcentajeVotos);
		io.sockets.emit('plot', votosParo, votosClases, votosBlanco, votosNulos);
		io.sockets.emit('getstatus', estado);
	});
	
	//Cuando el controlador actualiza el estado:
	socket.on('updatestatus', function (data) {
		//Guardamos el nuevo estado...
		estado = data;
		//Y hacemos "push" a todos los viewers.
		socket.broadcast.emit('getstatus', estado);
	});

	//Cuando un nuevo viewer se une, enviamos los valores de votos actuales.
	socket.on('addstream', function(){
		//Acualizamos la cantidad de personas viendo el stream
		viewers = viewers + 1;
		//Y enviamos la nueva cantidad a todos los viewers + los valores de las votaciones.
		io.sockets.emit('updateviewers', viewers);
		socket.emit('updatecuenta', 'paro', votosParo);
		socket.emit('updatecuenta', 'clases', votosClases);
		socket.emit('updatecuenta', 'nulo', votosNulos);
		socket.emit('updatecuenta', 'blanco', votosBlanco);
		socket.emit('updateporcentajes', porcentajeParo, porcentajeClases, porcentajeNulo, porcentajeBlanco);
		socket.emit('updatequorum', totalAlumnos, totalVotos, porcentajeVotos);
		socket.emit('plot', votosParo, votosClases, votosBlanco, votosNulos);
		socket.emit('getstatus', estado);
	});

	//Cuando alguien se desconecta:
	socket.on('disconnect', function (data){
		//Restamos 1 de la cuenta de conexiones
		viewers = viewers - 1;
		//Y actualizamos a todos.
		socket.broadcast.emit('updateviewers', viewers);
	});
});
