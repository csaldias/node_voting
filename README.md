node_voting
===========

Node-voting es una plataforma de conteo de votos en tiempo real escrita en nodejs. Actualmente es utilizada en la UTFSM para proveer a las votaciones estudiantiles de resultados en tiempo real.

El sistema en sí fue creado por mí, mientras que la idea original provino de Alex Arenas (@alexarenasf), que diseñó un sistema similar en PHP pero sin las características de actualización en tiempo real.

El sistema tiene 2 partes: el frontend HTML y la app en nodejs, que corresponden a las carpetas del mismo nombre. Ambas pueden funcionar en el mismo servidor o en servidores independientes (como es en nuestro caso). Asegúrate de modificar las URLs en el index.html del frontend para que apunten al servidor en donde funciona la app en nodejs, o tendrás poblemas. Antes de correr la app, asegúrate de ejecutar ```npm install``` para instalar las dependencias necesarias, además de tener abierto el puerto 8080 en tu firewall.

Siéntete libre de hacer un fork y modificar el código!

Uso
===

Requirements: node y npm instalados

Into node/ run the following commands:
```
npm install
```
Package dependences will be installed.

To run the app:
```
node app.js
```
A socket will be listening on http://localhost:8080
/edit.html
/index.html
