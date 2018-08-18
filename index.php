<?php 
header('Content-Type: application/json');
date_default_timezone_set('Europe/Belgrade');

require 'register/connectvars.php';
require 'vendor/autoload.php';

use Medoo\Medoo;

$data = json_decode(file_get_contents('php://input'), true);

$username = (empty($data["Username"])? "default_username" : $data["Username"]);
$password = (empty($data["Password"])? "default_password" : $data["Password"]);

$clientcerthash = $_SERVER['X-SSL-CLIENT-CERT-SHA1'];
$today = date("Y-m-d H:i:s");

$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => DB_NAME,
    'server' => DB_HOST,
    'username' => DB_USER,
    'password' => DB_PASSWORD,
    'charset' => 'utf8'
]);

$datas = $database->select("Korisnik", [
    "password"
], [
    "email" => "batica+1434@gmail.com"
]);

echo $datas;

$hashed_password = $datas["password"];

echo $hashed_password;


if (hash_equals($hashed_password, crypt($password, $hashed_password))) {
    echo "Password verified!";
}









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

//$jsondata[0]['password'].pop();

//echo json_encode($jsondata);


$pemdata = $_SERVER['X-SSL-CLIENT-CERT'];

$clientcert = openssl_x509_read($pemdata);


