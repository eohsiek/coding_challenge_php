<?php 

namespace CheckWriter;

include 'vendor/autoload.php';

$amount = '106.36';
echo $amount . '<br>';
$CheckWriter = new CheckWriter($amount);
echo $CheckWriter->getDescription();
