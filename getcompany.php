<?php
    require_once 'company.php';

    $companyid = "";

    if(isset($_POST['companyid'])){
        
        $companyid = $_POST['companyid'];
        
    }
    
    $userObject = new User();

    if(!empty($companyid)){
    	$json_array = $userObject->getCompanyTran($companyid);
        
    	echo json_encode($json_array);
    }
    else{
    	$json_array = $userObject->getCompany();
        
    	echo json_encode($json_array);
    }
        
    
    
?>