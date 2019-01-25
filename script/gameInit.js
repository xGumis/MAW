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
            console.log(res);
            handleGame(res);
        }
    });
}
function handleGame(json){
    json = JSON.parse(json);
    window.state = json['state'];
    if(window.state !== 'Full'){
        window.id = json['id'];
        if(window.state === 'Waiting') {$('body').html('<p>Oczekiwanie na gracza...<\/p>');window.start=false;}
        else if(window.start!==true){startGame();}
        else{
            if(json.hasOwnProperty('init') && json['init']===false) {window.state = 'Waiting';window.start = false;}
            if(json.hasOwnProperty('data')){
                let data = json['data'];
                let count = Object.keys(data).length;
                if(count>0){
                    if(data.hasOwnProperty('turn')) {window.turn = data['turn'];delete data.turn;}
                    if(data.hasOwnProperty('phase')) {window.phase = data['phase'];delete data.phase;}
                    if(data.hasOwnProperty('left')) {window.left = data['left'];delete data.left;}
                    jQuery.each(data,function(field,value){
                        if(value==(-1)) $('#'+field).children().remove();
                        else if(value==1) $('#'+field).append('<div class=\"bullet player1\"></div>');
                        else if(value==2) $('#'+field).append('<div class=\"bullet player2\"></div>');
                    });
                }
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
        data: {'function': 'init'},
        success: function(res){
            console.log(res);
        }
    });
    genDivs();
    if(window.id===1)$('.bullet').addClass('player1');
    else if(window.id===2)$('.bullet').addClass('player2');
    clickEvents();
}
