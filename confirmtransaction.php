<?php
    require_once 'transaction.php';

    $starttime = "";
    $endtime = "";
    $estitime = "";

    if(isset($_POST['starttime'])){
        
        $starttime = $_POST['starttime'];
        
    }

    if(isset($_POST['endtime'])){
        
        $endtime = $_POST['endtime'];
        
    }

    if(isset($_POST['estitime'])){
        
        $estitime = $_POST['estitime'];
        
    }
    
    $userObject = new User();

    $json_array = $userObject->confirmTransaction($starttime,$endtime,$estitime);
    
    echo json_encode($json_array);

?>