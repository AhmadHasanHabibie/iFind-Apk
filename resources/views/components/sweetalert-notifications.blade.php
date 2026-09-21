<!-- SweetAlert2 Modern Notification & Dialog Suite for i-Find -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-popup {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        border-radius: 1.5rem !important;
        padding: 1.75rem !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
    }
    .swal2-toast {
        border-radius: 1.25rem !important;
        padding: 0.875rem 1.25rem !important;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.18) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(10px) !important;
    }
    .swal2-title {
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.025em !important;
    }
    .swal2-html-container {
        font-weight: 500 !important;
        color: #475569 !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
    }
    .swal2-timer-progress-bar {
        background: #2563eb !important;
        height: 3px !important;
    }
</style>

<script>
    // 1. Toast Mixin Config
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    window.Toast = Toast;

    window.toastSuccess = function(message, title = 'Berhasil') {
        Toast.fire({
            icon: 'success',
            title: title,
            text: message,
            iconColor: '#10b981'
        });
    };

    window.toastError = function(message, title = 'Perhatian') {
        Toast.fire({
            icon: 'error',
            title: title,
            text: message,
            iconColor: '#ef4444'
        });
    };

    window.toastWarning = function(message, title = 'Peringatan') {
        Toast.fire({
            icon: 'warning',
            title: title,
            text: message,
            iconColor: '#f59e0b'
        });
    };

    window.toastInfo = function(message, title = 'Informasi') {
        Toast.fire({
            icon: 'info',
            title: title,
            text: message,
            iconColor: '#3b82f6'
        });
    };

    // 2. Custom Action Confirmation Dialog
    window.confirmAction = function(message, onConfirm, title = 'Konfirmasi Tindakan', confirmText = 'Ya, Lanjutkan', icon = 'warning') {
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            iconColor: icon === 'warning' ? '#f59e0b' : (icon === 'danger' ? '#ef4444' : '#3b82f6'),
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-lg shadow-blue-600/25 transition-all ml-2 cursor-pointer',
                cancelButton: 'px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    };

    // 3. Override Native Chrome alert() with Sleek Modal
    window.alert = function(message) {
        Swal.fire({
            title: 'Pemberitahuan',
            text: message,
            icon: 'info',
            iconColor: '#3b82f6',
            confirmButtonText: 'Mengerti',
            customClass: {
                confirmButton: 'px-6 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-lg shadow-blue-600/25 transition-all cursor-pointer'
            },
            buttonsStyling: false
        });
    };

    // 4. Global Form Interceptor for data-confirm
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.dataset && form.dataset.confirm && !form.dataset.confirmed) {
                e.preventDefault();
                const message = form.dataset.confirm;
                const title = form.dataset.confirmTitle || 'Konfirmasi Tindakan';
                const confirmText = form.dataset.confirmBtn || 'Ya, Lanjutkan';
                const isDanger = form.dataset.confirmDanger === 'true';

                Swal.fire({
                    title: title,
                    text: message,
                    icon: isDanger ? 'warning' : 'question',
                    iconColor: isDanger ? '#ef4444' : '#3b82f6',
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: isDanger
                            ? 'px-5 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-lg shadow-rose-600/25 transition-all ml-2 cursor-pointer'
                            : 'px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-lg shadow-blue-600/25 transition-all ml-2 cursor-pointer',
                        cancelButton: 'px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all cursor-pointer'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            }
        });

        // 5. Automatic Flash Notifications from Laravel Session
        @if (session('success'))
            window.toastSuccess(@json(session('success')), 'Berhasil');
        @endif

        @if (session('status'))
            @php
                $statusMsg = session('status');
                if ($statusMsg === 'profile-updated') {
                    $statusMsg = 'Profil berhasil diperbarui.';
                } elseif ($statusMsg === 'password-updated') {
                    $statusMsg = 'Password berhasil diperbarui.';
                } elseif ($statusMsg === 'verification-link-sent') {
                    $statusMsg = 'Tautan verifikasi baru telah dikirim ke email Anda.';
                }
            @endphp
            window.toastSuccess(@json($statusMsg), 'Pemberitahuan');
        @endif

        @if (session('error'))
            window.toastError(@json(session('error')), 'Perhatian');
        @elseif ($errors->any())
            window.toastError(@json($errors->first()), 'Validasi Gagal');
        @endif

        @if (session('warning'))
            window.toastWarning(@json(session('warning')), 'Peringatan');
        @endif

        @if (session('info'))
            window.toastInfo(@json(session('info')), 'Informasi');
        @endif
    });
</script>
