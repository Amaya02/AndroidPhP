<?php
    
    require_once 'user.php';
    
    $username = "";

    $fname = "";

    $lname = "";

    $num = "";
    
    $password = "";
    
    $email = "";
    
    if(isset($_POST['username'])){
        
        $username = $_POST['username'];
        
    }

    if(isset($_POST['fname'])){
        
        $fname = $_POST['fname'];
        
    }

    if(isset($_POST['lname'])){
        
        $lname = $_POST['lname'];
        
    }

    if(isset($_POST['num'])){
        
        $num = $_POST['num'];
        
    }
    
    if(isset($_POST['password'])){
        
        $password = $_POST['password'];
        
    }
    
    if(isset($_POST['email'])){
        
        $email = $_POST['email'];
        
    }
    
    
    
    $userObject = new User();
    
    // Registration
    
    if(!empty($username) && !empty($password) && !empty($email) && !empty($fname) && !empty($lname) && !empty($num)){
        
        $hashed_password = md5($password);
        
        $json_registration = $userObject->createNewRegisterUser($username, $hashed_password, $email, $fname, $lname, $num);
        
        echo json_encode($json_registration);
        
    }
    
    // Login
    
    if(!empty($username) && !empty($password) && empty($email) && empty($fname) && empty($lname) && empty($num)){
        
        $hashed_password = md5($password);
        
        $json_array = $userObject->loginUsers($username, $hashed_password);
        
        echo json_encode($json_array);
    }
    ?>