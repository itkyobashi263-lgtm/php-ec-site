<?php
//Cookieの保存期間
define('EXPIRATION_PERIOD', 30);
$cookie_expiration = time() + EXPIRATION_PERIPD * 60 * 24* 365;

//POSTされたフォームの値を変数に格納する
if (isset($_POST['cookie_confirmation']) === TRUE) {
    $cookie_confirmation = $_POST['cookie_confirmation'];
} else {
    $cookie_confirmation = '';
}
if