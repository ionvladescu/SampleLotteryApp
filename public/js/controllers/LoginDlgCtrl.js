App.controller('LoginDlgCtrl', ['$scope', '$mdDialog', '$mdToast', 'calls', function LoginDlgCtrl($scope, $mdDialog, $mdToast, calls) {

    $scope.submitDisable = false;

    $scope.login = {
        email: null,
        password: null,
        remember: true
    };

    $scope.loginSubmit = function() {

        $scope.submitDisable = true;

        calls.login($scope.login)
            .then(function(data) {
                if(data.login) {
                    $mdDialog.hide();
                    location.href = '/';
                }
            })
            .catch(function(error) {
                $scope.submitDisable = false;
                if(error.status == 422) {
                    $mdToast.show(
                        $mdToast.simple()
                            .textContent(error.data.message)
                            .parent($('[name="loginForm"]'))
                            .position('bottom right')
                            .hideDelay(3000)
                    );
                } else {
                    console.error(error);
                }
            });

    };
    $scope.closeDialog = function() {
        $mdDialog.hide();
    }

}]);

