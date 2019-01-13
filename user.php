<?php
    
    include_once 'db-connect.php';
    
    class User{
        
        private $db;
        
        private $db_table = "users";
        private $db_table2 = "company";
        
        public function __construct(){
            $this->db = new DbConnect();
        }
        
        public function isLoginExist($username, $password){
            
            $query = "select * from ".$this->db_table." where username = '$username' AND password = '$password' Limit 1";
            
            $result = mysqli_query($this->db->getDb(), $query);

            if(mysqli_num_rows($result) > 0){

                while($row = mysqli_fetch_array($result)) {
                    $data['id'] = $row['id'];
                    $data['username'] = $row['username'];
                    $data['fname'] = $row['fname'];
                    $data['lname'] = $row['lname'];
                    $data['num'] = $row['num'];
                    $data['email'] = $row['email'];
                    $data['password'] = $row['password'];

                }
                
                mysqli_close($this->db->getDb());
                
                
                return $data;
                
            }
            
            mysqli_close($this->db->getDb());
            
            return false;
            
        }
        
        public function isEmailExist($email){
            
            $query = "select * from ".$this->db_table." where email = '$email'";
            
            $result = mysqli_query($this->db->getDb(), $query);
            
            if(mysqli_num_rows($result) > 0){
                
                mysqli_close($this->db->getDb());
                
                return true;
                
            }
            
            
            return false;
            
        }

        public function isUsernameExist($username){
            
            $query = "select * from ".$this->db_table." where username = '$username'";
            
            $result = mysqli_query($this->db->getDb(), $query);
            
            if(mysqli_num_rows($result) > 0){
                
                mysqli_close($this->db->getDb());
                
                return true;
                
            }
            
            
            return false;
            
        }
        
        public function isValidEmail($email){
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }
        
        
        
        public function createNewRegisterUser($username, $password, $email, $fname, $lname, $num){
            
            
            $isEmailExisting = $this->isEmailExist($email);
            
            
            if($isEmailExisting){
                
                $json['success'] = 0;
                $json['message'] = "The Email Already Exists";
            }
            else{

                $isUsernameExisting = $this->isUsernameExist($username);

                if($isUsernameExisting){
                    $json['success'] = 0;
                    $json['message'] = "The Username Already Exists";
                }

                else{
                
                    $isValid = $this->isValidEmail($email);
                
                    if($isValid){
                        $query = "insert into ".$this->db_table." (username, fname, lname, num, password, email, created_at, updated_at) values ('$username', '$fname', '$lname', '$num', '$password', '$email', NOW(), NOW())";
                
                        $inserted = mysqli_query($this->db->getDb(), $query);
                
                        if($inserted == 1){
                            
                            $json['success'] = 1;
                            $json['message'] = "Successfully Registered! Login Now";
                    
                        }else{
                    
                        $json['success'] = 0;
                        $json['message'] = "Error in registering. Probably the username/email already exists";
                    
                        }
                
                        mysqli_close($this->db->getDb());
                    }

                    else{
                        $json['success'] = 0;
                        $json['message'] = "Email Address is not valid";
                    }
                }
            }
            
            return $json;
            
        }

        
        public function loginUsers($username, $password){
            
            $json = array();
            
            $canUserLogin = $this->isLoginExist($username, $password);
            
            
            if($canUserLogin != false){
                
                $json['id'] = $canUserLogin['id'];
                $json['username'] = $canUserLogin['username'];
                $json['fname'] = $canUserLogin['fname'];
                $json['lname'] = $canUserLogin['lname'];
                $json['num'] = $canUserLogin['num'];
                $json['email'] = $canUserLogin['email'];
                $json['password'] = $canUserLogin['password'];
                $json['success'] = 1;
                $json['message'] = "Successfully logged in";
                
            }else{
                $json['success'] = 0;
                $json['message'] = "Invalid Username or Password";
            }
            return $json;
        }

        public function updateInfo($userid,$username,$email,$fname,$lname,$num){

            $check = $this->isEmailExist2($userid, $email);
            if($check){
                $row[0]['result'] = 'Email already Exists';
            }
            else{
                $check = $this->isUsernameExist2($userid, $username);
                if($check){
                    $row[0]['result'] = 'Username already Exists';
                }
                else{
                    $this->updateInfo2($userid, $username, $email,$fname,$lname,$num);
                    $row[0]['result'] = 'success';
                }
            }

            return $row;

        }

        public function updatePass($userid,$curpass,$newpass){

            $check = $this->checkPass($userid,$curpass,$newpass);
            if(!$check){
                $row[0]['result'] = 'Password Does not Match';
            }
            else{
                $this->updatePass2($userid,$curpass,$newpass);
                $row[0]['result'] = 'success';
                $row[0]['pass'] =  md5($newpass);
            }

            return $row;
        }

        public function updatePass2($userid,$curpass,$newpass){
            $pass= md5($newpass);
            $query = "update users set password='$pass' , updated_at = NOW() where id = '$userid'";
            $i = mysqli_query($this->db->getDb(), $query);
        }

        public function updateInfo2($userid,$username,$email,$fname,$lname,$num){

            $query = "update users set username='$username', fname='$fname', lname='$lname', num='$num', email = '$email', updated_at = NOW() where id = '$userid'";
            $i = mysqli_query($this->db->getDb(), $query);

        }

        public function isEmailExist2($userid,$email){
            $query = "select * from ".$this->db_table." where email = '$email' and id != '$userid'";
            
            $i = mysqli_query($this->db->getDb(), $query);

            $num_rows = mysqli_num_rows($i);
            
            if($num_rows > 0){  
                return true;
                
            }
            else{
                return false;
            }

            mysqli_close($this->db->getDb());
        }

        public function isUsernameExist2($userid,$username){
            $query = "select * from ".$this->db_table." where username = '$username' and id != '$userid'";
            $i = mysqli_query($this->db->getDb(), $query);
            
            $num_rows = mysqli_num_rows($i);
            
            if($num_rows > 0){  
                return true;
                
            }
            else{
                return false;
            }
            mysqli_close($this->db->getDb());
        }

        public function checkPass($userid,$curpass,$newpass){
            $pass= md5($curpass);
            $query = "select * from ".$this->db_table." where password = '$pass' and id = '$userid'";
            $i = mysqli_query($this->db->getDb(), $query);
            
            $num_rows = mysqli_num_rows($i);
            
            if($num_rows > 0){  
                return true;
                
            }
            else{
                return false;
            }
            mysqli_close($this->db->getDb());
        }

        public function updateToken($id,$token){
            $query = "update users set fcm_regid='$token' , updated_at = NOW() where id = '$id'";
            $i = mysqli_query($this->db->getDb(), $query);
            $row[0]['result'] = 'success';
            return $row;
        }

        public function restartAlarm($id){
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d');
            $status = "Pending";
            $query = "select * from user_transac where userid = '$id' and status = '$status'";
            $i = mysqli_query($this->db->getDb(), $query);
            $num_rows = mysqli_num_rows($i);
            while($row = mysqli_fetch_array($i)){
                if($row['esti_date']>=$date){
                    $r[]=$row;
                }
            }
            if($num_rows==0){
                $r[0]['result']="empty";
                return $r;
            }
            else{
                $r[0]['result']="success";
                $r[0]['date_now'] = date('Y-m-d');
                $r[0]['time_now'] = date('H:i:s');
                return $r;
            }
        }

        public function getAlarm($id,$tN){
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d');
            $time = $tN;
            $status = "Pending";

            $query = "select user_transac.u_tranid, user_transac.transacid, user_transac.date_tran, user_transac.esti_date, user_transac.esti_start, user_transac.status, transaction.transacname, transaction.companyid, company.companyname from user_transac INNER JOIN transaction ON user_transac.transacid=transaction.transacid INNER JOIN company ON transaction.companyid=company.companyid where userid = '$id' and status = '$status' and esti_date = '$date' and esti_start = '$time' LIMIT 1";

            $i = mysqli_query($this->db->getDb(), $query);
            $num_rows = mysqli_num_rows($i);
            
            if($num_rows==0){
                $r[0]['result']="empty";
                return $r;
            }
            else{
                $row = mysqli_fetch_array($i);
                $r[0]['u_tranid']=$row['u_tranid'];
                $r[0]['companyname']=$row['companyname'];
                $r[0]['transacname']=$row['transacname'];
                $r[0]['result']="success";
                $uid = $r[0]['u_tranid'];
                return $r;
            }
        }

        public function getAlarm1($id,$tN){
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d');
            $time = $tN;
            $status = "Pending";
            $time = date('H:i',strtotime($time. ' + 1 hours'));

            $query = "select user_transac.u_tranid, user_transac.transacid, user_transac.date_tran, user_transac.esti_date, user_transac.esti_start, user_transac.status, transaction.transacname, transaction.companyid, company.companyname from user_transac INNER JOIN transaction ON user_transac.transacid=transaction.transacid INNER JOIN company ON transaction.companyid=company.companyid where userid = '$id' and status = '$status' and esti_date = '$date' and esti_start = '$time' LIMIT 1";

            $i = mysqli_query($this->db->getDb(), $query);
            $num_rows = mysqli_num_rows($i);
            
            if($num_rows==0){
                $r[0]['result']="empty";
                return $r;
            }
            else{
                $row = mysqli_fetch_array($i);
                $r[0]['u_tranid']=$row['u_tranid'];
                $r[0]['companyname']=$row['companyname'];
                $r[0]['transacname']=$row['transacname'];
                $r[0]['result']="success";
                $uid = $r[0]['u_tranid'];
                return $r;
            }
        }

        public function getAlarm2($id,$tN){
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d');
            $time = $tN;
            $status = "Pending";
            $time = date('H:i',strtotime($time. ' + 2 minutes'));

            $query = "select user_transac.u_tranid, user_transac.transacid, user_transac.date_tran, user_transac.esti_date, user_transac.esti_start, user_transac.status, transaction.transacname, transaction.companyid, company.companyname from user_transac INNER JOIN transaction ON user_transac.transacid=transaction.transacid INNER JOIN company ON transaction.companyid=company.companyid where userid = '$id' and status = '$status' and esti_date = '$date' and esti_start = '$time' LIMIT 1";

            $i = mysqli_query($this->db->getDb(), $query);
            $num_rows = mysqli_num_rows($i);
            
            if($num_rows==0){
                $r[0]['result']="empty";
                return $r;
            }
            else{
                $row = mysqli_fetch_array($i);
                $r[0]['u_tranid']=$row['u_tranid'];
                $r[0]['companyname']=$row['companyname'];
                $r[0]['transacname']=$row['transacname'];
                $r[0]['result']="success";
                $uid = $r[0]['u_tranid'];
                return $r;
            }
        }
    }
    ?>