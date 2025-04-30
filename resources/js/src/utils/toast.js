import Swal from 'sweetalert2';

export function toastSuccess(message = 'Thành công') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        timer: 3000,
        timerProgressBar: true,
        icon: 'success',
        // title: 'Thành công',
        text: message,
        showConfirmButton: false
    });
}

export function toastError(message = 'Lỗi', timer = 3000) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        timer: timer,
        timerProgressBar: true,
        icon: 'error',
        // title: 'Lỗi',
        html: message,
        showConfirmButton: false
    });
}
