<?php
/**
 * Created by PhpStorm.
 * User: Voja
 * Date: 02-Jul-17
 * Time: 19:34
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data["repository"]["id"] == 127429464) {
        echo shell_exec("git pull");
    }
}
//TODO: hmac check for autopull
//TODO: npm and gulp tasks after pull

