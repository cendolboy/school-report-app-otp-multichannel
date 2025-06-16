<?php
session_start();

if (!isset($_SESSION['welcome_message'])) {
    header('Location: index.php');
    exit;
}

$welcomeMessage = $_SESSION['welcome_message'];
$userName = 'Ahmad Fikri';
$photoUrl = 'https://i.pravatar.cc/100?img=12'; // placeholder avatar

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Peserta Didik</title>
    <link rel="shortcut icon" href="https://centro.pelindo.co.id/img/logo-e-report.b4f16b90.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800">

<!-- Navbar -->
<header class="bg-white shadow-md flex items-center justify-between px-6 py-3 sticky top-0 z-10">
    <div class="flex items-center gap-3">
        <img src="https://centro.pelindo.co.id/img/logo-e-report.b4f16b90.png" class="w-8 h-8" alt="Logo">
        <h1 class="text-lg font-bold text-grey-700">E-RAPOR</h1>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-sm font-medium hidden sm:block"><?= htmlspecialchars($userName); ?></span>
        <img src="<?= $photoUrl ?>" alt="Profile" class="w-9 h-9 rounded-full border">
        <form method="POST">
            <button type="submit" name="logout"
                class="text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                Keluar
            </button>
        </form>
    </div>
</header>

<!-- Layout -->
<div class="flex min-h-[calc(100vh-64px)]">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md hidden md:block">
        <nav class="p-6 space-y-3 text-sm">
            <h2 class="text-xs text-gray-400 uppercase tracking-wide mb-2">Navigasi</h2>
            <a href="#" class="block px-3 py-2 rounded hover:bg-indigo-50 text-indigo-700 font-medium bg-indigo-100">Dashboard</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">Lihat Rapor</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">Data Orang Tua</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">Pengaturan Akun</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <h2 class="text-2xl font-semibold mb-2">Selamat Datang, <?= htmlspecialchars($userName); ?> 👋</h2>
        <p class="text-gray-600 mb-6"><?= htmlspecialchars($welcomeMessage); ?></p>

        <!-- Grafik Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Grafik Absensi -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Grafik Absensi</h3>
                <canvas id="absensiChart" class="w-full h-64"></canvas>
            </div>

            <!-- Grafik Pelanggaran -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Grafik Pelanggaran</h3>
                <canvas id="pelanggaranChart" class="w-full h-64"></canvas>
            </div>
        </div>

        <!-- Rapor Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-4 border-b text-lg font-medium text-indigo-700">
                Ringkasan Nilai Rapor
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-indigo-600 text-white text-left">
                        <tr>
                            <th class="py-3 px-4">Mata Pelajaran</th>
                            <th class="py-3 px-4">Nilai</th>
                            <th class="py-3 px-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr class="border-b">
                            <td class="py-3 px-4">Matematika</td>
                            <td class="py-3 px-4">85</td>
                            <td class="py-3 px-4">Baik</td>
                        </tr>
                        <tr class="bg-gray-50 border-b">
                            <td class="py-3 px-4">Bahasa Indonesia</td>
                            <td class="py-3 px-4">90</td>
                            <td class="py-3 px-4">Sangat Baik</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 px-4">IPA</td>
                            <td class="py-3 px-4">78</td>
                            <td class="py-3 px-4">Cukup</td>
                        </tr>
                        <tr class="bg-gray-50 border-b">
                            <td class="py-3 px-4">IPS</td>
                            <td class="py-3 px-4">88</td>
                            <td class="py-3 px-4">Baik</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4">Bahasa Inggris</td>
                            <td class="py-3 px-4">92</td>
                            <td class="py-3 px-4">Sangat Baik</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Footer -->
<footer class="text-center text-sm text-gray-500 py-4 bg-white mt-auto border-t">
    &copy; <?= date('Y') ?> E-RAPOR. Semua hak dilindungi.
</footer>

<script>
    // Grafik Absensi
    const absensiCtx = document.getElementById('absensiChart').getContext('2d');
    new Chart(absensiCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Alfa'],
            datasets: [{
                data: [180, 10, 5],
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Grafik Pelanggaran
    const pelanggaranCtx = document.getElementById('pelanggaranChart').getContext('2d');
    new Chart(pelanggaranCtx, {
        type: 'doughnut',
        data: {
            labels: ['Ringan', 'Sedang', 'Berat'],
            datasets: [{
                data: [2, 1, 0],
                backgroundColor: ['#60A5FA', '#FBBF24', '#F87171'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

</body>
</html>
