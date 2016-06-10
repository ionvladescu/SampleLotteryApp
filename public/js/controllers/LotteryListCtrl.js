App.controller('LotteryListCtrl', ['$scope', '$mdDialog', '$mdMedia', '$mdColors', '$timeout', '$interval', 'api', 'socket', function LotteryListCtrl($scope, $mdDialog, $mdMedia, $mdColors, $timeout, $interval, api, ss) {

    $scope.lotteries = [];
    $scope.curDraw = [0, 0, 0];
    $scope.server_dt = null;
    $scope.search = null;
    $scope.filter = 'all';
    $scope.filters = [
        {val: 'all', title: 'All'},
        {val: 'mine', title: 'Joined'},
        {val: 'avail', title: 'Available'},
        {val: 'ended', title: 'Ended'},
    ];

    $scope.sortBy = 'draw_at';
    $scope.sortBys = [
        {field: 'id', title: "Id"},
        {field: 'draw_at', title: "Draw Date"},
        {field: '-draw_at', title: "Draw Date DESC"}
    ];

    $scope.drawFilter = function(inp) { // filter for ngrepeat
        switch($scope.filter) {
            case 'all':
                return true;
                break;
            case 'mine':
                return (inp.ticket_num != null);
                break;
            case 'avail':
                var result = moment.utc(inp.draw_at).diff(moment.utc($scope.server_dt));
                var resultInSeconds = moment.duration(result).asSeconds();
                return (resultInSeconds > 0);
                break;
            case 'ended':
                var result = moment.utc(inp.draw_at).diff(moment.utc($scope.server_dt));
                var resultInSeconds = moment.duration(result).asSeconds();
                return (resultInSeconds <= 0);
                break;
        }
        return true;
    };

    var calls = {
        lotteryList: function() { return api.lottery().$promise; },
        lotteryJoin: function(data) { return api.lotteryJoin({data: data}).$promise; },
    };

    calls.lotteryList().then(function(data) {
        $scope.lotteries = data.result.items;
        $scope.server_dt = data.result.server_dt;
        initLotteries();
    });

    ss.socket.on('winner', function(data) {
        $timeout(function() {
            var lot = _.find($scope.lotteries, {id: parseInt(data.lottery_id)});
            lot.ticket_won = String("000" + data.ticket_num).slice(-3);
            lot.draw_enable = true;
            $scope.onCardClick(null, lot, $scope.curDraw);
            $scope.startDraw(lot);
        });
    });

    $scope.startDraw = function(lot) {
        var iA = $interval(function() {
            $scope.curDraw[0] = _.random(0, 9);
        }, 50);
        var iB = $interval(function() {
            $scope.curDraw[1] = _.random(0, 9);
        }, 50);
        var iC = $interval(function() {
            $scope.curDraw[2] = _.random(0, 9);
        }, 50);

        $timeout(function() {
            $interval.cancel(iA);
            $scope.curDraw[0] = lot.ticket_won.substr(0, 1);
        }, 5000)
            .then(function() {
                $timeout(function() {
                    $interval.cancel(iB);
                    $scope.curDraw[1] = lot.ticket_won.substr(1, 1);
                }, 1000)
                    .then(function() {
                        $timeout(function() {
                            $interval.cancel(iC);
                            $scope.curDraw[2] = lot.ticket_won.substr(2, 1);
                        }, 1000)
                            .then(function() {
                                $timeout(function() {
                                    lot.draw_enable = false;
                                }, 3000);
                            });
                    });
            });
    };


    function initLotteries() {

        $scope.setCounter = function(lottery) {

            var result = moment.utc(lottery.draw_at).diff(moment.utc($scope.server_dt));

            var resultInSeconds = moment.duration(result).asSeconds();

            var duration = moment.duration({seconds: resultInSeconds});

            var intv = $interval(function() {
                var seconds = duration.asSeconds();
                duration = duration.subtract(moment.duration(1, 's'));
                lottery.counter = moment.duration(duration).format("h:mm:ss", {trim: false});

                if(seconds <= 1) {
                    $interval.cancel(intv);
                }
            }, 1000);

        };

        $scope.lotteries.forEach(function(lottery) {
            lottery.expiry = '';
            var current = moment.utc($scope.server_dt);
            var expiry = moment.utc(lottery.draw_at);
            var currentPlusOneDay = moment.utc(current).add(24, 'hours');

            if(moment(expiry).isBetween(current, currentPlusOneDay)) {
                $scope.setCounter(lottery);
            } else {
                if(moment(expiry).isBefore(current)) {
                    lottery.expiry = "ended";
                } else if(moment(expiry).isAfter(current)) {
                    lottery.expiry = expiry.from(current);
                }
            }
        });

        $scope.getJoinedAt = function(lottery) {
            return moment.utc(lottery.joined_at).format('LL');
        };

    }

    $scope.onCardClick = function(ev, lottery, curDraw) {

        $mdDialog.show({
            controller: 'LotteryItemDlgCtrl',
            locals: {lottery: lottery, curDraw: $scope.curDraw, calls: calls},
            templateUrl: 'templates/lotterydialog.tpl.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: true
        });

    };

}]);
