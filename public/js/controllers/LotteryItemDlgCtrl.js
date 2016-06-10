App.controller('LotteryItemDlgCtrl', ['$scope', '$timeout', '$interval', '$mdDialog', '$mdToast', 'lottery', 'curDraw', 'calls', function LotteryItemDlgCtrl($scope, $timeout, $interval, $mdDialog, $mdToast, lottery, curDraw, calls) {

    $scope.curDraw = curDraw;
    $scope.lottery = lottery;

    $scope.closeDialog = function() {
        $mdDialog.cancel();
    };

    $scope.join = function() {
        calls.lotteryJoin({id: $scope.lottery.id}).then(function(data) {
            if(data.error) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent(data.error.message)
                        .parent($('#lottery-dialog'))
                        .position('bottom right')
                        .hideDelay(2000)
                ).then(function() {
                    if(data.error.redir == 'login') {
                        $scope.$emit('show-login');
                    }
                });
            } else {
                $scope.lottery.participates = true;
                $scope.lottery.joined_at = data.result.joined_at;
                $scope.lottery.ticket_num = data.result.ticket_num;

                $mdToast.show(
                    $mdToast.simple()
                        .textContent('Thanks for joining this lottery! Good luck!')
                        .parent($('#lottery-dialog'))
                        .position('bottom right')
                        .hideDelay(2000)
                ).then(function() {

                    $mdDialog.hide();

                });
            }
        });
    };

}]);





