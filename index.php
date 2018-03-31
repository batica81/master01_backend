<?php 
header('Content-Type: application/json');


$data = json_decode(file_get_contents('php://input'), true);

$username = (empty($data["Username"])? "default_username" : $data["Username"]);
$password = (empty($data["Password"])? "default_password" : $data["Password"]);

$clientcerthash = $_SERVER['X-SSL-CLIENT-CERT-SHA1'];


$jsondata = array (
  0 => 
  array (
    'id' => '3079',
    'username' => $username,
    'password' => $password,
    'name' => 'Vojislav Ristivojevic',
    'email' => 'batica@gmail.com',
    'client_cert_hash' => $clientcerthash,
    'bank_info' => 
    array (
      'Broj kreditne kartice' => '3787 3449 3671 5000',
      'Broj racuna' => '551-1545661-25',
    ),
    'phone' => '38163555333',
    'website' => 'tor64.duckdns.org',
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


//print_r($pemdata);


// Get a certificate resource from the PEM string.
  //  $cert = openssl_x509_read( $pemdata );

    // Parse the resource and print out the contents.
  //  $cert_data = openssl_x509_parse( $cert );
    // array_walk( $cert_data, 'print_element' );

  // fastcgi_param X-SSL-CLIENT-CERT $ssl_client_escaped_cert;
  // fastcgi_param X-SSL-CLIENT-CERT $ssl_client_cert;

//    print_r($cert);
  //  print_r($cert_data);
    //print_r($pemdata);

    // var_dump(openssl_x509_parse($pemdata));

// print_r($_SERVER);

//hook test 3
