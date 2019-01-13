window.onload = loadGame;
window.onbeforeunload = function(){
    $.ajax({
        method: 'POST',
        url: 'php/ajax.php',
        data: {'function': 'disconnect','id': window.id}
    });
};
function loadGame(){
    updatePlayer();
    setTimeout(loadGame,1000);
}
function updatePlayer(){
    $.ajax({
        method: 'POST',
        url: 'php/ajax.php',
        data: {'function': 'update','id': window.id,'turn':window.turn,'start':window.start},
        success: function(res){
            handleGame(res);
        }
    });
}
function handleGame(json){
    console.log(json);
    json = JSON.parse(json);
    window.state = json['state'];
    if(window.state !== 'Full'){
        window.id = json['id'];
        if(window.state === 'Waiting') {$('body').html('<p>Oczekiwanie na gracza...<\/p>');window.start=false;}
        else if(window.start!==true){startGame();}
        else{
            if(json.hasOwnProperty('data')){
                console.log('Before {turn:'+window.turn+',phase:'+window.phase+',left:'+window.left+'}');
                let data = json['data'];
                let count = Object.keys(data).length;
                if(count>0){
                    if(data.hasOwnProperty('turn')) {window.turn = data['turn'];delete data.turn;}
                    if(data.hasOwnProperty('phase')) {window.phase = data['phase'];delete data.phase;}
                    if(data.hasOwnProperty('left')) {window.left = data['left'];delete data.left;}
                    jQuery.each(data,function(field,value){
                        if(value===-1) $('#'+field).removeClass('player1','player2');//todo
                        else if(value===1) $('#'+field).addClass('player1');
                        else if(value===2) $('#'+field).addClass('player2');
                    });
                }
                console.log('After {turn:'+window.turn+',phase:'+window.phase+',left:'+window.left+'}');
            }
        }
    }else {
        $('body').html('<p>Pokój pełny<\/p>');
        window.id = 0;
    }
}
function startGame(){
    window.hand = 9;
    window.left = 9;
    window.turn = 1;
    window.start = true;
    window.selected = -1;
    window.phase = 'Placing';
    if(window.id===1)
    $.ajax({
        method: 'POST',
        url: 'php/ajax.php',
        data: {'function': 'init'}
    });
    genDivs();
    if(window.id===1)$('.bullet').addClass('player1');
    else if(window.id===2)$('.bullet').addClass('player2');
    clickEvents();
}