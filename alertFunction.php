<?php

function showAlert($title, $icon, $redirectUrl)
{
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
Swal.fire({
    toast: true,
    position: 'center',
    icon: '$icon',
    title: '$title',
    showConfirmButton: false,
    timer: 3000
}).then(() => {
    window.location.href = '$redirectUrl';
});
</script>";
}