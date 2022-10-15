<?php
require_once('../initialize.php');

$hash = '123456';


$password = password_hash($hash, PASSWORD_DEFAULT);

 echo $password;



?>