<?php
function showAlert($title, $icon, $color, $redirectUrl)
{
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
    window.onload = function() {
        Swal.fire({
            toast: true,
            position: 'center',
            icon: '$icon',
            title: '$title',
            showConfirmButton: false,
            color: '$color',
            timer: 3000
        }).then(() => {
            window.location.href = '$redirectUrl';
        });
    };
    </script>";
}
