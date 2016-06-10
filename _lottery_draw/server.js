require('dotenv').config({path: '../.env'});
var _       = require('lodash'),
    Q       = require('q'),
    express = require('express'),
    expr    = express(),
    fs      = require('fs'),
    moment  = require('moment'),
    request = require('request'),
    server  = expr.listen(process.env.NAPP_SOCKETPORT, process.env.NAPP_SOCKETIP),
    io      = require('socket.io')(server, {log: false, serveClient: true});


require('./lib');
require('./config/dev');

var dbs = require('./config/db-connect_dev');

var m = {
    ltr: require('./models/')(dbs.ltr),
};

var UsersLotteries = m.ltr.UsersLotteries;
var Lottery = m.ltr.Lottery;
var Lotteries = m.ltr.Lotteries;

var userSockets = [];

var UserSocket = function(socket, user_id) {

    this.socket = socket;
    this.user_id = user_id;

};

function drawLotteryWinner(lottery_id) {
    return UsersLotteries.forge().query(function(q) { q.whereRaw('lottery_id = ?', [lottery_id]) }).fetch().then(function(res) {
        if(res) {
            var usrLtrs = res.toJSON();
            if(usrLtrs.length > 0) {
                var winIdx = _.random(0, usrLtrs.length - 1);
                var usrLtrWin = usrLtrs[winIdx];
                Lottery.forge({id: usrLtrWin.lottery_id}).fetch().then(function(ltr) {
                    if(ltr) {
                        ltr.save({winner_id: usrLtrWin.user_id});
                    }
                });
                return Q(usrLtrWin);
            }
            return Q(null);
        }
        return Q(null);
    });
}

function broadcastResults(usrLtrWin) {
    _.forEach(userSockets, function(sock) {
        sock.socket.emit('winner', {lottery_id: usrLtrWin.lottery_id, user_id: usrLtrWin.user_id, ticket_num: usrLtrWin.ticket_num});
    });
}

var triggered = [];

setInterval(function() {
    Lotteries.forge().query(function(q) { q.whereRaw('is_active = 1 AND draw_at >= NOW()') }).fetch().then(function(res) {
        if(res) {
            var ltrs = res.toJSON();
            _.forEach(ltrs, function(ltr) {
                var dtDraw = moment(ltr.draw_at).format('YYYY-MM-DD HH:mm:ss');
                var dtNow =  moment.utc().format('YYYY-MM-DD HH:mm:ss');

                var diff = moment(dtDraw).diff(dtNow);

                if(diff < 10000 && diff > 0) {
                    console.log(ltr.id, 'will trigger in', diff / 1000);
                }
                if(diff <= 1000 && !triggered.has(ltr.id)) {
                    console.log(diff);
                    console.log('DRAW!!!!');
                    drawLotteryWinner(ltr.id).then(function(usrLtrWin) {
                        console.log(usrLtrWin);
                        if(usrLtrWin) {
                            broadcastResults(usrLtrWin);

                            request.post(process.env.APP_APIPATH + "lottery/notify", {
                                //headers: {'Authorization': 'Bearer ' + NH_ACCESS_TOKEN.token.access_token},
                                body: {data: {lottery_id: usrLtrWin.lottery_id, user_id: usrLtrWin.user_id, ticket_num: usrLtrWin.ticket_num}},
                                json: true
                            }, function(err, response, data) {
                                if(data.error) {
                                    console.error("api notify error");
                                    console.error(data);
                                } else{
                                    console.log(data);
                                }

                            });
                        }
                    });
                    triggered.push(ltr.id);
                }
            });
        }
        return Q(1)

    }).then(function() {
    }).catch(function(err) { console.error(err) });

}, 1000);

setInterval(function() { // keep checking for disconnected sockets
    userSockets.each(function(sock) {
        if(sock.socket.disconnected) {
            userSockets = userSockets.without(sock);
        }
    });
}, 500);

io.on('connect', function(socket) {
    console.log('new connection', socket.handshake.address);

    socket.on('join', function(data) {
        console.log(data);
        if(!data.user_id) return;
        var userSock = new UserSocket(socket, data.user_id);
        userSockets.push(userSock);
    });

});


