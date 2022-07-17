/* 
* (2022) Carla Velasco Fàbrega <carlavelasco15@gmail.com>
*/




function getDaysInMonth(month, year) {
    var date = new Date(year, month, 1);
    var days = [];
    while (date.getMonth() === month) {
       days.push(new Date(date));
       date.setDate(date.getDate() + 1);
    }
    return days;
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