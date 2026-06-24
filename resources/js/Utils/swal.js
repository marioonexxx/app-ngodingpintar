import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const primaryColor = '#0284c7';

const baseOptions = {
    confirmButtonColor: primaryColor,
    cancelButtonColor: '#64748b',
    reverseButtons: true,
};

export const confirmAction = async ({
    title = 'Konfirmasi tindakan',
    text = 'Data akan diproses.',
    confirmButtonText = 'Ya, lanjutkan',
    icon = 'question',
    confirmButtonColor = primaryColor,
} = {}) => {
    const result = await Swal.fire({
        ...baseOptions,
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText: 'Batal',
        confirmButtonColor,
        preConfirm: () => {
            Swal.getConfirmButton().disabled = true;
            return true;
        }
    });

    return result.isConfirmed;
};

export const successAlert = (text = 'Data berhasil diproses.', title = 'Berhasil') => (
    Swal.fire({
        ...baseOptions,
        title,
        text,
        icon: 'success',
        confirmButtonText: 'OK',
    })
);

export const errorAlert = (text = 'Periksa kembali data yang diisi.', title = 'Gagal') => (
    Swal.fire({
        ...baseOptions,
        title,
        text,
        icon: 'error',
        confirmButtonText: 'OK',
    })
);
