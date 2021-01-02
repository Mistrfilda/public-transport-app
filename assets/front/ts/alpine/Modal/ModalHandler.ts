window.handleModal = function(): object {
    return {
        show: false,
        openModal() { this.show = true },
        click() { this.show = !this.show}
    }
}