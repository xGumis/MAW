<?php
session_start();
function __autoload($class_name) {
    require_once '../classes/' . $class_name . '.php';
}
function changeTurn(){
    if($_SESSION['turn']==1)$_SESSION['turn']=2;
    elseif($_SESSION['turn']==2)$_SESSION['turn']=1;
    $_SESSION['data']['turn'] = $_SESSION['turn'];
}
function changePhase(){
    if($_SESSION['phase']=='Placing'){
        $_SESSION['phase']='Game';
        $_SESSION['data']['phase'] = 'Game';
    }
    elseif ($_SESSION['phase']=='Game'){
        $_SESSION['phase']='End';
        $_SESSION['data']['phase'] = 'End';
    }
}
function lookForChanges(){
    $data = $_SESSION['data'];
    $_SESSION['sender'] = 0;
    $_SESSION['data'] = array();
    return $data;
}
$timeout = 5; //seconds
$function = $_POST['function'];
$log = array();
switch($function) {
    //region 'update'
    case('update'):
        $state = $_POST['id'];
        $turn = $_POST['turn'];
        $lock = fopen('lock','w');
        if(flock($lock,LOCK_EX)){
            //region Player 1
            if($state==1){
                file_put_contents('1',null);
                $log['id']=1;
                if(file_exists('2')){
                    $log['state'] = 'Connected';
                    $log['init'] = $_SESSION['init'];
                    if($_POST['start']==true){
                    if($turn!=0 && $_SESSION['sender']==2) $log['data']=lookForChanges();
                    }
                }else {
                    $log['state'] = 'Waiting';
                    $_SESSION['init'] = false;
                }
                $log['init'] = $_SESSION['init'];
            }//endregion
            //region Player 2
            elseif ($state==2){
                file_put_contents('2',null);
                $log['id']=2;
                if(file_exists('1')){
                    $log['state'] = 'Connected';
                    $log['init'] = $_SESSION['init'];
                    if($_POST['start']==true){
                    if($turn!=0 && $_SESSION['sender']==1) $log['data']=lookForChanges();
                    }
                }else {
                    $log['state'] = 'Waiting';
                    $_SESSION['init'] = false;
                }
                $log['init'] = $_SESSION['init'];
            }//endregion
            //region New Players
            else{
                if(!file_exists('1')){
                    $log['id']=1;
                    $log['state'] = 'Waiting';
                    $_SESSION['init'] = false;
                    $log['init'] = false;
                    file_put_contents('1',null);
                }elseif (!file_exists('2')){
                    $log['id']=2;
                    $log['state'] = 'Waiting';
                    $_SESSION['init'] = false;
                    $log['init'] = false;
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
    //region 'init'
    case('init'):
        if(!$_SESSION['init']){
            $_SESSION['init']=true;
            require_once('../classes/Board.php');
            $_SESSION['board'] = new Board();
            $board = $_SESSION['board'];
            $count = 0;
            foreach($board->fields as $field){
                if($field->get()==0) $count++;
            }
            $log['count'] = $count;
            $_SESSION['sender'] = 0;
            $_SESSION['hand'] = [9,9];
            $_SESSION['left'] = [9,9];
            $_SESSION['turn'] = 1;
            $_SESSION['phase'] = 'Placing';
            $_SESSION['selection'] = -1;
            $_SESSION['data'] = array();
        }
    break;//endregion
    //region 'place'
    case('place'):
        $field = $_POST['field'];
        $id = $_POST['id'];
        $turn = $_SESSION['turn'];
        $hand = $_SESSION['hand'][$id-1];
        $board = $_SESSION['board'];
        $log['turn'] =$turn;
        if($_SESSION['phase']=='Placing'&&$id==$turn && $hand>0 && $board->place($id,$field)){
                $_SESSION['hand'][$id-1]--;
                $_SESSION['data'][$field] = $id;
                $_SESSION['sender'] = $id;
                $match = $board->check($field);
                if($match>0){
                    $log['match']=$match;
                }else changeTurn();
                $log['turn'] = $_SESSION['turn'];
                if($_SESSION['hand']==[0,0]) changePhase();
                $log['phase']=$_SESSION['phase'];
                $log['result'] = true;
        }else $log['result'] = false;
        break;//endregion
    //region 'remove'
    case('remove'):
        $match = $_POST['match'];
        $field = $_POST['field'];
        $board = $_SESSION['board'];
        $opp = $_SESSION['turn']%2;
        $player = $_POST['id'];
        $turn = $_SESSION['turn'];
        if($turn==$player && $match>0 && $board->fields[$field]->get()!=$player && $board->remove($field)){
                $log['result'] = true;
                $_SESSION['data'][$field] = -1;
                $match--;
                $_SESSION['data']['left'] = --$_SESSION['left'][$opp];
                $_SESSION['sender'] = $player;
                if($_SESSION['left'][$opp]<3){
                    changePhase();
                }
                else if($match==0)changeTurn();
                $log['turn'] = $_SESSION['turn'];
        }else $log['result'] = false;
        break;//endregion
    //region 'move'
    case('move'):
        $from = $_POST['from'];
        $to = $_POST['to'];
        $player = $_POST['id'];
        $spec = $_SESSION['left'][$player-1]<4;
        $turn = $_SESSION['turn'];
        $board = $_SESSION['board'];
        if($_SESSION['phase']=='Game'&&$turn==$player && $board->move($from,$to,$spec)){
            $log['result'] = true;
            $_SESSION['data'][$from] = -1;
            $_SESSION['data'][$to] = $player;
            $_SESSION['sender'] = $player;
            $match = $board->check($to);
            if($match>0){
                $log['match']=$match;
            }else changeTurn();
            $log['turn'] = $_SESSION['turn'];
        }else $log['result'] = false;
        break;//endregion
    //region 'select'
    case('select'):
        $field = $_POST['field'];
        $_SESSION['selection'] = $field;
        $board = $_SESSION['board'];
        $player = $_POST['id'];
        $turn = $_SESSION['turn'];
        if($_SESSION['phase']=='Game'&&$turn==$player&&$board->fields[$field]->get()==$player)
            $log['result'] = true;
        else $log['result'] = false;
        break;//endregion
    //region 'debug class'
    case('debug'):
        $board = $_SESSION['board'];
        for($i=0;$i<24;$i++){
            $field = $board->fields[$i];
            $log[$i] = $field->get();
        }
        break;//endregion
}
echo json_encode($log);