/* 
* (2022) Carla Velasco Fàbrega <carlavelasco15@gmail.com>
*/

START_DATE = document.getElementById("activity_form_start_date").value;
END_DATE = document.getElementById("activity_form_end_date").value;

const diesSetmanaText = ['dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte', 'diumenge'];
WEEKDAY = [];

for (let i = 0; i < 7; i++) {    
    if(document.getElementById("activity_form_weekday_" + i ).checked) {
        WEEKDAY.push(diesSetmanaText[i]);
    } else {
        if (WEEKDAY.indexOf(diesSetmanaText[i]) > -1) { 
            WEEKDAY.splice(index, 1); 
        }
    }
}

if(START_DATE && END_DATE && WEEKDAY) {

    // Formateamos los inputs del formulario
    var start = getDateFromInput(START_DATE);
    var end = getDateFromInput(END_DATE);
    var weekdays = getIndexFromWeekdays(WEEKDAY);
    
    // A partir de los inputs formateados calculamos las sesiones de la actividad
    var sesionesTotales = getSessions(new Date(start), new Date(end), weekdays);
    
    // Creación de la tabla con las sesiones de la actividad
    createCalendar(sesionesTotales, 'tablaAqui');
    
    
} else {
    createCalendar([], 'tablaAqui');
}



document.getElementById("activity_form_start_date").onchange = function(e) {
    START_DATE = document.getElementById("activity_form_start_date").value;

    if(END_DATE && WEEKDAY) {

            // Formateamos los inputs del formulario
            var start = getDateFromInput(START_DATE);
            var end = getDateFromInput(END_DATE);
            var weekdays = getIndexFromWeekdays(WEEKDAY);
    
            // A partir de los inputs formateados calculamos las sesiones de la actividad
            var sesionesTotales = getSessions(new Date(start), new Date(end), weekdays);
    
            // Creación de la tabla con las sesiones de la actividad
            createCalendar(sesionesTotales, 'tablaAqui');
    }
}


document.getElementById("activity_form_end_date").onchange = function(e) {
    END_DATE = document.getElementById("activity_form_end_date").value;
    if(START_DATE && WEEKDAY) {

        // Formateamos los inputs del formulario
        var start = getDateFromInput(START_DATE);
        var end = getDateFromInput(END_DATE);
        var weekdays = getIndexFromWeekdays(WEEKDAY);

        // A partir de los inputs formateados calculamos las sesiones de la actividad
        var sesionesTotales = getSessions(new Date(start), new Date(end), weekdays);

        // Creación de la tabla con las sesiones de la actividad
        createCalendar(sesionesTotales, 'tablaAqui');

    }
}


document.getElementById("activity_form_weekday").onchange = function(e) { 
    const diesSetmanaText = ['dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte', 'diumenge'];
    WEEKDAY = [];

    for (let i = 0; i < 7; i++) {    
        if(document.getElementById("activity_form_weekday_" + i ).checked) {
            WEEKDAY.push(diesSetmanaText[i]);
        } else {
            if (WEEKDAY.indexOf(diesSetmanaText[i]) > -1) { 
                WEEKDAY.splice(index, 1); 
            }
        }
    }

    if(START_DATE && END_DATE) {

        // Formateamos los inputs del formulario
        var start = getDateFromInput(START_DATE);
        var end = getDateFromInput(END_DATE);
        var weekdays = getIndexFromWeekdays(WEEKDAY);

        // A partir de los inputs formateados calculamos las sesiones de la actividad
        var sesionesTotales = getSessions(new Date(start), new Date(end), weekdays);

        // Creación de la tabla con las sesiones de la actividad
        createCalendar(sesionesTotales, 'tablaAqui');
    }
}
