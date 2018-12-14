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
        
        
        
        public function createNewRegisterUser($username, $password, $email){
            
            
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
                        $query = "insert into ".$this->db_table." (username, password, email, created_at, updated_at) values ('$username', '$password', '$email', NOW(), NOW())";
                
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

        public function updateInfo($userid,$username,$email){

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
                    $this->updateInfo2($userid, $username, $email);
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

        public function updateInfo2($userid,$username,$email){

            $query = "update users set username='$username', email = '$email', updated_at = NOW() where id = '$userid'";
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
    }
    ?>