document.getElementById("activity_form_start_date").onchange = function(e) {
    START_DATE = document.getElementById("activity_form_start_date").value;
    if(END_DATE && WEEKDAY) {
        var sessions = calculateSessions(START_DATE, END_DATE, WEEKDAY);
        var arrayPretty = i18nDateCatalan(sessions);
        arrayToSimpleTable(arrayPretty, 2); 
    }
}

document.getElementById("activity_form_end_date").onchange = function(e) {
    END_DATE = document.getElementById("activity_form_end_date").value;
    
    if(START_DATE && WEEKDAY) {
        var sessions = calculateSessions(START_DATE, END_DATE, WEEKDAY);
        var arrayPretty = i18nDateCatalan(sessions);
        arrayToSimpleTable(arrayPretty, 2);  
    }
}

document.getElementById("activity_form_weekday").onchange = function(e) {   
    WEEKDAY = dayToNumber(document.getElementById("activity_form_weekday").value);
    if(START_DATE && END_DATE) {
        var sessions = calculateSessions(START_DATE, END_DATE, [1, 4, 3]);
        var arrayPretty = i18nDateCatalan(sessions);
        arrayToSimpleTable(arrayPretty, 2); 
    }
}

/* const diesSetmana = ['dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte', 'diumenge'];
const array = startActivity.split('-');
const [any, mes, dia] = array
console.log(dia) */

calculateSessions("2022-07-01", "2022-07-30", [3,5]);

function calculateSessions(startActivity, endActivity, weekday) {
        var start = moment(startActivity),
            end = moment(endActivity);
        var result = [];
        var days = [];

        if (typeof weekday == "object") {
            days = weekday; 
        } else if (typeof weekday == "number") {
            days[0] = weekday;
        }
  
        days.forEach(day => {
            var current = start.clone();
            while (current.day(7 + day).isBefore(end)) {
                result.push(current.clone());
            }
        });

        let sortedResult = result.sort(function(a, b){
            return moment(a).format('X')-moment(b).format('X')
        });

        return sortedResult;
    }


function i18nDateCatalan(array) {
    var result = [];

    array.forEach(element => {
        result.push(element.locale('ca').format('dddd') + '<br>' + element.locale('ca').format('L'));
    });

    return result;
}


function i18nDateUSA(array) {
    var result = [];

    array.forEach(element => {
        result.push(element.format('L'));
    });

    return result;
}


function dayToNumber(dayName) {
    var days = {dill:1,dima:2,dime:3,dijo:4,dive:5,diss:6,dium:7};
    var day = days[dayName.toLowerCase().substr(0,4)];
    return day;  
}

function arrayToSimpleTable(array, numColumnas = 3) {
    var tableHTML = '';

    array.forEach((value, index) => { 
        if(index == 0) 
            tableHTML += '<tr>';

        tableHTML += '<td class="text-capitalize">' + value + '</td>';
    
        if(array.length - 1 == index) {
            tableHTML += '</tr>';
        } else if ((index + 1) % numColumnas == 0) {
            tableHTML += '</tr> <tr>';
        }  
    });

    //totalTableSessions
    //
    document.getElementById('totalSessions').innerHTML = array.length;
    document.getElementById('dayTable').innerHTML= tableHTML;
}

/* function createSimpleTableBody(array, numColumnas = 3) {

}

function createSimpleTableHeader() {

}
 */



