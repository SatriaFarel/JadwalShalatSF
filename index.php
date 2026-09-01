<?php
/**
 * Konfigurasi Zona Waktu Server
 */
date_default_timezone_set('Asia/Jakarta');

/**
 * Array Nama Hari dan Bulan dalam Bahasa Indonesia
 */
$namaHari = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];

$namaBulan = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Tanggal Masehi Format Indonesia
$hariEn = date('l');
$bulanNum = date('n');
$tanggalMasehi = $namaHari[$hariEn] . ", " . date('j') . " " . $namaBulan[$bulanNum] . " " . date('Y');

/**
 * Pemetaan Nama Bulan Hijriah Ke Bahasa Indonesia
 */
$bulanHijriahIndo = [
    1 => 'Muharram', 2 => 'Safar', 3 => 'Rabiul Awal', 4 => 'Rabiul Akhir',
    5 => 'Jumadil Awal', 6 => 'Jumadil Akhir', 7 => 'Rajab', 8 => 'Sya\'ban',
    9 => 'Ramadan', 10 => 'Syawal', 11 => 'Zulkaidah', 12 => 'Zulhijah'
];

/**
 * Fungsi Mengambil Data Hijriah dari Aladhan API
 */
function getHijriDate() {
    $today = date("d-m-Y"); 
    $url = "https://api.aladhan.com/v1/gToH?date=" . $today;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $json = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }
    curl_close($ch);

    $data = json_decode($json, true);
    if (!$data || !isset($data['data']['hijri'])) {
        return null;
    }

    return $data['data']['hijri'];
}

$hijriData = getHijriDate();
$statusRamadan = "";
$isIdulFitri = false;

if ($hijriData) {
    $hijriDay   = intval($hijriData['day']);
    $hijriMonth = intval($hijriData['month']['number']);
    $hijriYear  = $hijriData['year'];
    $namaBulanHijriah = isset($bulanHijriahIndo[$hijriMonth]) ? $bulanHijriahIndo[$hijriMonth] : $hijriData['month']['en'];

    if ($hijriMonth == 9) {
        $statusRamadan = "Ramadan ke-" . $hijriDay . " " . $hijriYear . " H";
    } elseif ($hijriMonth == 10 && $hijriDay == 1) {
        $statusRamadan = "Selamat Idul Fitri! Mohon maaf lahir dan batin.";
        $isIdulFitri = true;
    } else {
        $statusRamadan = $hijriDay . " " . $namaBulanHijriah . " " . $hijriYear . " H";
    }
} else {
    $statusRamadan = "Gagal mengambil data Hijriyah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Shalat & Kalender Hijriah - Jakarta</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Scheherazade+New:wght@700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #f39c12;
            --bg-overlay: rgba(15, 23, 42, 0.78);
            --card-bg: rgba(255, 255, 255, 0.08);
            --card-border: rgba(255, 255, 255, 0.15);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(var(--bg-overlay), var(--bg-overlay)), url('src/latar.png') center/cover no-repeat fixed;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 2rem 1rem;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header Section */
        header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header-img {
            width: 80px;
            height: auto;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
            transition: transform 0.3s ease;
        }

        .header-img:hover {
            transform: scale(1.08) rotate(3deg);
        }

        h1 {
            font-family: 'Scheherazade New', serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        /* Container Tanggal Masehi & Hijriah */
        .dates-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .date-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--card-border);
            color: #ffffff;
            padding: 0.4rem 1rem;
            border-radius: 99px;
            font-size: 0.88rem;
            font-weight: 600;
            backdrop-filter: blur(8px);
        }

        .date-badge.hijri {
            background: rgba(243, 156, 18, 0.15);
            border-color: rgba(243, 156, 18, 0.4);
            color: #f1c40f;
        }

        /* Clock Card */
        .clock-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 1.25rem;
            text-align: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        #clock {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
        }

        .timezone-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Grid Jadwal Shalat */
        .grid-jadwal {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .card-sholat {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.25rem 1rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-sholat:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-4px);
            border-color: rgba(243, 156, 18, 0.5);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .card-sholat .icon {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .card-sholat .label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-sholat .time {
            font-size: 1.35rem;
            font-weight: 700;
            color: #ffffff;
            margin-top: 0.25rem;
        }

        /* Footer */
        footer {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: auto;
            padding-top: 2rem;
        }

        footer a {
            color: var(--primary-gold);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        footer a:hover {
            color: #f1c40f;
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 600px) {
            h1 { font-size: 1.75rem; }
            #clock { font-size: 2rem; }
            .grid-jadwal { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header -->
        <header>
            <img class="header-img" src="https://img.icons8.com/fluency/96/ffffff/mosque.png" alt="Ikon Masjid">
            <h1>Jadwal Shalat & Waktu Ibadah</h1>
            
            <!-- Tanggal Masehi & Hijriah -->
            <div class="dates-wrapper">
                <div class="date-badge">
                    📅 <?php echo $tanggalMasehi; ?>
                </div>
                <div class="date-badge hijri" id="ramadan-status">
                    🌙 <?php echo htmlspecialchars($statusRamadan); ?>
                </div>
            </div>
        </header>

        <!-- Jam Digital (WIB / Jakarta) -->
        <div class="clock-card">
            <div id="clock">00:00:00</div>
            <div class="timezone-label">WIB (Waktu Indonesia Barat - Jakarta)</div>
        </div>

        <!-- Grid Waktu Shalat -->
        <div class="grid-jadwal">
            <div class="card-sholat">
                <div class="icon">🌅</div>
                <div class="label">Imsak</div>
                <div class="time" id="imsak">--:--</div>
            </div>
            <div class="card-sholat">
                <div class="icon">🏙️</div>
                <div class="label">Subuh</div>
                <div class="time" id="subuh">--:--</div>
            </div>
            <div class="card-sholat">
                <div class="icon">🌞</div>
                <div class="label">Dzuhur</div>
                <div class="time" id="dzuhur">--:--</div>
            </div>
            <div class="card-sholat">
                <div class="icon">🌤️</div>
                <div class="label">Ashar</div>
                <div class="time" id="ashar">--:--</div>
            </div>
            <div class="card-sholat">
                <div class="icon">🌇</div>
                <div class="label">Maghrib</div>
                <div class="time" id="maghrib">--:--</div>
            </div>
            <div class="card-sholat">
                <div class="icon">🌃</div>
                <div class="label">Isya</div>
                <div class="time" id="isya">--:--</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Satria Farel Cipta Permata. All rights reserved.</p>
        <p><a href="https://linktree-sf.vercel.app" target="_blank">Kunjungi Linktree Saya</a></p>
    </footer>

    <!-- Logic Script -->
    <script>
        // 1. SERVICE WORKER & PUSH NOTIFICATION SETUP
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.register('service.js')
                .then(function(registration) {
                    initialisePush(registration);
                })
                .catch(function(error) {
                    console.error('Registrasi SW gagal:', error);
                });
        }

        function initialisePush(registration) {
            if (Notification.permission === 'denied') {
                console.warn('Izin notifikasi diblokir oleh pengguna.');
                return;
            }

            registration.pushManager.getSubscription()
                .then(function(subscription) {
                    if (subscription) return subscription;
                    
                    const vapidPublicKey = 'BFu1SBHdqgMrmmk-HbAb6BTALVh2HMG8LYWs3FYDcHolS20KlHV8vfxpMYvGw5M9T5Ac_DLm4YWGsNYNY8kfFS4';
                    const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);
                    
                    return registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: convertedVapidKey
                    });
                })
                .catch(function(error) {
                    console.warn('Push subscription tidak aktif:', error.message);
                });
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        function showNotification(title, message) {
            if (Notification.permission === "granted") {
                navigator.serviceWorker.getRegistration().then(function(registration) {
                    if (registration) {
                        registration.showNotification(title, {
                            body: message,
                            icon: "https://img.icons8.com/fluency/96/ffffff/mosque.png",
                            data: { url: window.location.href }
                        });
                        return;
                    }
                });
            } else {
                alert(title + "\n" + message);
            }
        }

        // 2. FETCH JADWAL & UPDATE CLOCK (Locked to Asia/Jakarta)
        async function getPrayerTimes() {
            try {
                const response = await fetch("jadwal.php");
                const data = await response.json();

                document.getElementById("imsak").textContent = data.imsak;
                document.getElementById("subuh").textContent = data.subuh;
                document.getElementById("dzuhur").textContent = data.dzuhur;
                document.getElementById("ashar").textContent = data.ashar;
                document.getElementById("maghrib").textContent = data.maghrib;
                document.getElementById("isya").textContent = data.isya;

                setInterval(() => checkPrayerTime(data.imsak, data.subuh, data.maghrib), 1000);
            } catch (error) {
                console.error("Gagal mengambil jadwal shalat:", error);
            }
        }

        function timeToMinutes(timeStr) {
            if (!timeStr) return 0;
            const parts = timeStr.split(":");
            return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
        }

        function checkPrayerTime(imsak, subuh, maghrib) {
            const now = new Date();
            // Ambil jam & menit zona Jakarta
            const timeOptions = { timeZone: 'Asia/Jakarta', hour12: false, hour: '2-digit', minute: '2-digit' };
            const timeParts = new Intl.DateTimeFormat('id-ID', timeOptions).formatToParts(now);
            
            let hours = '00', minutes = '00';
            timeParts.forEach(p => {
                if (p.type === 'hour') hours = p.value;
                if (p.type === 'minute') minutes = p.value;
            });

            const currentTime = `${hours}:${minutes}`;
            const currentMinutes = parseInt(hours, 10) * 60 + parseInt(minutes, 10);

            const imsakMinutes = timeToMinutes(imsak);
            if (Math.abs(currentMinutes - imsakMinutes) <= 10) {
                showNotification("Waktu Imsak", "Sahur telah berakhir. Waktu imsak telah tiba.");
            }

            if (currentTime === subuh) {
                showNotification("Waktunya Sholat Subuh", "Saatnya melaksanakan sholat subuh.");
            }

            if (currentTime === maghrib) {
                showNotification("Selamat Berbuka!", "Saatnya berbuka puasa 🍽️");
            }
        }

        function updateClock() {
            const now = new Date();
            // Konversi tampilan jam ke WIB (Asia/Jakarta)
            const options = {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            const timeString = new Intl.DateTimeFormat('id-ID', options).format(now);
            document.getElementById("clock").textContent = timeString.replace(/\./g, ':');
        }

        // 3. INIT
        getPrayerTimes();
        updateClock();
        setInterval(updateClock, 1000);

        const isIdulFitri = <?php echo $isIdulFitri ? 'true' : 'false'; ?>;
        if (isIdulFitri) {
            showNotification("Selamat Idul Fitri!", "Mohon maaf lahir dan batin.");
        }
    </script>
</body>
</html>