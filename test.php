<?php 

namespace CheckWriter;

include 'vendor/autoload.php';

$amount = '100100400.36';
echo $amount . '<br>';
$CheckWriter = new CheckWriter($amount);
echo $CheckWriter->getDescription();
