window.handleModal = function(): object {
    return {
        show: false,
        openModal() { this.show = true },
        closeModal() { this.show = false }
    }
}