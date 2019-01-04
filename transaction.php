<?php
    
    include_once 'db-connect.php';
    
    class User{
        
        private $db;
        
        private $db_table = "user_transac";
        private $db_table2 = "transaction";
        private $db_table3 = "company";
        
        public function __construct(){
            $this->db = new DbConnect();
        }
        
        public function addTransaction($userid, $transacid,$status,$date,$start){

            $query = "insert into ".$this->db_table."(userid, transacid, status, date_tran, esti_date, esti_start) values ('$userid', '$transacid', '$status', NOW(), '$date', '$start')";
                
            $inserted = mysqli_query($this->db->getDb(), $query);
                
            if($inserted == 1){
                            
                $json[0]['result'] = "success";
                    
            }else{
                    
                $json[0]['result'] = "error";
                    
            }
                
            mysqli_close($this->db->getDb());
            
            return $json;
        }

        public function getTransaction($userid, $status){

            $query = "select user_transac.u_tranid, user_transac.transacid, user_transac.date_tran, user_transac.esti_date, user_transac.esti_start, user_transac.status, transaction.transacname, transaction.companyid, company.companyname from ".$this->db_table." INNER JOIN ".$this->db_table2." ON user_transac.transacid=transaction.transacid INNER JOIN ".$this->db_table3." ON transaction.companyid=company.companyid where user_transac.userid='$userid' AND user_transac.status='$status'";

            $i = mysqli_query($this->db->getDb(), $query);

            $num_rows = mysqli_num_rows($i);
            while($row = mysqli_fetch_array($i)){
                $r[]=$row;
            }

            if($num_rows==0){
                $r[0]['result']="empty";
                return $r;
            }
            else{
                $r[0]['result']="success";
                return $r;
            }

            mysqli_close($this->db->getDb());
        }

        public function confirmTransaction($starttime,$endtime,$estitime,$transacid){
            $r2 = array();
            $status = "Pending";
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d');
            $time = date('H:i');
            $hour = (int)substr($starttime,0,2);
            $min = (int)substr($starttime,3,4);
            $hour1 = (int)substr($endtime,0,2);
            $min1 = (int)substr($endtime,3,4);

            $query = "select * from user_transac where transacid = '$transacid' and status = '$status'";

            $i = mysqli_query($this->db->getDb(), $query);
            $num_rows = mysqli_num_rows($i);
            while($row = mysqli_fetch_array($i)){
                $r[]=$row;
            }

            $r2[0]['date_now'] = date('Y-m-d');
            $r2[0]['time_now'] = date('H:i:s');

            if($num_rows==0){
                if(date('H')<$hour){
                    $date2 = $starttime;
                    $hour2 = (int)substr($date2,0,2);
                    $min2 = (int)substr($date2,3,4);
                    $r2[0]['esti_date'] = date('Y-m-d');
                    $r2[0]['estistart'] = $date2;
                    $x=true;
                    $count=1;
                    while($x){
                        if($hour2>$hour1){
                            $x = false;
                        }
                        else if($hour2==$hour1){
                            if($min2<$min1){
                                $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                $hour2 = (int)substr($date2,0,2);
                                $min2 = (int)substr($date2,3,4);
                                $r2[$count]['estistart'] = $date2;
                                $count++;
                            }
                            else{
                                $x=false;
                            }
                        }
                        else{
                            $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[$count]['estistart'] = $date2;
                            $count++;
                        }
                    }
                }
                else if(date('H')==$hour){
                    if($hour==$hour1){
                        if(date('i')<=$min){
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = date('Y-m-d');
                            $r2[0]['estistart'] = $date2;
                            $x=true;
                            $count=1;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $r2[$count]['estistart'] = $date2;
                                    $count++;
                                }
                            }
                        }
                        else if(date('i')<=$min1){
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = $date;
                            $x=true;
                            $count=0;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        if($hour2>=date('H')){
                                            if($hour2==date('H')){
                                                if($min2>=date('i')){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                            else{
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }     
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    if($hour2>=date('H')){
                                        if($hour2==date('H')){
                                            if($min2>=date('i')){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                        else{
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }  
                                }
                            }
                        }
                        else{
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = date('Y-m-d',strtotime($date. ' + 1 days'));
                            $r2[0]['estistart'] = $date2;
                            $x=true;
                            $count=1;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $r2[$count]['estistart'] = $date2;
                                    $count++;
                                }
                            }
                        }
                    }
                    else{
                        if(date('i')<=$min){
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = date('Y-m-d');
                            $r2[0]['estistart'] = $date2;
                            $x=true;
                            $count=1;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $r2[$count]['estistart'] = $date2;
                                    $count++;
                                }
                            }
                        }
                        else{
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = $date;
                            $x=true;
                            $count=0;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        if($hour2>=date('H')){
                                            if($hour2==date('H')){
                                                if($min2>=date('i')){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                            else{
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }  
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    if($hour2>=date('H')){
                                        if($hour2==date('H')){
                                            if($min2>=date('i')){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                        else{
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }  
                                }
                            }
                        }
                    }
                }
                else{
                    if(date('H')<$hour1){
                        $date2 = $starttime;
                        $hour2 = (int)substr($date2,0,2);
                        $min2 = (int)substr($date2,3,4);
                        $r2[0]['esti_date'] = $date;
                        $x=true;
                        $count=0;
                        while($x){
                            if($hour2>$hour1){
                                $x = false;
                            }
                            else if($hour2==$hour1){
                                if($min2<$min1){
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    if($hour2>=date('H')){
                                        if($hour2==date('H')){
                                            if($min2>=date('i')){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                        else{
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }       
                                }
                                else{
                                    $x=false;
                                }
                            }
                            else{
                                $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                $hour2 = (int)substr($date2,0,2);
                                $min2 = (int)substr($date2,3,4);
                                if($hour2>=date('H')){
                                    if($hour2==date('H')){
                                        if($min2>=date('i')){
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }
                                    else{
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                }  
                            }
                        }
                    }
                    else if(date('H')==$hour1){
                        if(date('i')<=$min1){
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = $date;
                            $x=true;
                            $count=0;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        if($hour2>=date('H')){
                                            if($hour2==date('H')){
                                                if($min2>=date('i')){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                            else{
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }     
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    if($hour2>=date('H')){
                                        if($hour2==date('H')){
                                            if($min2>=date('i')){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                        else{
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }  
                                }
                            }
                        }
                        else{
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = date('Y-m-d',strtotime($date. ' + 1 days'));
                            $r2[0]['estistart'] = $date2;
                            $x=true;
                            $count=1;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $r2[$count]['estistart'] = $date2;
                                    $count++;
                                }
                            }
                        }

                    }
                    else if(date('H')>$hour1){
                        $date2 = $starttime;
                        $hour2 = (int)substr($date2,0,2);
                        $min2 = (int)substr($date2,3,4);
                        $r2[0]['esti_date'] = date('Y-m-d',strtotime($date. ' + 1 days'));
                        $r2[0]['estistart'] = $date2;
                        $x=true;
                        $count=1;
                        while($x){
                            if($hour2>$hour1){
                                $x = false;
                            }
                            else if($hour2==$hour1){
                                if($min2<$min1){
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $r2[$count]['estistart'] = $date2;
                                    $count++;
                                }
                                else{
                                    $x=false;
                                }
                            }
                            else{
                                $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                $hour2 = (int)substr($date2,0,2);
                                $min2 = (int)substr($date2,3,4);
                                $r2[$count]['estistart'] = $date2;
                                $count++;
                            }
                        }
                    }
                }
            }
            else{
                if(date('H')<$hour){
                    $date2 = $starttime;
                    $hour2 = (int)substr($date2,0,2);
                    $min2 = (int)substr($date2,3,4);
                    $r2[0]['esti_date'] = date('Y-m-d');
                    $x=true;
                    $er = 0;
                    for($i =0; $i<count($r);$i++){
                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                            $h = (int)substr($r[$i]['esti_start'],0,2);
                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                            if($hour2 ==$h && $m == $min2){
                                $er++;
                            }
                        }
                    }
                    if($er<=0){
                        $r2[0]['estistart'] = $date2;
                        $count=1;
                    }
                    else{
                        $count=0;
                    }
                    while($x){
                        if($hour2>$hour1){
                            $x = false;
                        }
                        else if($hour2==$hour1){
                            if($min2<$min1){
                                $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                $hour2 = (int)substr($date2,0,2);
                                $min2 = (int)substr($date2,3,4);
                                $er = 0;
                                for($i =0; $i<count($r);$i++){
                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                        if($hour2 ==$h && $m == $min2){
                                            $er++;
                                        }
                                    }
                                }
                                if($er<=0){
                                    $r2[$count]['estistart'] = $date2;
                                    $count++;
                                }
                            }
                            else{
                                $x=false;
                            }
                        }
                        else{
                            $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $er=0;
                            for($i=0; $i<count($r);$i++){
                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                    if($hour2 ==$h && $m == $min2){
                                        $er++;
                                    }
                                    
                                }
                            }
                            if($er<=0){
                                $r2[$count]['estistart'] = $date2;
                                $count++;
                            }
                        }
                    }
                }
                else if(date('H')==$hour){
                    if($hour==$hour1){
                        if(date('i')<=$min){
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = date('Y-m-d');
                            $x=true;
                            $er = 0;
                            for($i =0; $i<count($r);$i++){
                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                    if($hour2 ==$h && $m == $min2){
                                        $er++;
                                    }
                                }
                            }
                            if($er<=0){
                                $r2[0]['estistart'] = $date2;
                                $count=1;
                            }
                            else{
                                $count=0;
                            }
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        $er = 0;
                                        for($i =0; $i<count($r);$i++){
                                            if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                $h = (int)substr($r[$i]['esti_start'],0,2);
                                                $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                if($hour2 ==$h && $m == $min2){
                                                    $er++;
                                                }
                                            }
                                        }
                                        if($er<=0){
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $er=0;
                                    for($i=0; $i<count($r);$i++){
                                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                            $h = (int)substr($r[$i]['esti_start'],0,2);
                                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                                            if($hour2 ==$h && $m == $min2){
                                                $er++;
                                            }
                                            
                                        }
                                    }
                                    if($er<=0){
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                }
                            }
                        }
                        else if(date('i')<=$min1){
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = $date;
                            $x=true;
                            $count=0;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        if($hour2>=date('H')){
                                            if($hour2==date('H')){
                                                if($min2>=date('i')){
                                                    $er=0;
                                                    for($i=0; $i<count($r);$i++){
                                                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                            $h = (int)substr($r[$i]['esti_start'],0,2);
                                                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                            if($hour2 ==$h && $m == $min2){
                                                                $er++;
                                                            }
                                                            
                                                        }
                                                    }
                                                    if($er<=0){
                                                        $r2[$count]['estistart'] = $date2;
                                                        $count++;
                                                    }
                                                }
                                            }
                                            else{
                                                $er=0;
                                                for($i=0; $i<count($r);$i++){
                                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                        if($hour2 ==$h && $m == $min2){
                                                            $er++;
                                                        }
                                                        
                                                    }
                                                }
                                                if($er<=0){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                        }  
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    if($hour2>=date('H')){
                                        if($hour2==date('H')){
                                            if($min2>=date('i')){
                                                $er=0;
                                                for($i=0; $i<count($r);$i++){
                                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                        if($hour2 ==$h && $m == $min2){
                                                            $er++;
                                                        }
                                                        
                                                    }
                                                }
                                                if($er<=0){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                        }
                                        else{
                                            $er=0;
                                            for($i=0; $i<count($r);$i++){
                                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                    if($hour2 ==$h && $m == $min2){
                                                        $er++;
                                                    }
                                                    
                                                }
                                            }
                                            if($er<=0){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                    }  
                                }
                            }
                        }
                        else{
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = date('Y-m-d',strtotime($date. ' + 1 days'));
                            $x=true;
                            $er = 0;
                            for($i =0; $i<count($r);$i++){
                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                    if($hour2 ==$h && $m == $min2){
                                        $er++;
                                    }
                                }
                            }
                            if($er<=0){
                                $r2[0]['estistart'] = $date2;
                                $count=1;
                            }
                            else{
                                $count=0;
                            }
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        $er = 0;
                                        for($i =0; $i<count($r);$i++){
                                            if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                $h = (int)substr($r[$i]['esti_start'],0,2);
                                                $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                if($hour2 ==$h && $m == $min2){
                                                    $er++;
                                                }
                                            }
                                        }
                                        if($er<=0){
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $er=0;
                                    for($i=0; $i<count($r);$i++){
                                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                            $h = (int)substr($r[$i]['esti_start'],0,2);
                                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                                            if($hour2 ==$h && $m == $min2){
                                                $er++;
                                            }
                                            
                                        }
                                    }
                                    if($er<=0){
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                }
                            }
                        }
                    }
                    else{
                        if(date('i')<=$min){
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = date('Y-m-d');
                            $x=true;
                            $er = 0;
                            for($i =0; $i<count($r);$i++){
                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                    if($hour2 ==$h && $m == $min2){
                                        $er++;
                                    }
                                }
                            }
                            if($er<=0){
                                $r2[0]['estistart'] = $date2;
                                $count=1;
                            }
                            else{
                                $count=0;
                            }
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        $er = 0;
                                        for($i =0; $i<count($r);$i++){
                                            if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                $h = (int)substr($r[$i]['esti_start'],0,2);
                                                $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                if($hour2 ==$h && $m == $min2){
                                                    $er++;
                                                }
                                            }
                                        }
                                        if($er<=0){
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $er=0;
                                    for($i=0; $i<count($r);$i++){
                                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                            $h = (int)substr($r[$i]['esti_start'],0,2);
                                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                                            if($hour2 ==$h && $m == $min2){
                                                $er++;
                                            }
                                            
                                        }
                                    }
                                    if($er<=0){
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                }
                            }
                        }
                        else{
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = $date;
                            $x=true;
                            $count=0;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        if($hour2>=date('H')){
                                            if($hour2==date('H')){
                                                if($min2>=date('i')){
                                                    $er=0;
                                                    for($i=0; $i<count($r);$i++){
                                                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                            $h = (int)substr($r[$i]['esti_start'],0,2);
                                                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                            if($hour2 ==$h && $m == $min2){
                                                                $er++;
                                                            }
                                                            
                                                        }
                                                    }
                                                    if($er<=0){
                                                        $r2[$count]['estistart'] = $date2;
                                                        $count++;
                                                    }
                                                }
                                            }
                                            else{
                                                $er=0;
                                                for($i=0; $i<count($r);$i++){
                                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                        if($hour2 ==$h && $m == $min2){
                                                            $er++;
                                                        }
                                                        
                                                    }
                                                }
                                                if($er<=0){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                        }  
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    if($hour2>=date('H')){
                                        if($hour2==date('H')){
                                            if($min2>=date('i')){
                                                $er=0;
                                                for($i=0; $i<count($r);$i++){
                                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                        if($hour2 ==$h && $m == $min2){
                                                            $er++;
                                                        }
                                                        
                                                    }
                                                }
                                                if($er<=0){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                        }
                                        else{
                                            $er=0;
                                            for($i=0; $i<count($r);$i++){
                                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                    if($hour2 ==$h && $m == $min2){
                                                        $er++;
                                                    }
                                                    
                                                }
                                            }
                                            if($er<=0){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                    }  
                                }
                            }
                        }
                    }
                }
                else{
                    if(date('H')<$hour1){
                        $date2 = $starttime;
                        $hour2 = (int)substr($date2,0,2);
                        $min2 = (int)substr($date2,3,4);
                        $r2[0]['esti_date'] = $date;
                        $x=true;
                        $count=0;
                        while($x){
                            if($hour2>$hour1){
                                $x = false;
                            }
                            else if($hour2==$hour1){
                                if($min2<$min1){
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    if($hour2>=date('H')){
                                        if($hour2==date('H')){
                                            if($min2>=date('i')){
                                                $er=0;
                                                for($i=0; $i<count($r);$i++){
                                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                        if($hour2 ==$h && $m == $min2){
                                                            $er++;
                                                        }
                                                        
                                                    }
                                                }
                                                if($er<=0){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                        }
                                        else{
                                            $er=0;
                                            for($i=0; $i<count($r);$i++){
                                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                    if($hour2 ==$h && $m == $min2){
                                                        $er++;
                                                    }
                                                    
                                                }
                                            }
                                            if($er<=0){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                    }  
                                }
                                else{
                                    $x=false;
                                }
                            }
                            else{
                                $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                $hour2 = (int)substr($date2,0,2);
                                $min2 = (int)substr($date2,3,4);
                                if($hour2>=date('H')){
                                    if($hour2==date('H')){
                                        if($min2>=date('i')){
                                            $er=0;
                                            for($i=0; $i<count($r);$i++){
                                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                    if($hour2 ==$h && $m == $min2){
                                                        $er++;
                                                    }
                                                    
                                                }
                                            }
                                            if($er<=0){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                    }
                                    else{
                                        $er=0;
                                        for($i=0; $i<count($r);$i++){
                                            if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                $h = (int)substr($r[$i]['esti_start'],0,2);
                                                $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                if($hour2 ==$h && $m == $min2){
                                                    $er++;
                                                }
                                                
                                            }
                                        }
                                        if($er<=0){
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }
                                }  
                            }
                        }
                    }
                    else if(date('H')==$hour1){
                        if(date('i')<=$min1){
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = $date;
                            $x=true;
                            $count=0;
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        if($hour2>=date('H')){
                                            if($hour2==date('H')){
                                                if($min2>=date('i')){
                                                    $er=0;
                                                    for($i=0; $i<count($r);$i++){
                                                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                            $h = (int)substr($r[$i]['esti_start'],0,2);
                                                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                            if($hour2 ==$h && $m == $min2){
                                                                $er++;
                                                            }
                                                            
                                                        }
                                                    }
                                                    if($er<=0){
                                                        $r2[$count]['estistart'] = $date2;
                                                        $count++;
                                                    }
                                                }
                                            }
                                            else{
                                                $er=0;
                                                for($i=0; $i<count($r);$i++){
                                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                        if($hour2 ==$h && $m == $min2){
                                                            $er++;
                                                        }
                                                        
                                                    }
                                                }
                                                if($er<=0){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                        }  
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    if($hour2>=date('H')){
                                        if($hour2==date('H')){
                                            if($min2>=date('i')){
                                                $er=0;
                                                for($i=0; $i<count($r);$i++){
                                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                        if($hour2 ==$h && $m == $min2){
                                                            $er++;
                                                        }
                                                        
                                                    }
                                                }
                                                if($er<=0){
                                                    $r2[$count]['estistart'] = $date2;
                                                    $count++;
                                                }
                                            }
                                        }
                                        else{
                                            $er=0;
                                            for($i=0; $i<count($r);$i++){
                                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                    if($hour2 ==$h && $m == $min2){
                                                        $er++;
                                                    }
                                                    
                                                }
                                            }
                                            if($er<=0){
                                                $r2[$count]['estistart'] = $date2;
                                                $count++;
                                            }
                                        }
                                    }  
                                }
                            }
                        }
                        else{
                            $date2 = $starttime;
                            $hour2 = (int)substr($date2,0,2);
                            $min2 = (int)substr($date2,3,4);
                            $r2[0]['esti_date'] = date('Y-m-d',strtotime($date. ' + 1 days'));
                            $x=true;
                            $er = 0;
                            for($i =0; $i<count($r);$i++){
                                if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                    $h = (int)substr($r[$i]['esti_start'],0,2);
                                    $m =  (int)substr($r[$i]['esti_start'],3,4);
                                    if($hour2 ==$h && $m == $min2){
                                        $er++;
                                    }
                                }
                            }
                            if($er<=0){
                                $r2[0]['estistart'] = $date2;
                                $count=1;
                            }
                            else{
                                $count=0;
                            }
                            while($x){
                                if($hour2>$hour1){
                                    $x = false;
                                }
                                else if($hour2==$hour1){
                                    if($min2<$min1){
                                        $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                        $hour2 = (int)substr($date2,0,2);
                                        $min2 = (int)substr($date2,3,4);
                                        $er = 0;
                                        for($i =0; $i<count($r);$i++){
                                            if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                                $h = (int)substr($r[$i]['esti_start'],0,2);
                                                $m =  (int)substr($r[$i]['esti_start'],3,4);
                                                if($hour2 ==$h && $m == $min2){
                                                    $er++;
                                                }
                                            }
                                        }
                                        if($er<=0){
                                            $r2[$count]['estistart'] = $date2;
                                            $count++;
                                        }
                                    }
                                    else{
                                        $x=false;
                                    }
                                }
                                else{
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $er=0;
                                    for($i=0; $i<count($r);$i++){
                                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                            $h = (int)substr($r[$i]['esti_start'],0,2);
                                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                                            if($hour2 ==$h && $m == $min2){
                                                $er++;
                                            }
                                            
                                        }
                                    }
                                    if($er<=0){
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                }
                            }
                        }

                    }
                    else if(date('H')>$hour1){
                        $date2 = $starttime;
                        $hour2 = (int)substr($date2,0,2);
                        $min2 = (int)substr($date2,3,4);
                        $r2[0]['esti_date'] = date('Y-m-d',strtotime($date. ' + 1 days'));
                        $x=true;
                        $er = 0;
                        for($i =0; $i<count($r);$i++){
                            if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                $h = (int)substr($r[$i]['esti_start'],0,2);
                                $m =  (int)substr($r[$i]['esti_start'],3,4);
                                if($hour2 ==$h && $m == $min2){
                                    $er++;
                                }
                            }
                        }
                        if($er<=0){
                            $r2[0]['estistart'] = $date2;
                            $count=1;
                        }
                        else{
                            $count=0;
                        }
                        while($x){
                            if($hour2>$hour1){
                                $x = false;
                            }
                            else if($hour2==$hour1){
                                if($min2<$min1){
                                    $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                    $hour2 = (int)substr($date2,0,2);
                                    $min2 = (int)substr($date2,3,4);
                                    $er = 0;
                                    for($i =0; $i<count($r);$i++){
                                        if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                            $h = (int)substr($r[$i]['esti_start'],0,2);
                                            $m =  (int)substr($r[$i]['esti_start'],3,4);
                                            if($hour2 ==$h && $m == $min2){
                                                $er++;
                                            }
                                        }
                                    }
                                    if($er<=0){
                                        $r2[$count]['estistart'] = $date2;
                                        $count++;
                                    }
                                }
                                else{
                                    $x=false;
                                }
                            }
                            else{
                                $date2 = date('H:i:s',strtotime($date2. ' + '.$estitime.' minutes'));
                                $hour2 = (int)substr($date2,0,2);
                                $min2 = (int)substr($date2,3,4);
                                $er=0;
                                for($i=0; $i<count($r);$i++){
                                    if($r2[0]['esti_date'] == $r[$i]['esti_date']){
                                        $h = (int)substr($r[$i]['esti_start'],0,2);
                                        $m =  (int)substr($r[$i]['esti_start'],3,4);
                                        if($hour2 ==$h && $m == $min2){
                                            $er++;
                                        }
                                        
                                    }
                                }
                                if($er<=0){
                                    $r2[$count]['estistart'] = $date2;
                                    $count++;
                                }
                            }
                        }
                    }
                }
            }

            return $r2;

        }

        
    }
?>