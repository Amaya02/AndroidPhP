<?php
    
    require_once 'user.php';

    $id = "";

    if(isset($_POST['id'])){
        
        $id = $_POST['id'];
        
    }
    
    $userObject = new User();
    
    // Update Info
        
    $json_array = $userObject->restartAlarm($id);
        
    echo json_encode($json_array);
            
?>