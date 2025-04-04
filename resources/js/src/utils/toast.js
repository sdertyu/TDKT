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

export function toastError(message = 'Lỗi') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        timer: 3000,
        timerProgressBar: true,
        icon: 'error',
        // title: 'Lỗi',
        text: message,
        showConfirmButton: false
    });
}
