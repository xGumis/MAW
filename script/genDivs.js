function genDivs() {
    $('body').html('<div id="board"></div><div id="loader"></div>');
    let divs = '';
    let count = 0;
    for (let i = 0; i < 7; i++)
        for (let j = 0; j < 7; j++) {
            divs += '<div class=\"field';
            if (((i === 3 || j === 3) || (i === j) || (i === 6 - j)) && !(i === 3 && j === 3)) {
                divs += ' clickable\" id=\"' + count + '\"';
                count++;
            } else divs += '\"';
            divs += '><\/div>';
        }
    $('#board').html(divs);
    divs = '';
    for (let i = 0; i < hand; i++) {
        divs += '<div class=\"bullet\" style=\"left:' + (10 + i * 10) + 'px\"><\/div>';
    }
    $('#loader').html(divs);
}