<?php
    
    include_once 'db-connect.php';
    
    class User{
        
        private $db;
        
        private $db_table = "company";
        private $db_table2 = "transaction";
        
        public function __construct(){
            $this->db = new DbConnect();
        }
        
        public function getCompany(){
            
            $query = "select * from ".$this->db_table;
            
            $i = mysqli_query($this->db->getDb(), $query);

            $num_rows = mysqli_num_rows($i);
            while($row = mysqli_fetch_array($i)){

                $r[]=$row;
                $check=$row['companyid'];
            }

            if($num_rows==0){
                $r[$num_rows]['result']="empty";
                return $r;
            }
            else{
                 $r[0]['result']="success";
                return $r;
            }
            
            mysqli_close($this->db->getDb());
        }

        public function getCompanyTran($companyid){

            $check;
            
            $query = "select * from ".$this->db_table2." where companyid = '$companyid'";
            
            $i = mysqli_query($this->db->getDb(), $query);

            $num_rows = mysqli_num_rows($i);
            while($row = mysqli_fetch_array($i)){

                $r[]=$row;
                $check=$row['transacid'];
            }

            if($num_rows==0){
                $r[$num_rows]['result']="empty";
                return $r;
            }
            else{
                 $r[0]['result']="success";
                return $r;
            }
            
            mysqli_close($this->db->getDb());
        }

        public function searchCompany($search){
            
            $query = "select * from ".$this->db_table." where companyname LIKE '%$search%'";
            
            $i = mysqli_query($this->db->getDb(), $query);

            $num_rows = mysqli_num_rows($i);
            while($row = mysqli_fetch_array($i)){

                $r[]=$row;
                $check=$row['companyid'];
            }

            if($num_rows==0){
                $r[$num_rows]['result']="empty";
                return $r;
            }
            else{
                 $r[0]['result']="success";
                return $r;
            }
            
            mysqli_close($this->db->getDb());
        }
        
    }
    ?>