// Initialize cluster params
var cluster  = require('cluster'),
_portSocket  = 8080,
_portRedis   = 6379,
_HostRedis   = 'localhost';

//If this is the master process...
if (cluster.isMaster) {
	//We create the server instance, but we don't listen to http requests (yet).
	//The master process only creates the server instance, the workers listen to HTTP requests.
	var server 		= require('http').createServer();
	var	socketIO	= require('socket.io').listen(server);
	var	redis 		= require('socket.io-redis');

	socketIO.adapter(redis({ host: _HostRedis, port: _portRedis }));

	//Let's create workers for each available core
	var numbrerCores = require('os').cpus().length;
	for (var i = 0; i < numbrerCores; i++) {
		cluster.fork();
	}

	//Debug messages for each possible case
	cluster.on('fork', function(worker) {
        console.log('Worker %s has spawned.', worker.id);
    });
    cluster.on('online', function(worker) {
        console.log('Worker %s is online.', worker.id);
    });
    cluster.on('listening', function(worker, addr) {
        console.log('Worker %s is listening on %s:%d.', worker.id, addr.address, addr.port);
    });
    cluster.on('disconnect', function(worker) {
        console.log('Worker %s is disconnected.', worker.id);
    });
    cluster.on('exit', function(worker, code, signal) {
        console.log('Worker %s has died (%s).', worker.id, signal || code);
        if (!worker.suicide) {
            console.log('Restarting worker...');
            cluster.fork();
        }
    });
}

//If this is the worker process...
if (cluster.isWorker) {
	//We use the code for a single node.
	require("./app.js");
}