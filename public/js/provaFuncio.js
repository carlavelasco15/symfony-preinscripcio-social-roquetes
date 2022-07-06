function getSessions (startDate, endDate, weekdays) {
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
  
  // Usage
  const dates = getSessions(new Date(2013, 10, 22), new Date(2013, 11, 25), [2,3])
  dates.forEach(function (date) {
    /* console.log(date.getDate() + '/' + date.getMonth() + '/' + date.getFullYear()) */
  })



  /* function calculateSessionsMoment(startActivity, endActivity, weekday) {
        var start = moment(startActivity),
            end = moment(endActivity);
        var result = [];
  
        weekday.forEach(day => {
            var current = start.clone();
            while (current.day(7 + day).isBefore(end)) {
                result.push(current.clone());
            }
        });

        let sortedResult = result.sort(function(a, b){
            return moment(a).format('X')-moment(b).format('X')
        });

        return sortedResult;
    } */