const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  },
});

const ConfirmationAlert = Swal.mixin({
  icon: "warning",
  confirmButtonText: __LANG === "es" ? "Aceptar" : "Accept",
  showCancelButton: true,
  cancelButtonText: __LANG === "es" ? "Cancelar" : "Cancel",
  customClass: {
    confirmButton: "btn btn-primary mr-2",
    cancelButton: "btn btn-secondary",
  },
  buttonsStyling: false,
});
const Alert = Swal.mixin({
  confirmButtonText: __LANG === "es" ? "Aceptar" : "Accept",
  customClass: {
    confirmButton: "btn btn-primary",
  },
  buttonsStyling: false,
});
