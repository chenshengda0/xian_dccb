<?php
session_start();
if(isset($_GET['u'])){
    unset($_SESSION['userid']);
    unset($_SESSION['savetime']);
    unset($_SESSION['bmb']);
    unset($_SESSION["yzmm"]);
    unset($_SESSION["yzmbm"]);
    
}


  echo "<script>location.href='login.php';</script>";

?>

