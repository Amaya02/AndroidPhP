<?php
    require_once 'transaction.php';

    $userid = "";
    $transacid = "";
    $status = "";
    $date = "";
    $start = "";

    if(isset($_POST['userid'])){
        
        $userid = $_POST['userid'];
        
    }

    if(isset($_POST['transacid'])){
        
        $transacid = $_POST['transacid'];
        
    }

    if(isset($_POST['status'])){
        
        $status = $_POST['status'];
        
    }

    if(isset($_POST['date'])){
        
        $date = $_POST['date'];
        
    }

    if(isset($_POST['start'])){
        
        $start = $_POST['start'];
        
    }

    
    $userObject = new User();

    $json_array = $userObject->addTransaction($userid,$transacid,$status,$date,$start);
        
    echo json_encode($json_array);

?>