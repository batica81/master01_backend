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
    "password",
    "status",
    "phone",
    "id"
], [
    "email" => $username
]);

$hashed_password = $datas[0]["password"];
$user_status = $datas[0]["status"];
$user_id = $datas[0]["id"];
$phone = $datas[0]["phone"];

$datas2 = $database->select("Stanje", [
    "promena",
    "stanje"
], [
    "korisnik" => $user_id
]);

$stanje = end($datas2)["stanje"];

$datas3 = $database->select("Sertifikat", [
    "hash"
], [
    "owner" => $user_id
]);
$izdatiSertifikat = $datas3[0]["hash"];


$jsondata = array (
  0 => 
  array (
    'id' => $user_id,
    'username' => $username,
    'date' => $today,
    'name' => 'Vojislav Ristivojevic',
    'email' => $username,
    'client_cert_hash' => $clientcerthash,
    'bank_info' => 
    array (
      'Broj kreditne kartice' => '3787 3449 3671 5000',
      'Broj racuna' => '551-1545661-25',
      'Stanje' => $stanje,
    ),
    'phone' => $phone,
    'website' => 'api.master01.duckdns.org',
    'company' => 
    array (
      'name' => 'Code Red',
      'catchPhrase' => 'Multi-layered client-server neural-net',
      'bs' => 'harness real-time e-markets',
    ),
  ),
);

$unathorized = array (
    0 =>
        array (
            'response' => 'Pogresna lozinka'
        ),
);

$certMismatch = array (
    0 =>
        array (
            'response' => 'Korisnicki sertifikat se ne slaze sa e-mail adresom.'
        ),
);


if ($clientcerthash != $izdatiSertifikat) {
    echo json_encode($certMismatch);
}

else if (hash_equals($hashed_password, crypt($password, $hashed_password))) {
    echo json_encode($jsondata);

} else {
    echo json_encode($unathorized);
}




