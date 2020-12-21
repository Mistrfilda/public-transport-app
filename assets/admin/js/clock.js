export default function() {
    let clockElement = document.getElementById("departure-table-clock");

    if (clockElement !== null) {
        myTimer();

        setInterval(function () {
            myTimer();
        }, 1000);
    }

    function myTimer() {
        var d = new Date();
        document.getElementById("departure-table-clock").innerHTML = d.toLocaleTimeString();
    }
}