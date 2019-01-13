//field click
function clickEvents(){
    $('.clickable').click(function () {
        if(window.turn===window.id){
            if(window.match > 0) remove(this);
            else if(window.phase==='Placing') place(this);
            else if(window.phase==='Game' && window.selected === -1) select(this);
            else if(window.phase==='Game' && window.selected !== -1) move(this);
        }
    });
}

function place(field){
    let number = parseInt($(field).attr('id'));
    $.ajax({
        url: 'php/ajax.php',
        method: 'POST',
        data: {'function': 'place','id': window.id,'field':number},
        success: function(res) {
            console.log(res);
            res = JSON.parse(res);
            if(res['result']){
                if(window.turn === res['turn']){
                    window.match = res['match'];
                }else window.turn = res['turn'];
                window.phase = res['phase'];
                window.hand--;
                $(field).html('<div class=\"bullet player'+ window.id +'\"><\/div>');
                $('#loader').children().last().remove();
            }
        }
    });

}
function move(field){
    let to = parseInt($(field).attr('id'));
    let from = window.selected;
    $.ajax({
        url: 'php/ajax.php',
        method: 'POST',
        data: {'function': 'move','from': from,'to':to,'id': window.id},
        success: function(res){
            res = JSON.parse(res);
            window.turn = res['turn'];
            if(res['result']===true) {
                $(field).addClass('player'+window.id);
                $('#'+from).removeClass('player'+window.id);
                window.turn = res['turn'];
                window.match = res['match'];
            }
        }
    });

}
function remove(field){
        let number = parseInt($(field).attr('id'));
        $.ajax({
            url: 'php/ajax.php',
            method: 'POST',
            data: {'function': 'remove','field':number,'id':window.id},
            success: function(res){
                res = JSON.parse(res);
                window.turn = res['turn'];
                if(res['result']===true){
                    $(field).removeClass('player'+window.id%2+1);
                    window.match--;
                }
            }
        });
}
function select(field){
    let number = parseInt($(field).attr('id'));
    $.ajax({
        url: 'php/ajax.php',
        method: 'POST',
        data: {'function': 'select','field':number,'id':window.id},
        success: function(res){
            res = JSON.parse(res);
            if(res['result']===true){
                $('.highlight').removeClass('highlight');
                $(this).addClass('highlight');
            }
        }
    });

}
