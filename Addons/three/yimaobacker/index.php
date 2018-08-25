<?php
session_start();
if(isset($_GET['u'])){
    unset($_SESSION['houid']);
    unset($_SESSION['houname']);
    unset($_SESSION['bmb']);
    echo "<script>window.location.href='../home.php'</script>";
}

echo "<script>window.location.href='../home.php'</script>"; 
?>