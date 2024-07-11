<?php 
$username = $_POST['username'];
$password = $_POST['password'];
if($password == 'admin' && $username == 'admin'){
    header("location: ../index_section.php");

}else{
    header("location: ../login.php");
}

?>