<?php
$loggedIn = Auth::check();
if($loggedIn) {
    $user = Auth::user();
}

?>
<div class="md-toolbar-tools" ng-controller="SignUpCtrl">
    <h2>
        Lottery
    </h2>
    <span flex></span>
    <?php if($loggedIn): ?>
        <?= $user->email ?>
        <md-button class="md-raised md-accent md-hue-2" ng-click="logout()">LOGOUT</md-button>
    <?php else: ?>
        <md-button class="md-raised md-accent md-hue-2" ng-click="showLogin(evt)">LOGIN</md-button>
        <md-button class="md-raised md-accent md-hue-2" ng-click="showSignup(evt)">SIGN UP!</md-button>
    <?php endif ?>
</div>
