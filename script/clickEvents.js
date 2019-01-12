//field click
$('.clickable').click(function () {
    if(hand>0&&$(this).html()===''){
        $(this).html('<div class=\"bullet\"><\/div>');
        hand--;
        $('#loader').children().last().remove();
    }
    $('.highlight').removeClass('highlight');
    $(this).addClass('highlight');
});