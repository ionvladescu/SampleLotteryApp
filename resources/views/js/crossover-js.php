<?php
$user = null;
if(Auth::check()) {
    $user = Auth::user();
}

?>
var GLB = {
    homeUrl: "<?=URL::to("/")?>",
    apiAddress: "<?=env('APP_APIPATH')?>",
    socketAdress: "<?=env('APP_SOCKET')?>",
    curYear: "<?=date("Y")?>",
    loginRedir: '',
    uId: <?= ($user ? $user->id : 'null')?>,
};
