var App = angular.module('App', ['ngSanitize', 'ngResource', 'ngCookies', 'ngRoute', 'ngAnimate', 'ngMaterial'])
    .config(['$routeProvider','$mdThemingProvider', function($routeProvider, $mdThemingProvider) {
         $routeProvider.when('/', {templateUrl: './partial/header/home'});
         $routeProvider.when('/activate/:code', {templateUrl: './partial/header/register'});

         $mdThemingProvider.theme('default').primaryPalette('brown').accentPalette('deep-orange');

    }]);

