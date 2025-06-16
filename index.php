<?php
session_start();
include "config.php";

function sendOtp($phone, $method) {
    $method_info = get_valid_method_and_key($method);

    $data = [
        'phone' => $phone,
        'gateway_key' => $method_info['gateway_key'],
        'method' => $method_info['method'],
    ];

    $ch = curl_init(API_URL_SEND);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . MERCHANT_KEY,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $result = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    file_put_contents('api_log.txt', "Send OTP Response: " . $result . "\n", FILE_APPEND);

    if ($result === false) {
        return ['status' => 'error', 'message' => 'Unable to contact the API. ' . $error];
    }

    return json_decode($result, true);
}

function verifyOtp($phone, $otp, $otp_id) {
    $method = $_SESSION['otp_method'] ?? DEFAULT_METHOD;
    $method_info = get_valid_method_and_key($method);

    $data = [
        'phone' => $phone,
        'otp' => $otp,
        'otp_id' => $otp_id,
        'gateway_key' => $method_info['gateway_key'],
    ];

    $ch = curl_init(API_URL_VERIFY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . MERCHANT_KEY,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $result = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    file_put_contents('api_log.txt', "Verify OTP Response: " . $result . "\n", FILE_APPEND);

    if ($result === false) {
        return ['status' => 'error', 'message' => 'Unable to contact the API. ' . $error];
    }

    return json_decode($result, true);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send_otp_confirmed'])) {
        $phone = $_POST['phone'];
        $method = $_POST['method'] ?? DEFAULT_METHOD;
        $_SESSION['otp_method'] = $method;

        $response = sendOtp($phone, $method);

        if (isset($response['status']) && $response['status'] === true) {
            $_SESSION['phone'] = $phone;
            $_SESSION['otp_id'] = $response['data']['id'];
            $message = "Kode OTP dikirim via " . strtoupper($method) . " ke $phone.";
        } else {
            $message = "Gagal mengirim OTP: " . ($response['message'] ?? 'Terjadi kesalahan.');
        }
    } elseif (isset($_POST['verify_otp'])) {
        $otp = implode('', $_POST['otp']);
        $phone = $_SESSION['phone'] ?? '';
        $otp_id = $_SESSION['otp_id'] ?? '';

        if ($phone && $otp_id) {
            $response = verifyOtp($phone, $otp, $otp_id);
            if (isset($response['status']) && $response['status'] === true) {
                $method = $_SESSION['otp_method'] ?? DEFAULT_METHOD;
                $_SESSION['welcome_message'] = "Selamat datang, $phone! (via " . strtoupper($method) . ")";
                header('Location: welcome.php');
                exit;
            } else {
                $message = "Verifikasi gagal: " . ($response['message'] ?? 'Terjadi kesalahan.');
            }
        } else {
            $message = "Nomor atau OTP tidak tersedia.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-RAPOR</title>
    <link rel="shortcut icon" href="https://centro.pelindo.co.id/img/logo-e-report.b4f16b90.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Header -->
    <header class="bg-white shadow-sm py-4 px-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <img src="https://centro.pelindo.co.id/img/logo-e-report.b4f16b90.png" alt="Logo" class="w-10 h-10">
            <h1 class="text-xl font-bold text-grey-700">E-RAPOR</h1>
        </div>
        <nav class="text-sm text-gray-600 hidden md:block">Transparansi Prestasi, Akses Tanpa Batas.</nav>
    </header>

    <!-- Main Section -->
    <main class="container mx-auto px-4 py-10">
        <div class="flex flex-col lg:flex-row gap-10 items-center justify-center">
            <!-- Left Banner -->
            <div class="w-full lg:w-1/2">
                <img src="https://www.scnsoft.de/education-industry/elearning-portal/elearning-portals-cover-picture.svg" 
                     alt="E-Learning Banner" class="w-full max-w-md mx-auto">
                <p class="text-center text-sm text-gray-500 mt-4">Setiap Nilai Ada Cerita, Setiap Siswa Punya Potensi.</p>
            </div>
        
            <!-- Right Form -->
            <div class="w-full lg:w-1/2 bg-white p-8 rounded-2xl shadow-md">
                <h2 class="text-2xl font-semibold text-center text-indigo-700 mb-6">Akses Rapor Pendidikan</h2>
                <!-- Form Kirim OTP -->
                <form id="otpForm" onsubmit="event.preventDefault(); showMethodModal();">
                    <label for="phoneInput" class="block text-sm text-gray-600 mb-1">Masukkan No Hp Terdaftar</label>
                    <input type="text" id="phoneInput" name="phone" placeholder="08xxxxxxxxxx" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4"
                        pattern="08[0-9]{6,12}">

                    <button type="submit"
                        class="w-full bg-indigo-600 text-white py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                        Dapatkan OTP
                    </button>
                </form>

                <div class="border-t my-6"></div>

                <!-- Form OTP -->
                <h3 class="text-center font-medium text-gray-700 mb-3">Masukkan Kode OTP</h3>
                <form method="POST">
                    <div class="flex justify-center gap-2 mb-4">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                            <input type="text" name="otp[]" maxlength="1" pattern="[0-9]" required
                                   class="w-10 h-12 text-center text-lg border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 shadow-sm"
                                   oninput="moveToNext(this, event)">
                        <?php endfor; ?>
                    </div>
                    <button type="submit" name="verify_otp"
                        class="w-full bg-green-600 text-white py-2 rounded-lg font-medium hover:bg-green-700 transition">
                        Verifikasi
                    </button>
                </form>

                <?php if (!empty($message)): ?>
                    <div class="mt-4 text-sm text-red-600 text-center animate-pulse">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-10 py-4 text-center text-sm text-gray-500">
        &copy; <?= date('Y') ?> E-RAPOR App. Semua Hak Dilindungi.
    </footer>

    <!-- Modal Metode OTP -->
    <div id="methodModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-sm shadow-xl animate-fadeIn">
            <h3 class="text-lg font-semibold text-center text-gray-700 mb-4">Pilih Metode OTP</h3>
            <form method="POST">
                <input type="hidden" name="phone" id="modalPhone" required>
                <input type="hidden" name="method" id="selectedMethod" required>

                <div class="grid grid-cols-2 gap-3 mb-5" id="methodButtons">
                    <button type="button" data-method="sms"
                        class="method-btn bg-gray-100 hover:bg-gray-200 text-sm font-medium rounded-lg px-3 py-2">
                        SMS
                    </button>
                    <button type="button" data-method="whatsapp"
                        class="method-btn bg-indigo-600 text-white text-sm font-medium rounded-lg px-3 py-2">
                        WhatsApp
                    </button>
                    <button type="button" data-method="telepon"
                        class="method-btn bg-gray-100 hover:bg-gray-200 text-sm font-medium rounded-lg px-3 py-2">
                        Telepon
                    </button>
                    <button type="button" data-method="misscall"
                        class="method-btn bg-gray-100 hover:bg-gray-200 text-sm font-medium rounded-lg px-3 py-2">
                        Missed Call
                    </button>
                </div>

                <button type="submit" name="send_otp_confirmed"
                    class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                    Lanjutkan
                </button>
            </form>
        </div>
    </div>

    <!-- Animasi Modal -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>

    <!-- Script OTP Modal -->
    <script>
        function showMethodModal() {
            const phone = document.getElementById('phoneInput').value.trim();
            if (!phone) return;
            document.getElementById('modalPhone').value = phone;
            document.getElementById('methodModal').classList.remove('hidden');
            document.getElementById('methodModal').classList.add('flex');
            selectMethod('whatsapp');
        }

        function selectMethod(method) {
            document.getElementById('selectedMethod').value = method;
            document.querySelectorAll('.method-btn').forEach(btn => {
                if (btn.getAttribute('data-method') === method) {
                    btn.classList.remove('bg-gray-100', 'hover:bg-gray-200');
                    btn.classList.add('bg-indigo-600', 'text-white');
                } else {
                    btn.classList.remove('bg-indigo-600', 'text-white');
                    btn.classList.add('bg-gray-100', 'hover:bg-gray-200');
                }
            });
        }

        document.querySelectorAll('.method-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const method = btn.getAttribute('data-method');
                selectMethod(method);
            });
        });

        function moveToNext(el, event) {
            if (el.value.length >= el.maxLength) {
                const next = el.nextElementSibling;
                if (next && next.tagName === "INPUT") {
                    next.focus();
                }
            }
        }
    </script>
</body>

</body>
</html>
