/* Afegir els dies de la setmana al formulari (EDIT) */

var setmana = ['dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte', 'diumenge'];

for(let i = 0; i < setmana.length; i++) {
    var currentDay = document.getElementById("activity_form_weekday_" +  i).value;
    if(setmana[i] == currentDay && horario.includes(setmana[i])) {
        document.getElementById("activity_form_weekday_" +  i).checked = true;
    }
}


/* Afegir les hores al formulari (EDIT) */

var arraySchedule = horario.split(' ');
var startHour = arraySchedule[arraySchedule.length-3].split(':');
var endHour = arraySchedule[arraySchedule.length-1].split(':');
document.getElementById('activity_form_start_hour_hour').value = parseInt(startHour[0]); 
document.getElementById('activity_form_start_hour_minute').value = parseInt(startHour[1]); 
document.getElementById('activity_form_end_hour_hour').value = parseInt(endHour[0]); 
document.getElementById('activity_form_end_hour_minute').value = parseInt(endHour[1]);
