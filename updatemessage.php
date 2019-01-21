<?php
    
    require_once 'user.php';

    $id = "";
    
    $message = "";

    if(isset($_POST['id'])){
        
        $id = $_POST['id'];
        
    }
    
    if(isset($_POST['message'])){
        
        $message = $_POST['message'];
        
    }

    $userObject = new User();
    
    // Update Info
        
    $json_array = $userObject->updateMes($id,$message);
        
    echo json_encode($json_array);
            
?>