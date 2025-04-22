//alert box
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

// confirm dialog box
function confirmAlert(title, page) {
    Swal.fire({
        text: title,
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes",
        cancelButtonColor: "#006400",
        width: "350px",
        padding: "10px"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = page;
        }
    });
}

// confirm dialog box for ad delete
function confirmAlertAd(ad_id) {
    Swal.fire({
        text: "Are you sure you want to delete this ad?",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes",
        cancelButtonColor: "#006400",
        width: "350px",
        padding: "10px"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "delete_ad.php?ad_id=" + ad_id;;
        }
    });
}

// confirm dialog box for remove from wishlist
function confirmAlertWishlist() {
    Swal.fire({
        text: "Are you sure you remove from wishlist?",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes",
        cancelButtonColor: "#006400",
        width: "350px",
        padding: "10px"
    })
}

// confirm dialog box for request delete
function confirmAlerRequest(request_id) {
    Swal.fire({
        text: "Are you sure you want to delete this request?",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes",
        cancelButtonColor: "#006400",
        width: "350px",
        padding: "10px"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "delete_request.php?request_id=" + request_id;;
        }
    });
}
