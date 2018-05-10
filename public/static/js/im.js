var wsServer = 'ws://127.0.0.1:9960';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    // websocket.send('hello im 9960');
    // push_live(evt.data)
    console.log("Connected 9960 to WebSocket server.");
};
websocket.onclose = function (evt) {
    console.log("Disconnected");
};
websocket.onmessage = function (evt) {
    push_im(evt.data);
    // console.log('Retrieved data from server live : ' + evt.data);
};
websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};

function push_im(data) {
    var data = JSON.parse(data);
    var im_comment = "<div class=\"comment\"><span style=\"font-size: small;font-weight: bold\">"+data.user+" : </span><span style='font-size: small'>"+data.talk_time+"</span><br/><span>"+data.content+"</span></div>";
    $("#comments").prepend(im_comment);
    // console.log(data);
}