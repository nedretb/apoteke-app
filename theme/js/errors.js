


document.addEventListener("DOMContentLoaded", function() {

    const xdebug = document.getElementsByClassName('xdebug-error');
    var time = + new Date();
    var poruka = '<div class="error-message"><b>Greska!</b><br /> Na ovoj stranici se dogodila greška. Molimo prijavite HR Administratoru.<br />Kod greške: ' + time +' </div>'

    if(xdebug.length > 0){

        document.getElementsByClassName('xdebug-error')[0].style.display = 'none';

        let elem = document.querySelector ( 'body' );
        elem.innerHTML = elem.innerHTML + poruka;
    }
});