<?php
    
    require_once 'user.php';

    $id = "";
    
    $status = "";

    if(isset($_POST['id'])){
        
        $id = $_POST['id'];
        
    }
    
    if(isset($_POST['status'])){
        
        $status = $_POST['status'];
        
    }

    $userObject = new User();
    
    // Update Info
        
    $json_array = $userObject->updateStat($id,$status);
        
    echo json_encode($json_array);
            
?>