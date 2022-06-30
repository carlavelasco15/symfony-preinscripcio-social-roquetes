
var casilla = document.querySelectorAll('.disponibility');

casilla.forEach(function(element) {
    var nums = element.innerHTML.split(" / ");
    element.style.background = (nums[1] <= nums[0]) ? '#fababa' : ((nums[1]-2) <= nums[0]) ? '#fde3b8fa' : '#b7e1b7';
});

