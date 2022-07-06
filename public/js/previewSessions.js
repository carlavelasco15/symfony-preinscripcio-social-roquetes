

createCalendar([], 'tablaAqui');


document.getElementById("activity_form_start_date").onchange = function(e) {
    START_DATE = document.getElementById("activity_form_start_date").value;
    if(END_DATE && WEEKDAY) {



    }
}

document.getElementById("activity_form_end_date").onchange = function(e) {
    END_DATE = document.getElementById("activity_form_end_date").value;
    if(START_DATE && WEEKDAY) {


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

function getIndexFromWeekdays(array) {
    var weekdays = { dill: 1, dima: 2, dime: 3, dijo: 4, dive: 5, diss: 6, dium: 0 }; 
    var result = [];

    array.forEach(weekday => {
        result.push(weekdays[weekday.toLowerCase().substr(0,4)]);
    });

    return result;  
}

function getDateFromInput(inputDate) {
    var array = inputDate.split("-");
    return new Date(array[0], array[1]-1, array[2]).toString();
}

function getSessions(startDate, endDate, weekdays) {
    const dates = []
    let currentDate = startDate;

    const addDays = function (days) {
      const date = new Date(this.valueOf())
      date.setDate(date.getDate() + days)
      return date
    }

    while (currentDate <= endDate) {
        weekdays.forEach(weekday => {
            if(currentDate.getDay() == weekday)
                dates.push(currentDate)
        });
            currentDate = addDays.call(currentDate, 1)
        }

    return dates
}
  
function getCatalanDates(array) {

    const setmana = ['diumenge', 'dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte'];
    var result = [];

    array.forEach(date => {
        result.push(setmana[date.getDay()] + '<br>' + ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth()+1)).slice(-2) + '/' + date.getFullYear());
    });

    return result;
}

function createTable(array, idParent, colNum = 2) {
    var tableHTML = '<table class="table table-bordered table-striped" id="dayTable">';

    array.forEach((value, index) => { 
        if(index == 0) 
            tableHTML += '<tr>';

        tableHTML += '<td class="text-capitalize">' + value + '</td>';
    
        if(array.length - 1 == index) {
            tableHTML += '</tr>';
        } else if ((index + 1) % colNum == 0) {
            tableHTML += '</tr> <tr>';
        }  
    });

    tableHTML += '</table>';

    document.getElementById("" + idParent + "").innerHTML= tableHTML;
}



function createCalendarHeader(sessions, idParent, date) { 
    var prev = `<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Caret Back</title><path d="M321.94 98L158.82 237.78a24 24 0 000 36.44L321.94 414c15.57 13.34 39.62 2.28 39.62-18.22v-279.6c0-20.5-24.05-31.56-39.62-18.18z" fill="#fff"/></svg>`;
    var next = `<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Caret Forward</title><path d="M190.06 414l163.12-139.78a24 24 0 000-36.44L190.06 98c-15.57-13.34-39.62-2.28-39.62 18.22v279.6c0 20.5 24.05 31.56 39.62 18.18z" fill="#fff"/></svg>`;
    const mesos = ['gener', 'febrer', 'març', 'abril', 'maig', 'juny', 'juliol', 'agost', 'setembre', 'octubre', 'novembre', 'desembre'];

    var dateObject = new Date(date);

    var parsedSessions = sessions.map(e => {
        return "\"" + e + "\"";    
    })

    console.log(`createCalendar([${parsedSessions}], "${idParent}", "${new Date(dateObject)}", "PREV")`);
    var htmlHeader= `<table class="text-center table-bordered table my-4" style="width:330px; margin: 0 auto;">
                        <thead class="table-dark" >
                            <tr>
                                <th colspan="7">
                                    <div class="w-100 d-flex flex-row justify-content-around">
                                        <div style="width: 23px;cursor: pointer;">
                                            <a onclick='createCalendar([${parsedSessions}], "${idParent}", "${new Date(dateObject)}", "PREV")'>${prev}</a>
                                        </div>
                                        <div>
                                            <span class="text-capitalize">${mesos[dateObject.getMonth()]}</span> ${dateObject.getFullYear()}
                                        </div>
                                        <div style="width: 23px;cursor: pointer;">
                                            <a onclick='createCalendar([${parsedSessions}], "${idParent}", "${new Date(dateObject)}", "NEXT")'>${next}</a>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th>dl</th>
                                <th>dm</th>
                                <th>dc</th>
                                <th>dj</th>
                                <th>dv</th>
                                <th>ds</th>
                                <th>dg</th>
                            </tr>
                        </thead>
                        <tbody id="table-body-object">
                    `;

    return htmlHeader; 
}



function createCalendar(sessions, idParent, date = 'NULL', page = "NULL") {  
        
    var today = new Date();
    today.setHours(0,0,0,0)
    
    
    if(page == "PREV") {
        var fecha = new Date(date);
        fecha = fecha.setMonth((fecha.getMonth()-1));
        var actual = new Date(fecha);
        console.log('canal 1')
    } else if(page == "NEXT") {
        var fecha = new Date(date);
        fecha = fecha.setMonth((fecha.getMonth()+1));
        var actual = new Date(fecha);
        console.log('canal 2')
    } else if(date == 'NULL' && sessions.length > 0) {
        var actual = new Date(sessions[0]);
        console.log('canal 3')
    }  else {
        var actual = today;
        console.log('canal 4')
    }


    var daysInMonth = getDaysInMonth(actual.getMonth(), actual.getFullYear());
    var tableHTML = createCalendarHeader(sessions, idParent, actual);
  
    daysInMonth.forEach((day, index) => {

        // Afegir dies en blanc quan el mes comença entre setmana
        if(index == 0) {
            tableHTML += '<tr>';
            if(day.getDay() == 0) {
                // diumenge.getDay() == 0
                for (let i = 0; i < 6; i++) {
                    tableHTML += '<td>&nbsp</td>';                    
                }
            } else {
                for (let i = 1; i < day.getDay(); i++) {
                    tableHTML += '<td>&nbsp</td>';
                }
            }
        }

        // Comprova si el dia es troba dintre de les sessions
        if(sessions.length > 0 ) {
            for (let i = 0; i < sessions.length; i++) {
                const element = sessions[i];
                if(today.toString() == day.toString()) {
                    tableHTML += "<td class='today'>" + day.getDate() + '</td>';
                    break;
                } else if (element.toString() == day.toString()) {
                    tableHTML += "<td class='red'>" + day.getDate() + '</td>';
                    break;
                } else if((sessions.length)-1 == i) {
                    tableHTML += "<td>" + day.getDate() + '</td>';
                }   
            }
        } else {
            // Si hi ha 0 sessions
            if(today.toString() == day.toString()) {
                tableHTML += "<td class='today'>" + day.getDate() + '</td>';
            } else  {
                tableHTML += "<td>" + day.getDate() + '</td>';
            }   
        }
      
        if(daysInMonth.length - 1 == index && (day.getDay()) % 7 != 0) {
            for (let i = day.getDay(); i < 7; i++) {
                tableHTML += '<td>&nbsp</td>';
            }
            tableHTML += '</tr>';
        } else if ((day.getDay()) % 7 == 0) {
                tableHTML += '</tr><tr>';
        }

    })

    tableHTML += " </tbody></table>";
    /* window.onload = function() { */
        document.getElementById("" + idParent + "").innerHTML = tableHTML;
    /* } */
}


function getDaysInMonth(month, year) {
    var date = new Date(year, month, 1);
    var days = [];
    while (date.getMonth() === month) {
       days.push(new Date(date));
       date.setDate(date.getDate() + 1);
    }
    return days;
 }

 


















































/* 
*** Recurrència personalitzada **

- Cada setmana el dimecres
- Cada mes el primer dimecres
- Anualment el 1 de juny
- Cada dia entre setmana (dilluns-divendres)

*/



