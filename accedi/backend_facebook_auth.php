<?php
require_once 'vendor/autoload.php'; // Assicurati di includere le librerie Facebook

$fb = new \Facebook\Facebook([
  'app_id' => '827661742457088',
  'app_secret' => '23c4932d756a47a7c30141b20bcb4df7',
  'default_graph_version' => 'v14.0',
]);

$accessToken = $_POST['accessToken'];

try {
  $response = $fb->get('/me?fields=id,name,email', $accessToken);
  $user = $response->getGraphUser();
  echo 'Name: ' . $user['name'];
  echo 'Email: ' . $user['email'];
} catch(\Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(\Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
?>