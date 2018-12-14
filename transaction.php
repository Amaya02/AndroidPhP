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
        
        public function addTransaction($userid, $transacid,$status,$date,$start,$end){

            $query = "insert into ".$this->db_table."(userid, transacid, status, date_tran, esti_date, esti_start, esti_end) values ('$userid', '$transacid', '$status', NOW(), '$date', '$start', '$end')";
                
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

            $query = "select user_transac.u_tranid, user_transac.transacid, user_transac.date_tran, user_transac.esti_date, user_transac.esti_start, user_transac.esti_end, user_transac.status, transaction.transacname, transaction.companyid, company.companyname from ".$this->db_table." INNER JOIN ".$this->db_table2." ON user_transac.transacid=transaction.transacid INNER JOIN ".$this->db_table3." ON transaction.companyid=company.companyid where user_transac.userid='$userid' AND user_transac.status='$status'";

            $i = mysqli_query($this->db->getDb(), $query);

            $num_rows = mysqli_num_rows($i);
            while($row = mysqli_fetch_array($i)){

                $r[]=$row;
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

        public function confirmTransaction($starttime,$endtime,$estitime){
            date_default_timezone_set('Asia/Singapore');
            $date = date('Y-m-d');
            $time = date('H:i');
            $hour = (int)substr($starttime,0,2);
            $min = (int)substr($starttime,3,4);
            $hour1 = (int)substr($endtime,0,2);
            $min1 = (int)substr($endtime,3,4);
            $esti=(int)$estitime;
            $query = "select * from user_transac order by u_tranid desc limit 1";

            $i = mysqli_query($this->db->getDb(), $query);
            $num_rows = mysqli_num_rows($i);
            while($row = mysqli_fetch_array($i)){

                $r[]=$row;
            }

            if($num_rows==0){
               if(date('H')<$hour){
                    $row[0]['estistart']=$starttime;
                    $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                    $row[0]['esti_date']=$date;
               }
               else if(date('H')==$hour){
                    if($hour==$hour1){
                        if(date('i')<=$min){
                            $row[0]['estistart']=$starttime;
                            $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                            $row[0]['esti_date']=$date;
                        }
                        else{
                            if(date('i')<=$min1){
                                $row[0]['estistart']=date('H:i');
                                $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                            else{
                                $row[0]['estistart']=$starttime;
                                $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                            }
                        }
                    }
                    else{
                        if(date('i')<=$min){
                            $row[0]['estistart']=$starttime;
                            $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                            $row[0]['esti_date']=$date;
                        }
                        else{
                            $row[0]['estistart']=date('H:i');
                            $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                            $row[0]['esti_date']=$date;
                        }
                    } 
               }
               else{
                    if(date('H')<$hour1){
                        $row[0]['estistart']=date('H:i');
                        $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                        $row[0]['esti_date']=$date;
                    }
                    else if(date('H')==$hour1){
                        if(date('i')<=$min1){
                            $row[0]['estistart']=date('H:i');
                            $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                            $row[0]['esti_date']=$date;
                        }
                        else{
                            $row[0]['estistart']=$starttime;
                            $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                            $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                        }
                    }
                    else{
                        $row[0]['estistart']=$starttime;
                        $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                        $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                    }
               }
            }
            else{
                $hour3 = (int)substr($r[0]['esti_end'],0,2);
                $min3 =  (int)substr($r[0]['esti_end'],3,4);
                if($date==$r[0]['esti_date']){
                    if(date('H')<$hour){
                        $row[0]['estistart']=$r[0]['esti_end'];
                        $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                        $row[0]['esti_date']=$date;
                    }
                    else if(date('H')==$hour){
                        if($hour==$hour1){
                            if(date('i')<=$min){
                                $row[0]['estistart']=$r[0]['esti_end'];
                                $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                            else{
                                if(date('i')<=$min1){
                                    if(date('i')<=$min3){
                                        $row[0]['estistart']=$r[0]['esti_end'];
                                        $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                        $row[0]['esti_date']=$date;
                                    }
                                    else{
                                        $row[0]['estistart']=date('H:i');
                                        $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                        $row[0]['esti_date']=$date;
                                    }
                                }
                                else{
                                    $row[0]['estistart']=$starttime;
                                    $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                                }
                            }
                        }
                        else{
                            if(date('i')<=$min){
                                $row[0]['estistart']=$r[0]['esti_end'];
                                $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                            else{
                                if(date('H')<$hour3){
                                    $row[0]['estistart']=$r[0]['esti_end'];
                                    $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=$date;
                                }
                                else if(date('H')==$hour3){
                                    if(date('i')<=$min3){
                                        $row[0]['estistart']=$r[0]['esti_end'];
                                        $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                        $row[0]['esti_date']=$date;
                                    }
                                    else{
                                        $row[0]['estistart']=date('H:i');
                                        $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                        $row[0]['esti_date']=$date;
                                    }
                                }
                                else{
                                    $row[0]['estistart']=date('H:i');
                                    $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=$date;
                                }
                            }
                        }
                    }
                    else{
                        if(date('H')<$hour1){
                            if(date('H')<$hour3){
                                $row[0]['estistart']=$r[0]['esti_end'];
                                $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                            else if(date('H')==$hour3){
                                if(date('i')<=$min3){
                                    $row[0]['estistart']=$r[0]['esti_end'];
                                    $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=$date;
                                }
                                else{
                                    $row[0]['estistart']=date('H:i');
                                    $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=$date;
                                }
                            }
                            else{
                                $row[0]['estistart']=date('H:i');
                                $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                        }
                        else if(date('H')==$hour1){
                            if(date('i')<=$min1){
                                if(date('H')<$hour3){
                                    $row[0]['estistart']=$r[0]['esti_end'];
                                    $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=$date;
                                }
                                else if(date('H')==$hour3){
                                    if(date('i')<=$min3){
                                        $row[0]['estistart']=$r[0]['esti_end'];
                                        $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                        $row[0]['esti_date']=$date;
                                    }
                                    else{
                                        $row[0]['estistart']=date('H:i');
                                        $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                        $row[0]['esti_date']=$date;
                                    }
                                }
                                else{
                                    $row[0]['estistart']=date('H:i');
                                    $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=$date;
                                }
                            }
                            else{
                                $row[0]['estistart']=$starttime;
                                $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                            }
                        }
                        else{
                            $row[0]['estistart']=$starttime;
                            $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                            $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                        }
                    }
                }
                else{
                    if(date('H')<$hour){
                        $row[0]['estistart']=$starttime;
                        $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                        $row[0]['esti_date']=$date;
                   }
                   else if(date('H')==$hour){
                        if($hour==$hour1){
                            if(date('i')<=$min){
                                $row[0]['estistart']=$starttime;
                                $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                            else{
                                if(date('i')<=$min1){
                                    $row[0]['estistart']=date('H:i');
                                    $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=$date;
                                }
                                else{
                                    $dateX = date('Y-m-d',strtotime($date. ' + 1 days'));
                                    if($dateX == $r[0]['esti_date']){
                                        $row[0]['estistart']=$r[0]['esti_end'];
                                        $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                        $row[0]['esti_date']=$dateX;
                                    }
                                    else{
                                        $row[0]['estistart']=$starttime;
                                        $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                                        $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                                    }
                                }
                            }
                        }
                        else{
                            if(date('i')<=$min){
                                $row[0]['estistart']=$starttime;
                                $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                            else{
                                $row[0]['estistart']=date('H:i');
                                $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                        } 
                   }
                   else{
                        if(date('H')<$hour1){
                            $row[0]['estistart']=date('H:i');
                            $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                            $row[0]['esti_date']=$date;
                        }
                        else if(date('H')==$hour1){
                            if(date('i')<=$min1){
                                $row[0]['estistart']=date('H:i');
                                $row[0]['estiend']=date('H:i',strtotime($time. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$date;
                            }
                            else{
                                $dateX = date('Y-m-d',strtotime($date. ' + 1 days'));
                                if($dateX == $r[0]['esti_date']){
                                    $row[0]['estistart']=$r[0]['esti_end'];
                                    $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=$dateX;
                                }
                                else{
                                    $row[0]['estistart']=$starttime;
                                    $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                                    $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                                }
                            }
                        }
                        else{
                            $dateX = date('Y-m-d',strtotime($date. ' + 1 days'));
                            if($dateX == $r[0]['esti_date']){
                                $row[0]['estistart']=$r[0]['esti_end'];
                                $row[0]['estiend']=date('H:i',strtotime($r[0]['esti_end']. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=$dateX;
                            }
                            else{
                                $row[0]['estistart']=$starttime;
                                $row[0]['estiend']=date('H:i',strtotime($starttime. ' + '.$estitime.' minutes'));
                                $row[0]['esti_date']=date('Y-m-d',strtotime($date. ' + 1 days'));
                            }
                        }
                   }
                }
            }
            return $row;
        }
    }
?>