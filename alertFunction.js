function showAlert(title, icon, color) {
    Swal.fire({
        toast: true,
        position: 'center',
        icon: icon,
        title: title,
        showConfirmButton: false,
        color: color,
        timer: 2000
    });
}