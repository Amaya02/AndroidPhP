<?php
    require_once 'transaction.php';

    $starttime = "";
    $endtime = "";
    $estitime = "";
    $transacid = "";

    if(isset($_POST['starttime'])){
        
        $starttime = $_POST['starttime'];
        
    }

    if(isset($_POST['endtime'])){
        
        $endtime = $_POST['endtime'];
        
    }

    if(isset($_POST['estitime'])){
        
        $estitime = $_POST['estitime'];
        
    }

     if(isset($_POST['transacid'])){
        
        $transacid = $_POST['transacid'];
        
    }
    
    $userObject = new User();

    $json_array = $userObject->confirmTransaction($starttime,$endtime,$estitime,$transacid);
    
    echo json_encode($json_array);

?>