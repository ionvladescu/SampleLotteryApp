<?php
$loggedIn = Auth::check();
if($loggedIn) {
    $user = Auth::user();
}

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <base href="<?=URL::to("/") . "/"?>">

    <title>Sample Lottery App</title>

    <meta charset="utf-8" />

    <link rel="stylesheet" href="node_modules/angular-material/angular-material.css" type="text/css">
    <link rel="stylesheet" href="css/main.css" type="text/css">

    <script type="text/javascript" src="crossover.js"></script>
    <script type="text/javascript">
        <?php if($loggedIn):?>
        GLB.uId = <?=$user->id?>;
        <?php endif?>
    </script>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

    <script type="text/javascript" src="<?=env('APP_SOCKET')?>/socket.io/socket.io.js"></script>

    <script type="text/javascript" src="node_modules/angular/angular.js"></script>
    <script type="text/javascript" src="node_modules/angular-sanitize/angular-sanitize.js"></script>
    <script type="text/javascript" src="node_modules/angular-cookies/angular-cookies.js"></script>
    <script type="text/javascript" src="node_modules/angular-resource/angular-resource.js"></script>
    <script type="text/javascript" src="node_modules/angular-route/angular-route.js"></script>
    <script type="text/javascript" src="node_modules/angular-animate/angular-animate.js"></script>
    <script type="text/javascript" src="node_modules/angular-aria/angular-aria.js"></script>
    <script type="text/javascript" src="node_modules/angular-touch/angular-touch.js"></script>

    <script type="text/javascript" src="node_modules/angular-material/angular-material.js"></script>

    <script type="text/javascript" src="node_modules/lodash/lodash.min.js"></script>
    <script type="text/javascript" src="node_modules/moment/moment.js"></script>
    <script type="text/javascript" src="node_modules/moment-duration-format/lib/moment-duration-format.js"></script>

    <script type="text/javascript" src="js/app/app.js"></script>
    <script type="text/javascript" src="js/app/app.services.js"></script>
    <script type="text/javascript" src="js/controllers/LotteryListCtrl.js"></script>
    <script type="text/javascript" src="js/controllers/LotteryItemDlgCtrl.js"></script>
    <script type="text/javascript" src="js/controllers/SignUpCtrl.js"></script>
    <script type="text/javascript" src="js/controllers/SignUpDlgCtrl.js"></script>
    <script type="text/javascript" src="js/controllers/LoginDlgCtrl.js"></script>
    <script type="text/javascript" src="js/controllers/RegisterCtrl.js"></script>
    <script type="text/javascript" src="js/controllers/RegisterDlgCtrl.js"></script>

</head>
<body layout="column" ng-app="App" ng-cloak>

<md-toolbar>
    <div ng-view></div>
</md-toolbar>

<md-content layout-align="space-between start" ng-controller="LotteryListCtrl">

    <md-subheader class="ltr-subheader" layout-fill>
        <div layout="row" layout-align="start start" flex>
            <md-input-container>
                <label>Search Lotteries</label>
                <input ng-model="search">
            </md-input-container>
            <span flex></span>
            <md-input-container>
                <label>Show</label>
                <md-select ng-model="filter">
                    <md-option ng-repeat="flt in filters" value="{{flt.val}}">
                        {{flt.title}}
                    </md-option>
                </md-select>
            </md-input-container>
            <md-input-container>
                <label>Order</label>
                <md-select ng-model="sortBy">
                    <md-option ng-repeat="ord in sortBys" value="{{ord.field}}">
                        {{ord.title}}
                    </md-option>
                </md-select>
            </md-input-container>
        </div>
    </md-subheader>

    <section layout-wrap layout="row">
        <div class="ltr-card-container" flex-gt-lg="20" flex-gt-md="25" flex-gt-sm="33" flex-gt-xs="50"
             ng-repeat="lottery in lotteries | orderBy:sortBy | filter:search | filter:drawFilter"
             ng-click="onCardClick($event, lottery, null)">
            <md-card class="ltr-card">
                <img ng-src="{{lottery.image_url}}" class="md-card-image" alt="{{lottery.title}}">

                <md-card-title>
                    <md-card-title-text>
                        <span class="md-headline">{{lottery.title}}</span>
                    </md-card-title-text>
                </md-card-title>

                <md-card-content layout="row" layout-align="top left">
                    {{lottery.description}}
                </md-card-content>

                <md-card-actions layout="column" layout-fill layout-padding>
                    <div md-colors="::{background:'primary-hue-1'}" ng-if="lottery.expiry">Draw {{lottery.expiry}}</div>
                    <div md-colors="::{background:'primary-hue-2'}" ng-if="lottery.counter">Draw: {{lottery.counter}}</div>

                    <div md-colors="::{background:'accent-hue-2'}" ng-if="lottery.ticket_won">Winning ticket: {{lottery.ticket_won}}</div>

                    <div ng-if="lottery.ticket_num" md-colors="::{background:'accent-hue-2'}" layout="row">
                        <span>Your ticket: {{lottery.ticket_num}}</span>
                        <span flex></span>
                        <span class="md-caption">joined @ {{getJoinedAt(lottery)}}</span>
                    </div>

                </md-card-actions>
            </md-card>
        </div>

    </section>

</md-content>
</body>
</html>
