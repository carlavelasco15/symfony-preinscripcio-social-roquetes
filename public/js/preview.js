
window.onload = function() {
    document.getElementById("activity_form_picture").onchange = function(e) {
        if(!e.target.files[0].name.match(/\.(jpe?g|png|gif)$/i)) {
            alert('El tipo del fichero debe ser JPG, PNG o GIF');
        } else {
            let reader = new FileReader();
            reader.readAsDataURL(e.target.files[0]);

            reader.onload = function() {
                let image = document.getElementById('preview');
                image.src = reader.result;
            }
        }
    }
}

function dayToNumber(dayName) {
    var days = {dill:1,dima:2,dime:3,dijo:4,dive:5,diss:6,dium:7};
    var day = days[dayName.toLowerCase().substr(0,4)];
    return day;  
}


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
    WEEKDAY = dayToNumber(document.getElementById("activity_form_weekday").value);
    console.log(WEEKDAY);

    if(START_DATE && END_DATE) {

        var start = moment(START_DATE),
            end   = moment(END_DATE),
            day   = WEEKDAY; 

        var result = [];
        var current = start.clone();

        while (current.day(7 + day).isBefore(end)) {
            result.push(current.clone());
        }
            
        result.forEach((element, index) => {
            if(index == 0) result = [];
            result.push(element.locale('fr').format('L'));
        });

        console.log(result);
        document.getElementById('totalSessions').innerHTML = result.length;
        document.getElementById('dayTable').innerHTML= arrayToTable(result);
    }
}



function arrayToTable(array, numColumnas = 3) {
    var tableHTML = '';

    array.forEach((value, index) => { 
        if(index == 0) 
            tableHTML += '<tr>';
        
        tableHTML += '<td>' + value + '</td>';
    
        if(array.length - 1 == index) {
            tableHTML += '</tr>';
        } else if ((index + 1) % numColumnas == 0) {
            tableHTML += '</tr> <tr>';
        }  
    });

    document.getElementById('totalSessions').innerHTML = array.length;
    document.getElementById('dayTable').innerHTML= tableHTML;
}




