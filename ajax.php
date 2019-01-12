<?php
session_start();
$timeout = 30; //seconds
$function = $_POST['function'];
$log = array();
switch($function) {
    case('update'):
        $state = $_POST['id'];
        $lock = fopen('lock','w');
        if(flock($lock,LOCK_EX)){
            if($state==1){
                file_put_contents('1',null);
                $log['id']=1;
            }elseif ($state==2){
                file_put_contents('2',null);
                $log['id']=2;
            }else{
                if(!file_exists('1')){
                    $log['id']=1;
                    file_put_contents('1',null);
                }elseif (!file_exists('2')){
                    $log['id']=2;
                    file_put_contents('2',null);
                }
            }
            if(file_exists('1')){
                if(time()-filemtime('1')>$timeout){
                    unlink('1');
                }
            }
            if(file_exists('2')){
                if(time()-filemtime('2')>$timeout){
                    unlink('2');
                }
            }
            flock($lock,LOCK_UN);
            fclose($lock);
        }
        break;
    case('disconnect'):
        $state = $_POST['id'];
        if($state == 1) unlink('1');
        elseif ($state == 2) unlink('2');
        break;
}
echo json_encode($log);