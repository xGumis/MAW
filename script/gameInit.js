window.onload = loadGame;
window.onbeforeunload = function(){
    $.ajax({
        method: 'POST',
        url: 'ajax.php',
        data: {'function': 'disconnect','id': id}
    });
};
function loadGame(){
    updatePlayer(window.id);
    setTimeout(loadGame,1000);
}
function updatePlayer(id){
    $.ajax({
        method: 'POST',
        url: 'ajax.php',
        data: {'function': 'update','id': id},
        success: function(res){
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
        else if(window.start!==true){startGame();window.start = true;}
        else{
            //Tutaj gra
        }
    }else {
        $('body').html('<p>Pokój pełny<\/p>');
        window.id = 0;
    }
}
function startGame(){
    genDivs();
    clickEvents();
}
