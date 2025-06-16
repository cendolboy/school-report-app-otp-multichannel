# 📚 Rapor Digital Sekolah dengan OTP Multi-Channel Login

![Tampilan Aplikasi](https://github.com/cendolboy/school-report-app-otp-multichannel/blob/main/Screenshoot.jpeg)

Aplikasi **Rapor Digital** untuk sekolah yang dirancang agar wali murid dapat mengakses nilai siswa dengan **login menggunakan nomor HP** yang sudah terdaftar di sekolah, serta **autentikasi OTP multi-channel** yang andal (SMS, WhatsApp, dan Panggilan Suara).

> 🔐 Sistem OTP menggunakan layanan [Fazpass Secure](https://www.fazpass.com) yang mendukung fallback otomatis antar channel jika terjadi kegagalan pengiriman.

---

## 🚀 Fitur Utama

* ✅ Login admin untuk manajemen data
* ✅ Guru dapat input nilai siswa
* ✅ Siswa dan wali murid bisa melihat rapor
* ✅ Login wali murid cukup dengan nomor HP terdaftar
* ✅ OTP Multi-Channel:

  * SMS
  * WhatsApp
  * Voice Call
* ✅ **Auto-fallback channel** jika OTP gagal dikirim

---

## 🧰 Teknologi yang Digunakan

* **Frontend**: HTML, CSS, JavaScript
* **Backend**: Node.js / PHP *(ubah sesuai proyekmu)*
* **Database**: MySQL
* **OTP Service**: [Fazpass Secure](https://www.fazpass.com)

---

## 🗂️ Struktur Direktori (Contoh)

```
/public         → Tampilan pengguna (UI)
/views          → Template HTML/EJS
/backend        → API, logic OTP, autentikasi
/config         → Konfigurasi Fazpass & koneksi database
```

---

## ⚙️ Instalasi & Setup

> Pastikan kamu sudah menginstal `Node.js` atau `PHP` (tergantung stack), serta MySQL untuk database.

```bash
# Clone repository
git clone https://github.com/username/rapor-digital-sekolah-otp-multichannel.git
cd rapor-digital-sekolah-otp-multichannel

# Instal dependensi (jika Node.js)
npm install

# Atur koneksi database & Fazpass di file config (misal: .env atau config.js)

# Jalankan aplikasi
npm start
```

---

## 🧪 Uji Coba

1. Masukkan nomor HP wali murid yang terdaftar
2. Terima OTP via salah satu channel (SMS/WA/Call)
3. Jika satu channel gagal, sistem otomatis mengirim lewat channel lainnya
4. Masukkan OTP → login berhasil

---

## 🤝 Kolaborasi & Lisensi

Proyek ini open source dan berada di bawah lisensi [MIT](LICENSE).

---

## 📩 Kontak

📧 Untuk pertanyaan, kerja sama, atau feedback:
[amosduan.ad@gmail.com](mailto:amosduan.ad@gmail.com)

---

## 🔖 Hashtag & Tag YouTube

```
#RaporDigital #OTPLogin #AplikasiSekolah #MultiChannelOTP #FazpassSecure
#DigitalisasiSekolah #LoginNomorHP #EdTech #ProjectSekolah #ProgrammerIndonesia
```

---

> Made with ❤️ for better education access and smarter authentication.
