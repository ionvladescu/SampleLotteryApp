App.factory('api', ['$resource', function($resource) {

    return $resource(GLB.apiAddress + ":ctrl" + "/:action", {ctrl: '@ctrl', action: '@action'}, {
        signUp: {method: 'POST', isArray: false, params: {ctrl: 'signup'}},
        register: {method: 'POST', isArray: false, params: {ctrl: 'signup'}},
        login: {method: 'POST', isArray: false, params: {ctrl: 'login'}},
        forgotPassword: {method: 'POST', isArray: false, params: {ctrl: 'login', action: 'forgot'}},
        changePassword: {method: 'POST', isArray: false, params: {ctrl: 'login', action: 'reset'}},
        profile: {method: 'POST', isArray: false, params: {ctrl: 'profile'}},
        lottery: {method: 'GET', isArray: false, params: {ctrl: 'lottery'}},
        lotteryJoin: {method: 'POST', isArray: false, params: {ctrl: 'lottery', action: 'join'}},

    });

}]);

App.factory('socket', ['$rootScope', function($rootScope) {
    var self = this;

    var socket = io.connect(GLB.socketAdress);
    self.socket = socket;

    socket.on('disconnect', function() { console.log("%cSocket: DISCONNECTED!", "color:#AE5D00;font-weight:bold"); });
    socket.on('connect_failed', function() { console.log("%cSocket: FAILED TO CONNECT!", "color:#AE5D00;font-weight:bold"); });
    socket.on('connect', function() {

        console.log("%cSocket: CONNECTED!", "color:#AE5D00;font-weight:bold");
        self.socket.emit('join', {user_id: GLB.uId});

    });

    return self;
}]);