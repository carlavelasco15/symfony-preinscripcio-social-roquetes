
/* 
* (2022) Carla Velasco Fàbrega <carlavelasco15@gmail.com>
*/


/* Pintar les caselles segons la disponibilitat */

var casilla = document.querySelectorAll('.disponibility');

casilla.forEach(function(element) {
    var [num1, num2] = element.innerHTML.split(" / ");
    var num1 = parseInt(num1);
    var num2 = parseInt(num2);
    element.style.background = (num2 <= num1) ? '#fababa' : ((num2-2) <= num1) ? '#fde3b8fa' : '#b7e1b7';
});



