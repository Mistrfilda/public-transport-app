window.handleModal = function(): object {
    return {
        show: false,
        click() { this.show = !this.show}
    }
}