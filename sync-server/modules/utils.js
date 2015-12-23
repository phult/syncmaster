var Utils = function () {
    this.now = function () {
        var date = new Date();
        var year = date.getFullYear();
        var month = (1 + date.getMonth()).toString();
        month = month.length > 1 ? month : "0" + month;
        var day = date.getDate().toString();
        day = day.length > 1 ? day : "0" + day;
        var hour = date.getHours();
        var minute = date.getMinutes();
        var second = date.getSeconds();
        return day + "/" + month + "/" + year + " " + hour + ":" + minute + ":" + second;
    };
    this.log = function (message, option) {
        if (option != null) {
            if (option.timeDisp) {
                console.log(this.now() + ": " + message);
            }
        } else {
            console.log(message);
        }
    };
    this.parseObjectToRequestURL = function (obj) {
        var retval = "";
        var index = 0;
        for (var property in obj) {
            retval += (index == 0 ? "" : "&") + property + "=" + obj[property];
            index++;
        }        
        return retval;
    };
};
module.exports = new Utils();