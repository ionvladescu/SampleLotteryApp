App.controller('SignUpCtrl', ['$scope', '$rootScope', '$mdDialog', 'api', function SignUpCtrl($scope, $rootScope, $mdDialog, api) {

    var calls = {
        signUp: function(data) { return api.signUp({action: 'signup', data: data}).$promise; },
        login: function(data) { return api.login({action: 'login', data: data}).$promise; },
        logout: function() { return api.login({action: 'logout'}).$promise; }
    };

    $rootScope.$on('show-login', function() { $scope.showLogin(null) });

    $scope.showSignup = function(evt) {

        $mdDialog.show({
            controller: 'SignUpDlgCtrl',
            locals: {calls: calls},
            templateUrl: 'templates/signupdialog.tpl.html',
            targetEvent: evt,
            clickOutsideToClose: true
        });

    };

    $scope.showLogin = function(evt) {

        $mdDialog.show({
            controller: 'LoginDlgCtrl',
            locals: {calls: calls},
            templateUrl: 'templates/logindialog.tpl.html',
            targetEvent: evt,
            clickOutsideToClose: true
        });

    };

    $scope.logout = function() {

        calls.logout().then(function() {
            location.href = '/';
        });
    };
}]);

