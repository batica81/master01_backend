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
            'Greska' => 'Pogresna lozinka'
        ),
);

$certMismatch = array (
    0 =>
        array (
            'Greska:' => 'Korisnicki sertifikat se ne slaze sa e-mail adresom.',
            'prilozeni' => strtoupper($clientcerthash),
            'u bazi:' => trim($izdatiSertifikat)
        ),
);


if (strtoupper($clientcerthash) != trim($izdatiSertifikat)) {
    echo json_encode($certMismatch);
}

else if (hash_equals($hashed_password, crypt($password, $hashed_password))) {
//    echo json_encode($jsondata);

    define('AES_256_CBC', 'aes-256-cbc');
// sha256 hash od "123456"
//    $encryption_key = hex2bin("8D969EEF6ECAD3C29A3A629280E686CF0C3F5D5A86AFF3CA12020C923ADC6C92");
    $encryption_key = hex2bin( hash('sha256', $password));
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
    $data = json_encode($jsondata);
    $encrypted = openssl_encrypt($data, AES_256_CBC, $encryption_key,0 , $iv);
    $encrypted = $iv. base64_decode($encrypted);
//    $parts = explode(':', $encrypted);
//    $decrypted = openssl_decrypt($parts[0], AES_256_CBC, $encryption_key, 0, $parts[1]);

    echo base64_encode($encrypted);
//    echo $encrypted;

} else {
    echo json_encode($unathorized);
}




