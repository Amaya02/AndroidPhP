<?php
    require_once 'company.php';

    $search = "";

    if(isset($_POST['search'])){
        
        $search = $_POST['search'];
        
    }
    
    $userObject = new User();

    $json_array = $userObject->searchCompany($search);
        
    echo json_encode($json_array);    
    
?>