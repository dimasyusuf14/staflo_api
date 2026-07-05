<x-mail::message>
# Halo, {{ $user->name }}

Password akun Anda telah berhasil direset oleh administrator.

**Password Default:**

<x-mail::panel>
{{ $defaultPassword }}
</x-mail::panel>

Silakan login menggunakan password tersebut, kemudian segera ubah password Anda melalui menu **Change Password** demi menjaga keamanan akun.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
