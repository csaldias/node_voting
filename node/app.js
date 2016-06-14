var express = require('express'),
    cluster = require('cluster'),
    net = require('net'),
    sio = require('socket.io'),
    sio_redis = require('socket.io-redis');

var port = 3000,
    num_processes = require('os').cpus().length;

if (cluster.isMaster) {
    // This stores our workers. We need to keep them to be able to reference
    // them based on source IP address. It's also useful for auto-restart,
    // for example.
    var workers = [];

    // Helper function for spawning worker at index 'i'.
    var spawn = function(i) {
        workers[i] = cluster.fork();

        // Optional: Restart worker on exit
        workers[i].on('exit', function(worker, code, signal) {
            console.log('respawning worker', i);
            spawn(i);
        });
    };

    // Spawn workers.
    console.log(num_processes, 'threads.');
    for (var i = 0; i < num_processes; i++) {
        spawn(i);
    }

    // Helper function for getting a worker index based on IP address.
    // This is a hot path so it should be really fast. The way it works
    // is by converting the IP address to a number by removing the dots,
    // then compressing it to the number of slots we have.
    //
    // Compared against "real" hashing (from the sticky-session code) and
    // "real" IP number conversion, this function is on par in terms of
    // worker index distribution only much faster.
    var worker_index = function(ip, len) {
        var s = '';
        for (var i = 0, _len = ip.length; i < _len; i++) {
            if (ip[i] !== '.') {
                s += ip[i];
            }
        }

        return Number(s) % len;
    };

    // Create the outside facing server listening on our port.
    var server = net.createServer({ pauseOnConnect: true }, function(connection) {
        // We received a connection and need to pass it to the appropriate
        // worker. Get the worker for this connection's source IP and pass
        // it the connection.
        var worker = workers[worker_index(connection.remoteAddress, num_processes)];
        worker.send('sticky-session:connection', connection);
    }).listen(port);
} else {
    // Note we don't use a port here because the master listens on it for us.
    var app = new express();

    // Here you might use middleware, attach routes, etc.

    // Don't expose our internal server to the outside.
    var server = app.listen(0, 'localhost'),
        io = sio(server);

    // Tell Socket.IO to use the redis adapter. By default, the redis
    // server is assumed to be on localhost:6379. You don't have to
    // specify them explicitly unless you want to change them.
    io.adapter(sio_redis({ host: 'localhost', port: 6379 }));

    // Here you might use Socket.IO middleware for authorization etc.
    var db_redis  = require('redis'),
        db_client = db_redis.createClient();

    db_client.on('connect', function () {
       console.log('Redis DB Connected.');
    });

    var metadata = {};
    var viewers = 0;

    metadata['num_students'] = 2593;
    metadata['quorum'] = 30;

    var resetCounts = function() {
      //votes:1 -> Votacion paro definido
      //votes:2 -> Votacion paro marcha CONFECh
      //msg     -> Mensaje de estado

      //Creamos votes:1 en la BD si no existe.
      db_client.exists('votes:1', function (err, reply) {
        if (reply == 1) {
          console.log('votes:1 already exists.');
        } else {
          console.log('Creating DB votes:1 ...');
          db_client.hmset('votes:1', 'si', 0, 'no', 0, 'nulo', 0, 'blanco', 0);
        }
      });
      
      //Creamos votes:2 en la BD si no existe.
      db_client.exists('votes:2', function (err, reply) {
        if (reply == 1) {
          console.log('votes:2 already exists.');
        } else {
          console.log('Creating DB votes:2 ...');
          db_client.hmset('votes:2', 'si', 0, 'no', 0, 'nulo', 0, 'blanco', 0);
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

      //Creamos msg en la BD si no existe.
      db_client.exists('conn', function (err, reply) {
        if (reply == 1) {
          console.log('conn already exists.');
        } else {
          console.log('Creating DB conn ...');
          db_client.set('conn', 0);
        }
      });
    };

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

      socket.on('upvote10', function (poll, choice) {
        console.log("Upvoted 10 "+poll+","+choice+".");
        db_client.hincrby(poll, choice, 10);
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

      socket.on('downvote10', function (poll, choice) {
        console.log("Downvoted 10 "+poll+","+choice+".");
        db_client.hincrby(poll, choice, -10);
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
        db_client.incrby('conn', 1);
        db_client.get("conn", function (err_1, num_conn) {
          io.sockets.emit('connections', num_conn);
        });

        
        //Enviamos los metadatos
        socket.emit('update_metadata', metadata);
        
        //Enviamos los resultados de las votaciones
        //Obtenemos los resultados de la primera votacion
        db_client.hgetall("votes:1", function (err_1, votes_1) {
          db_client.hgetall("votes:2", function (err_1, votes_2) {
              io.sockets.emit('update_counts', votes_1, votes_2);
          });
        });

        //Enviamos el mensaje actual
        db_client.get("msg", function (err, reply) {
          socket.emit('update_status', reply);
        });
      });
      
      socket.on('disconnect', function (data){
        //Actualizamos la cantidad de viewers
        db_client.incrby('conn', -1);
        db_client.get("conn", function (err_1, num_conn) {
          io.sockets.emit('connections', num_conn);
        });
      });
    });

    // Listen to messages sent from the master. Ignore everything else.
    process.on('message', function(message, connection) {
        if (message !== 'sticky-session:connection') {
            return;
        }

        // Emulate a connection event on the server by emitting the
        // event with the connection the master sent us.
        server.emit('connection', connection);

        connection.resume();
    });
} 