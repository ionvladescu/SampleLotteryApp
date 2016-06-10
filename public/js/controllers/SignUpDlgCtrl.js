App.controller('SignUpDlgCtrl', ['$scope', '$mdDialog', '$mdToast', 'calls', function SignUpDlgCtrl($scope, $mdDialog, $mdToast, calls) {

    $scope.submitDisable = false;

    $scope.showHints = true;

    $scope.signup = {
        email: null,
        mobile: null
    };

    $scope.signUpSubmit = function() {

        $scope.submitDisable = true;

        calls.signUp($scope.signup).then(function(data) {
            $mdToast.show(
                $mdToast.simple()
                    .textContent(data.popup.message)
                    .parent($('[name="userForm"]'))
                    .position('bottom right')
                    .hideDelay(2000)
            ).then(function() { $mdDialog.hide(); });

        }).catch(function(error) {
            $scope.submitDisable = false;
            if(error.status == 422) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent(error.data.message)
                        .parent($('[name="userForm"]'))
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

