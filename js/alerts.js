const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
    }
});



const ConfirmationAlert = Swal.mixin({
    icon: "warning",
    title: __LANG === 'es' ? 'Seguro que desea borrarlo?' : 'Are you sure you want to delete it?',
    confirmButtonText: __LANG === 'es' ? "Aceptar" : 'Accept',
    showCancelButton: true,
    cancelButtonText: __LANG === 'es' ? "Cancelar" : 'Cancel'
});