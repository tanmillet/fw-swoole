var wsServer = 'ws://127.0.0.1:9988';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    websocket.send('hello chris 9988');
    // push_live(evt.data)
    console.log("Connected 9988 to WebSocket server.");
};
websocket.onclose = function (evt) {
    console.log("Disconnected");
};
websocket.onmessage = function (evt) {
    console.log('Retrieved data from server live : ' + evt.data);
};
websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};

function push_live(data) {
    console.log(data);
}