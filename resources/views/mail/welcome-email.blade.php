@component('mail::message')
    # Selamat Datang di Staflo! 🎉

    Halo **{{ $userName }}**,

    Akun Anda telah berhasil dibuat di sistem **Staflo**. Berikut adalah detail login Anda:

    @component('mail::panel')
        **Email:** {{ $userEmail }}
        **Password:** `{{ $userPassword }}`
        **Role:** {{ $userRole }}
    @endcomponent

    ## Informasi Penting:
    - ⚠️ Segera ubah password Anda setelah login pertama kali
    - 🔐 Jangan bagikan password ini kepada siapa pun
    - 📱 Gunakan kredensial ini untuk mengakses platform Staflo

    ## Langkah Berikutnya:
    1. Buka aplikasi Staflo
    2. Login dengan email dan password di atas
    3. Ubah password Anda ke yang lebih aman

    Jika Anda memiliki pertanyaan atau membutuhkan bantuan, hubungi administrator.

    @component('mail::button', ['url' => config('app.url')])
        Buka Staflo
    @endcomponent

    Terima kasih,
    Tim Staflo
@endcomponent
