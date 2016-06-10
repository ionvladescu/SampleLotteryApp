App.controller('RegisterDlgCtrl', ['$scope', '$routeParams', '$location', '$mdDialog', '$mdToast', 'calls', 'codeResult', function RegisterDlgCtrl($scope, $routeParams, $location, $mdDialog, $mdToast, calls, codeResult) {

    $scope.register = {
        validCode: null,
        submitDisable: false
    };

    $scope.form = {
        password: '',
        passwordB: '',
        firstname: '',
        lastname: '',
    };

    $scope.register.validCode = true;
    $scope.form.firstname = codeResult.firstname;
    $scope.form.lastname = codeResult.lastname;
    $scope.form.email = codeResult.email;
    $scope.form.mobile = codeResult.mobile;

    $scope.setValidity = function(field, cond, fnAdditional) {
        if(cond) {
            $scope.regForm[field].$setValidity('b', false);
        } else {
            $scope.regForm[field].$setValidity('b', true);
        }

        if(fnAdditional) fnAdditional.call(this, cond);

    };

    $scope.checkPassword = function() {
        $scope.regForm.passwordB.$setValidity('b', ($scope.regForm.password.$modelValue == $scope.regForm.passwordB.$modelValue));

    };

    $scope.validateForm = function() {
        $scope.regForm.$setValidity(null, true);

        var reqObj = {
            activationCode: $routeParams.code,
            firstname: $scope.form.firstname,
            lastname: $scope.form.lastname,
            password: $scope.form.password
        };

        return reqObj;
    };

    $scope.sendRegistration = function() { //button
        var reqObj = $scope.validateForm();

        if($scope.regForm.$valid) {
            calls.register(reqObj).then(function(data) {
                if(data.result && data.result.id) {
                    $mdToast.show(
                        $mdToast.simple()
                            .textContent('Registration Complete! Logging in...')
                            .parent($('[name="regForm"]'))
                            .position('bottom right')
                            .hideDelay(2000)
                    ).then(function() {
                        $mdDialog.hide();
                        calls.login({email: data.result.email, password: reqObj.password, remember: true}).then(function() {
                            location.href = '/';
                        });
                    });
                }
            }).catch(function(error) {
                if(error.status == 422) {
                    $mdToast.show(
                        $mdToast.simple()
                            .textContent(error.data.message)
                            .parent($('[name="regForm"]'))
                            .position('bottom right')
                            .hideDelay(2000)
                    ).then(function() {
                        $location.path('/');
                    });
                } else {
                    console.error(error);
                }
            });
        }
    };

}]);

