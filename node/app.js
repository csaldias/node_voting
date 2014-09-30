//votaciones_ceeinf: Votaciones en tiempo real.

var express = require('express'),
  app = express(),
    server = require('http').createServer(app),
    io = require('socket.io').listen(server);

server.listen(8080);

app.use(express.static(__dirname + '/html'));

var metadata = {}
var viewers = 0;
var status_msg = '';
var votes = {}

metadata['num_students'] = 2135;
metadata['quorum'] = 30;

function resetCounts() {
  votes['paro'] = 0;
  votes['clases'] = 0;
  votes['nulo'] = 0;
  votes['blanco'] = 0;
  status_msg = '';
}

resetCounts();

io.sockets.on('connection', function (socket) {
  socket.on('update_votes', function (choice, value) {
    votes[choice] = parseInt(value);
    socket.broadcast.emit('update_counts', votes);
  });

  socket.on('reset', function() {
    resetCounts();
    io.sockets.emit('update_counts', votes);
    io.sockets.emit('update_status', status_msg);
  });
  
  socket.on('update_status', function (msg) {
    status_msg = msg;
    socket.broadcast.emit('update_status', status_msg);
  });

  socket.on('addstream', function(){ 
    viewers += 1;
    io.sockets.emit('update_viewers', viewers);
    socket.emit('update_metadata', metadata);
    socket.emit('update_counts', votes);
    socket.emit('update_status', status_msg);
  });

  socket.on('disconnect', function (data){
    viewers -= 1;
    socket.broadcast.emit('update_viewers', viewers);
  });
});