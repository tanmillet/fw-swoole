var wsServer = 'ws://127.0.0.1:9989';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    // websocket.send('hello chris');
    push_chart(evt.data)
    console.log("Connected to WebSocket server.");
};
websocket.onclose = function (evt) {
    console.log("Disconnected");
};
websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
};
websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};

function push_chart(data) {

}