var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

var trigger = document.getElementsByClassName("paper-menu-trigger")[0];
var menu = document.getElementsByClassName("paper-context")[0];
var menuItems = document.getElementsByClassName("paper-context-list-item");

trigger.addEventListener("click", function() {
  if (menu.classList.contains("visible")) {
    menu.classList.remove("visible");
    
    setTimeout(function() {
      menu.classList.remove("no-delay");
    }, 0);
  } else {
    menu.classList.add("visible");
    
    setTimeout(function() {
      menu.classList.add("no-delay");
    }, 0);
  }
});

Array.prototype.forEach.call(menuItems, function(menuItem) {
  menuItem.addEventListener("click", function() {
    menu.classList.remove("visible");
    
    setTimeout(function() {
      menu.classList.remove("no-delay");
    }, 0);
  });
});



var trigger2 = document.getElementsByClassName("paper-menu-trigger-two")[0];
var menu2 = document.getElementsByClassName("paper-context-two")[0];
var menuItems2 = document.getElementsByClassName("paper-context-two-list-item");

trigger2.addEventListener("click", function() {
  if (menu2.classList.contains("visible")) {
    menu2.classList.remove("visible");
    
    setTimeout(function() {
      menu2.classList.remove("no-delay");
    }, 0);
  } else {
    menu2.classList.add("visible");
    
    setTimeout(function() {
      menu2.classList.add("no-delay");
    }, 0);
  }
});

Array.prototype.forEach.call(menuItems2, function(menuItem2) {
  menuItem2.addEventListener("click", function() {
    menu2.classList.remove("visible");
    
    setTimeout(function() {
      menu2.classList.remove("no-delay");
    }, 0);
  });
});
