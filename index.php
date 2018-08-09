<?php 
header('Content-Type: application/json');
date_default_timezone_set('Europe/Belgrade');

$data = json_decode(file_get_contents('php://input'), true);

$username = (empty($data["Username"])? "default_username" : $data["Username"]);
$password = (empty($data["Password"])? "default_password" : $data["Password"]);

$clientcerthash = $_SERVER['X-SSL-CLIENT-CERT-SHA1'];
$today = date("Y-m-d H:i:s");

$jsondata = array (
  0 => 
  array (
    'id' => '3079',
    'username' => $username,
    'password' => $password,
    'date' => $today,
    'name' => 'Vojislav Ristivojevic',
    'email' => 'batica@gmail.com',
    'client_cert_hash' => $clientcerthash,
    'bank_info' => 
    array (
      'Broj kreditne kartice' => '3787 3449 3671 5000',
      'Broj racuna' => '551-1545661-25',
      'Stanje' => '32000',
    ),
    'phone' => '38163555333',
    'website' => 'api.master01.duckdns.org',
    'company' => 
    array (
      'name' => 'Code Red',
      'catchPhrase' => 'Multi-layered client-server neural-net',
      'bs' => 'harness real-time e-markets',
    ),
  ),
);


echo json_encode($jsondata);


$pemdata = $_SERVER['X-SSL-CLIENT-CERT'];

$clientcert = openssl_x509_read($pemdata);


