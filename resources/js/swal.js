// Sweet Alert configs - MODAL AND TOAST
import Swal from 'sweetalert2'

export const swalModal = Swal.mixin({
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  confirmButtonText: 'Tenho',
  cancelButtonText: 'Cancelar',
  buttonsStyling: false,
  heightAuto: false,
  showClass: {
    popup: 'animate__animated animate__zoomIn animate__faster',
  },
  hideClass: {
    popup: 'animate__animated animate__zoomOut animate__faster',
  },
  customClass: {
    confirmButton: 'btn btn-lg btn-primary mr-2 font-weight-bold',
    cancelButton: 'btn btn-lg btn-light'
  }
})

const swalToastInit = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 4500,
  timerProgressBar: true,
  showCloseButton: true,
  didOpen: (toast) => {
    toast.addEventListener('click', () => {Swal.close()})
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})

export const swalToast = {
  success(message) {
    return swalToastInit.fire({
      icon: 'success',
      title: message,
      iconColor: '#38c172'
    })
  },

  warning(message) {
    return swalToastInit.fire({
      icon: 'warning',
      iconHtml: '<i class="fas fa-exclamation-triangle"></i>',
      title: message,
      iconColor: '#f69220'
    })
  },

  info(message) {
    return swalToastInit.fire({
      icon: 'info',
      iconHtml: '<i class="fas fa-info-circle"></i>',
      title: message,
      iconColor: '#39a0da'
    })
  },

  error(message) {
    return swalToastInit.fire({
      icon: 'error',
      iconHtml: '<i class="fas fa-times"></i>',
      title: message,
      iconColor: '#e3342f'
    })
  }
}
