// node_voting: Votaciones en tiempo real.

var express   = require('express'),
    app       = express(),
    server    = require('http').createServer(app),
    io        = require('socket.io').listen(server),
    db_redis  = require('redis'),
    db_client = db_redis.createClient();

server.listen(8080);

db_client.on('connect', function () {
    console.log('Redis DB Connected.');
});

app.use(express.static(__dirname + '/html'));

var metadata = {};
var viewers = 0;

metadata['num_students'] = 2135;
metadata['quorum'] = 30;

function resetCounts() {
  //votes:1 -> Votacion paralizacion CONFECH Miercoles
  //votes:2 -> Votacion paralizacion interna, y forma de paralizacion
  //msg     -> Mensaje de estado

  //Creamos votes:1 en la BD si no existe.
  db_client.exists('votes:1', function (err, reply) {
    if (reply == 1) {
      console.log('votes:1 already exists.');
    } else {
      console.log('Creating DB votes:1 ...');
      db_client.hmset('votes:1', 'si', 0, 'no', 0, 'nulos', 0, 'blancos', 0);
    }
  });
  
  //Creamos votes:2 en la BD si no existe.
  db_client.exists('votes:2', function (err, reply) {
    if (reply == 1) {
      console.log('votes:2 already exists.');
    } else {
      console.log('Creating DB votes:2 ...');
      db_client.hmset('votes:2', 'si', 0, 'no', 0, 'nulos', 0, 'blancos', 0, 'def', 0, 'indef', 0);
    }
  });

  //Creamos msg en la BD si no existe.
  db_client.exists('msg', function (err, reply) {
    if (reply == 1) {
      console.log('msg already exists.');
    } else {
      console.log('Creating DB msg ...');
      db_client.set('msg', '');
    }
  });
}

resetCounts();

//Cuando exista una conexion por WebSockets...
io.sockets.on('connection', function (socket) {

  //Funcion de actualizacion de votos +1 (Redis-friendly)
  socket.on('upvote', function (poll, choice) {
    console.log("Upvoted "+poll+","+choice+".");
    db_client.hincrby(poll, choice, 1);
    //Obtenemos los resultados de la primera votacion
    db_client.hgetall("votes:1", function (err_1, votes_1) {
      db_client.hgetall("votes:2", function (err_1, votes_2) {
        io.sockets.emit('update_counts', votes_1, votes_2);
      });
    });
  });

  //Funcion de actualizacion de votos -1 (Redis-friendly)
  socket.on('downvote', function (poll, choice) {
    console.log("Downvoted "+poll+","+choice+".");
    db_client.hincrby(poll, choice, -1);
    var votes_1={}, votes_2={};
    //Obtenemos los resultados de la primera votacion
    db_client.hgetall("votes:1", function (err_1, votes_1) {
      db_client.hgetall("votes:2", function (err_1, votes_2) {
        io.sockets.emit('update_counts', votes_1, votes_2);
      });
    });
  });
  
  //Funcion de actualizacion de estado (Redis-friendly)
  socket.on('update_status', function (msg) {
    console.log("Updated status message: "+msg);
    db_client.set('msg', msg);
    io.sockets.emit('update_status', msg);
  });

  //Actualizacion inicial de valores
  socket.on('addstream', function(){ 
    //Actualizamos la cantidad de viewers
    viewers += 1;
    
    //Enviamos los metadatos
    socket.emit('update_metadata', metadata);
    
    //Enviamos los resultados de las votaciones
    //Obtenemos los resultados de la primera votacion
    db_client.hgetall("votes:1", function (err_1, votes_1) {
      db_client.hgetall("votes:2", function (err_1, votes_2) {
        socket.emit('update_counts', votes_1, votes_2);
      });
    });

    //Enviamos el mensaje actual
    db_client.get("msg", function (err, reply) {
      socket.emit('update_status', reply);
    });
  });

  socket.on('disconnect', function (data){
    viewers -= 1;
  });
});