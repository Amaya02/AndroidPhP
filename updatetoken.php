<?php
    
    require_once 'user.php';

    $id = "";
    
    $token = "";

    if(isset($_POST['id'])){
        
        $id = $_POST['id'];
        
    }
    
    if(isset($_POST['token'])){
        
        $token = $_POST['token'];
        
    }

    $userObject = new User();
    
    // Update Info
        
    $json_array = $userObject->updateToken($id,$token);
        
    echo json_encode($json_array);
            
?>