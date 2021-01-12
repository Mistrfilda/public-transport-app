window.clock = function (): object {
    return {
        initClock() {
            let clockElement = this.$el;

            const setClock = () => {
                var d = new Date();
                clockElement.innerHTML = d.toLocaleTimeString();
            }

            setClock();
            setInterval(function () {
                setClock();
            }, 1000);
        },
    }
}