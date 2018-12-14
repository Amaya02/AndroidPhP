<?php
    require_once 'transaction.php';

    $userid = "";
    $status = "";

    if(isset($_POST['userid'])){
        
        $userid = $_POST['userid'];
        
    }

    if(isset($_POST['status'])){
        
        $status = $_POST['status'];
        
    }
    
    $userObject = new User();

    $json_array = $userObject->getTransaction($userid,$status);
        
    echo json_encode($json_array);
?>