<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

// $con = mysqli_connect('localhost:3307','root','','tnega_triplicane');
$con = mysqli_connect('localhost','root','','tnega_iim_trichy');

?>