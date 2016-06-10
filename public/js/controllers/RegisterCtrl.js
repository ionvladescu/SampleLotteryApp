App.controller('RegisterCtrl', ['$scope', '$routeParams', '$timeout', '$location', '$mdDialog', '$mdToast', 'api', function RegisterCtrl($scope, $routeParams, $timeout, $location, $mdDialog, $mdToast, api) {

    var calls = {
        register: function(rdata) { return api.register({action: "register", data: rdata}).$promise; },
        registerCheckCode: function() { return api.register({action: "checkcode", data: {activationCode: $routeParams.code}}).$promise; },
        login: function(data) { return api.login({action: 'login', data: data}).$promise; },
    };

    var result = null;

    if($routeParams.code) {
        calls.registerCheckCode().then(function(data) {
            if(data.result) {
                $mdDialog.show({
                    controller: 'RegisterDlgCtrl',
                    locals: {calls: calls, codeResult: data.result},
                    templateUrl: 'templates/registerdialog.tpl.html'
                });

            }

        }).catch(function(error) {
            if(error.status == 422) {
                $mdDialog.show($mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Error')
                    .textContent('Your activation code is invalid, or already activated.')
                    .ok('OK')
                ).then(function() {
                    $timeout(function() {
                        $location.path('/');
                    })
                })

            }
        });
    }

}]);

