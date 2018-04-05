<?php
/**
 * Created by PhpStorm.
 * User: Voja
 * Date: 01-Apr-18
 * Time: 14:24
 **/

require 'connectvars.php';
require '../vendor/autoload.php';

use Medoo\Medoo;
use Twilio\Rest\Client;
use chillerlan\QRCode\QRCode;

if (!isset($_SERVER['PHP_AUTH_PW']) || (($_SERVER['PHP_AUTH_PW'] != PHP_AUTH_PW))) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Master01"');
    exit('<h3>Master01 registracija</h3>Molimo unesite ispravno korisnicko ime i lozinku.');
}

$userId = 0;

$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => DB_NAME,
    'server' => DB_HOST,
    'username' => DB_USER,
    'password' => DB_PASSWORD,
    'charset' => 'utf8'
]);

if (ENV == "dev") {
    $path = '127.0.0.1/master01_backend';
} else if (ENV == "prod"){
    $path = 'master01.duckdns.org';
}

function rand_num_pass($numchar){
    // pravi random string od $numchar cifara
    $pass = "";
    for ($i=0; $i < $numchar; $i++) {
        $pass .= mt_rand(0,9);
    }
    return $pass;
}

function sendSMS($pin) {
    $sid = TWILLIO_SID;
    $token = TWILLIO_AUTH_TOKEN;
    try {
        $client = new Client($sid, $token);
    } catch (\Twilio\Exceptions\ConfigurationException $e) {
        echo $e->getMessage();
    }

    // Use the client to do fun stuff like send text messages!
    $client->messages->create(
    // the number you'd like to send the message to
        REAL_PHONE,
        array(
            // A Twilio phone number you purchased at twilio.com/console
            'from' => TWILLIO_PHONE,
            // the body of the text message you'd like to send
            'body' => 'Vas pin za aplikaciju Master01 je: ' . $pin
        )
    );
}

function sendEMAIL($email, $pin){
    $from = new SendGrid\Email("Master01 Administrator", "admin@master01.duckdns.org");
    $subject = "PIN kod za aplikaciju Master01";
    $to = new SendGrid\Email($email, $email);
    $content = new SendGrid\Content("text/plain", "Vas PIN kod za aplikaciju Master01 je: " . $pin);
    $mail = new SendGrid\Mail($from, $subject, $to, $content);
    $apiKey = SENDGRID_API_KEY;
    $sg = new \SendGrid($apiKey);
    $response = $sg->client->mail()->send()->post($mail);
//        echo $response->statusCode();
//        print_r($response->headers());
//        echo $response->body();
}

if (isset($_POST) && (!empty($_POST['email'])) ) {
    try {
        $insertStatus = $database->insert('Korisnik', [
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'phone' => $_POST['phone']
//        'phone' => REAL_PHONE
        ]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $userId = $database->id();
}

if ($userId != 0) {
    $email = $_POST['email'];
    $pin = rand_num_pass(6);
    chdir('../cert');

    if (ENV_OS == 'windows') {
        $output = shell_exec('autocert.bat '. $email .' '. $pin);
    } else {
        $output = shell_exec('./autocert.sh '. $email .' '. $pin);
        copy('../cert/certs/'.$email.'.p12', '../register/tempcert/'.$email.'.p12');
    }

    $sha1temp = explode( 'Fingerprint=' , $output  )[1];
    $sha1 = str_replace (':', '', $sha1temp);

    $database->insert('Sertifikat', [
        'hash' => $sha1,
        'owner' => $userId,
        //todo DEV ONLY !!!!
        'serial' => $pin
    ]);

    $QRdata = $path .'/tempcert/' . $email . '.p12';


    //sendSMS($pin);
    //sendEMAIL($email, $pin);

    //TODO: dinamicko pripremanje aplikacije
}

?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="css/favicon.ico"/>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
     <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
     <script src="js/bootstrap.min.js"></script>
    <title>Master01 registracija</title>
</head>

<body>
<div class="container">
    <div class="row centered-form">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <div class="panel panel-default">
                <div class="text-center panel-heading">
                    <h3 class="panel-title">Registrujte novi nalog</h3>
                </div>
                <div class="panel-body">
                    <form action="" method="post" role="form">
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control input-sm" placeholder="E-mail">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="password" class="form-control input-sm" placeholder="Lozinka">
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone" id="phone" class="form-control input-sm" placeholder="Broj telefona">
                        </div>
                        <input type="submit" value="Registruj korisnika" class="btn btn-info btn-block">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="text-center <?php if (!(isset($QRdata))) {echo 'hidden';} ?>">
    <h3>Link za skidanje Android aplikacije Master01</h3>
<!--    <a href="https://github.com/batica81/Master01/raw/master/dist/app-debug.apk"><img src="app_link.png" alt=""></a>-->
    <a href="apk/app-debug.apk"><img class="qrcode" src="<?php echo (new QRCode)->render($path .'/apk/app-debug.apk'); ?>" alt=""></a>

    <h3>Link za skidanje sertifikata</h3>
    <a href="tempcert/<?php if (isset($email)) {echo htmlspecialchars($email);} ?>.p12"><img class="qrcode" src="<?php if (isset($QRdata)) {echo (new QRCode)->render($QRdata);} ?>" alt=""></a>
</div>
</body>
