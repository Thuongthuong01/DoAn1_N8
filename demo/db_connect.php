<?php
$servername ="localhost";
$username="root";
$password="";
$dbname="doan1";

$conn= new mysqli($servername,$username,$password,$dbname);
if($conn){
    $setLang = mysqli_query($conn,"SET NAMES 'utf8'");
}else{
    die("Kết nối thất bại!".mysqli_connect_error());
}
?>