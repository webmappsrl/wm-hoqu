<?php
require 'autoload.php';
header('Content-type: application/json');
$h=hoqu::Instance();
echo $h->getInfo();

