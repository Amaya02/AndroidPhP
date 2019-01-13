<?php
    
    require_once 'user.php';

    $id = "";
    $alarm = "";
    $tN = "";

    if(isset($_POST['id'])){
        
        $id = $_POST['id'];
        
    }

    if(isset($_POST['alarm'])){
        
        $alarm = $_POST['alarm'];
        
    }

    if(isset($_POST['tN'])){
        
        $tN = $_POST['tN'];
        
    }
    
    $userObject = new User();
    
    // Update Info

    if($alarm=="0"){

    	$json_array = $userObject->getAlarm($id,$tN);

    }
    else if($alarm=="1"){
    	$json_array = $userObject->getAlarm1($id,$tN);
    }
    else if($alarm=="2"){
    	$json_array = $userObject->getAlarm2($id,$tN);
    }
        
        
    echo json_encode($json_array);
            
?>