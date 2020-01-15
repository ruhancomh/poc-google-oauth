<?php

require 'vendor/autoload.php';
session_start();

$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'];

$client = new Google_Client();
$client->setAuthConfig('./client_credentials.json');
$client->addScope('openid email profile');
$client->setRedirectUri($redirect_uri);
$client->setState('xyz');

$client->setHostedDomain('picpay.com');

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['id_token_token']);
}

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    // store in the session also
    $_SESSION['id_token_token'] = $token;
    // redirect back to the example
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    return;
}

if (
    !empty($_SESSION['id_token_token'])
    && isset($_SESSION['id_token_token']['id_token'])
) {
    $client->setAccessToken($_SESSION['id_token_token']);
} else {
    $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
    $token_data = $client->verifyIdToken();
}

?>

<div class="box">
    <?php if (isset($authUrl)): ?>
        <div class="request">
            <a class='login' href='<?= $authUrl ?>'>Connect Me!</a>
        </div>
    <?php else: ?>
        <div class="data">
            <p>Here is the data from your Id Token:</p>
            <pre><?php var_export($token_data) ?></pre>
        </div>
    <?php endif ?>
</div>
