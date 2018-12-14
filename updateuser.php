<?php
    
    require_once 'user.php';

    $userid = "";
    
    $username = "";

    $email = "";

    $curpass = "";
    
    $newpass = "";

    if(isset($_POST['userid'])){
        
        $userid = $_POST['userid'];
        
    }
    
    
    if(isset($_POST['username'])){
        
        $username = $_POST['username'];
        
    }

    if(isset($_POST['email'])){
        
        $email = $_POST['email'];
        
    }

    if(isset($_POST['curpass'])){
        
        $curpass = $_POST['curpass'];
        
    }
    
    if(isset($_POST['newpass'])){
        
        $newpass = $_POST['newpass'];
        
    }
    
    
    $userObject = new User();
    
    // Update Info
    
    if(!empty($username) && !empty($email)){
        
        $json_array = $userObject->updateInfo($userid,$username,$email);
        
        echo json_encode($json_array);
        
    }

    if(!empty($curpass) && !empty($newpass)){
        
        $json_array = $userObject->updatePass($userid,$curpass,$newpass);
        
        echo json_encode($json_array);
        
    }
    
    
?>