<?php
$timeout = 30; //seconds
$function = $_POST['function'];
$log = array();
switch($function) {
    //region 'update'
    case('update'):
        $state = $_POST['id'];
        $lock = fopen('lock','w');
        if(flock($lock,LOCK_EX)){
            //region Player 1
            if($state==1){
                file_put_contents('1',null);
                $log['id']=1;
                if(file_exists('2')){
                    $log['state'] = 'Connected';
                }else $log['state'] = 'Waiting';
            }//endregion
            //region Player 2
            elseif ($state==2){
                file_put_contents('2',null);
                $log['id']=2;
                if(file_exists('1')){
                    $log['state'] = 'Connected';
                }else $log['state'] = 'Waiting';
            }//endregion
            //region New Players
            else{
                if(!file_exists('1')){
                    $log['id']=1;
                    $log['state'] = 'Waiting';
                    file_put_contents('1',null);
                }elseif (!file_exists('2')){
                    $log['id']=2;
                    $log['state'] = 'Waiting';
                    file_put_contents('2',null);
                }else $log['state'] = 'Full';
            }//endregion
            //region Timeout
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
            //endregion
            flock($lock,LOCK_UN);
            fclose($lock);
        }
        break;//endregion
    //region 'disconnect'
    case('disconnect'):
        $state = $_POST['id'];
        if($state == 1) unlink('1');
        elseif ($state == 2) unlink('2');
        break;//endregion

}
echo json_encode($log);