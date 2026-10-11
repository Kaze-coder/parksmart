<?php
session_start();
require 'php/database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$ticket_data = null;
$error_msg = null;

// Sertakan file tarif
require_once 'php/config/tarif.php';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    
    // Find ticket by code or plat nomor that is still parked
    $query = "SELECT * FROM tickets WHERE (ticket_code = '$search' OR plat_nomor = '$search') AND status = 'parkir' ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $ticket_data = mysqli_fetch_assoc($result);
        
        // Calculate dynamic tariff using centralized config
        $jam_keluar = time(); // current time
        $kalkulasi = hitungTarifParkir($ticket_data['jam_masuk'], $jam_keluar, $ticket_data['jenis_kendaraan']);
        
        $ticket_data['jam_keluar_display'] = date('Y-m-d H:i:s', $jam_keluar);
        $ticket_data['durasi_asli'] = $kalkulasi['durasi_asli'];
        $ticket_data['durasi_display'] = $kalkulasi['total_jam_pembulatan'] . " Jam";
        $ticket_data['total_biaya'] = $kalkulasi['total_biaya'];
        $ticket_data['teks_rumus'] = $kalkulasi['teks_rumus'];
        
    } else {
        $error_msg = "Tiket aktif tidak ditemukan untuk pencarian: " . htmlspecialchars($search);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Gate Out - ParkSmart</title>
    <?php include 'components/layout/head.php'; ?>
</head>
<body class="bg-surface text-on-surface font-body-md min-h-screen flex">
    
    <?php include 'components/layout/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include 'components/layout/header.php'; ?>

        <main class="flex-1 p-md md:p-xl bg-surface-container-low overflow-y-auto">
            <div class="max-w-4xl mx-auto space-y-lg mt-md">
                <!-- Page Header -->
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Keluar Kendaraan</h2>
                    <p class="font-body-lg text-body-lg text-on-surface-variant mt-xs">Proses pembayaran dan pembukaan palang otomatis.</p>
                </div>
                
                <!-- Input Area -->
                <form action="" method="GET" class="bg-surface p-md rounded-lg shadow-sm border border-outline-variant/30 flex flex-col gap-xs">
                    <label class="block font-label-sm text-label-sm text-on-surface-variant" for="ticket-input">Scan Barcode atau Input Plat Nomor</label>
                    <div class="flex flex-col md:flex-row gap-sm items-stretch">
                        <div class="relative flex-1">
                            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-outline">barcode_scanner</span>
                            <input autocomplete="off" autofocus class="w-full h-full pl-xl pr-sm py-sm border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-body-md bg-surface text-on-surface placeholder:text-outline-variant transition-colors" id="ticket-input" name="search" placeholder="Contoh: B 1234 XYZ atau Scan Tiket" type="text" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" required/>
                        </div>
                        <button type="button" onclick="startCameraScanner()" class="w-full md:w-auto px-md py-sm bg-surface-container text-on-surface rounded-lg font-label-bold text-label-bold hover:bg-surface-container-high transition-colors shadow-sm flex items-center justify-center gap-xs border border-outline-variant" title="Scan via Kamera Laptop/HP">
                            <span class="material-symbols-outlined">photo_camera</span> Scan
                        </button>
                        <button type="submit" class="w-full md:w-auto px-lg py-sm bg-primary text-on-primary rounded-lg font-label-bold text-label-bold hover:bg-primary-container transition-colors shadow-sm flex items-center justify-center gap-xs">
                            <span class="material-symbols-outlined">search</span> Cari
                        </button>
                    </div>
                </form>

                <?php if($error_msg): ?>
                    <div class="p-sm bg-error-container text-on-error-container rounded-lg flex items-center gap-sm">
                        <span class="material-symbols-outlined">error</span>
                        <p class="font-body-sm"><?= $error_msg ?></p>
                    </div>
                <?php endif; ?>

                <!-- Vehicle Summary & Payment Section -->
                <?php if($ticket_data): ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-md">
                    <!-- Ticket Details Card -->
                    <div class="lg:col-span-2 bg-surface rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
                        <div class="bg-surface-container-lowest px-md py-sm border-b border-outline-variant/30 flex justify-between items-center">
                            <h3 class="font-headline-md text-headline-md text-on-surface">Detail Parkir</h3>
                            <span class="bg-secondary-container text-on-secondary-container px-sm py-xs rounded-full font-label-sm text-label-sm font-bold flex items-center gap-xs capitalize">
                                <span class="material-symbols-outlined text-[16px]"><?= $ticket_data['jenis_kendaraan'] == 'mobil' ? 'directions_car' : 'two_wheeler' ?></span> <?= $ticket_data['jenis_kendaraan'] ?>
                            </span>
                        </div>
                        <div class="p-md space-y-md">
                            <div class="flex justify-between items-center bg-surface-container-lowest p-sm rounded-lg border border-outline-variant/20">
                                <span class="font-body-md text-on-surface-variant">Plat Nomor</span>
                                <span class="font-headline-md text-headline-md text-on-surface font-bold"><?= $ticket_data['plat_nomor'] ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-md">
                                <div class="space-y-xs">
                                    <span class="block font-label-sm text-label-sm text-on-surface-variant flex items-center gap-xs">
                                        <span class="material-symbols-outlined text-[16px]">login</span> Jam Masuk
                                    </span>
                                    <span class="block font-body-md text-on-surface font-medium"><?= date('H:i WIB', strtotime($ticket_data['jam_masuk'])) ?></span>
                                    <span class="block font-body-sm text-on-surface-variant"><?= date('d M Y', strtotime($ticket_data['jam_masuk'])) ?></span>
                                </div>
                                <div class="space-y-xs">
                                    <span class="block font-label-sm text-label-sm text-on-surface-variant flex items-center gap-xs">
                                        <span class="material-symbols-outlined text-[16px]">logout</span> Jam Keluar
                                    </span>
                                    <span class="block font-body-md text-on-surface font-medium"><?= date('H:i WIB', strtotime($ticket_data['jam_keluar_display'])) ?></span>
                                    <span class="block font-body-sm text-on-surface-variant"><?= date('d M Y', strtotime($ticket_data['jam_keluar_display'])) ?></span>
                                </div>
                            </div>
                            <div class="pt-sm border-t border-outline-variant/20">
                                <h4 class="font-label-lg font-bold text-on-surface mb-xs">Rincian Tarif Progresif</h4>
                                <div class="space-y-xs text-[13px]">
                                    <div class="flex justify-between items-center text-on-surface-variant">
                                        <span>Durasi Parkir</span>
                                        <span class="font-medium"><?= $ticket_data['durasi_asli'] ?></span>
                                    </div>
                                    <div class="flex justify-between items-center text-on-surface-variant">
                                        <span>Pembulatan</span>
                                        <span class="font-medium text-primary"><?= $ticket_data['durasi_display'] ?></span>
                                    </div>
                                    <div class="flex justify-between items-center text-on-surface-variant pt-xs border-t border-dashed border-outline-variant/30">
                                        <span>Rumus Tarif</span>
                                        <span class="font-medium text-right max-w-[200px] leading-tight"><?= $ticket_data['teks_rumus'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Billing & Actions Card -->
                    <div class="bg-surface rounded-xl shadow-sm border border-outline-variant/30 flex flex-col justify-between overflow-hidden">
                        <div class="p-md bg-primary-fixed/20">
                            <span class="block font-label-sm text-label-sm text-on-surface-variant mb-xs text-center uppercase tracking-wider">Total Tagihan</span>
                            <div class="text-center font-headline-xl text-headline-xl text-primary font-bold">
                                Rp <?= number_format($ticket_data['total_biaya'], 0, ',', '.') ?>
                            </div>
                            <p class="text-center font-body-sm text-on-surface-variant mt-xs">Termasuk Pajak</p>
                        </div>
                        <div class="p-md space-y-sm flex-1 flex flex-col justify-end">
                            <form action="php/proses/checkout_proses.php" method="POST" id="checkoutForm">
                                <input type="hidden" name="ticket_id" value="<?= $ticket_data['id'] ?>">
                                <input type="hidden" name="ticket_code" value="<?= $ticket_data['ticket_code'] ?>">
                                <input type="hidden" name="jam_keluar" value="<?= $ticket_data['jam_keluar_display'] ?>">
                                <input type="hidden" name="total_biaya" value="<?= $ticket_data['total_biaya'] ?>">
                                
                                <button type="button" onclick="confirmCheckout()" class="w-full bg-primary text-on-primary py-md rounded-lg font-headline-md text-[18px] hover:bg-primary-container transition-all active:scale-[0.98] shadow-md flex items-center justify-center gap-sm">
                                    <span class="material-symbols-outlined">payments</span>
                                    Bayar &amp; Buka Palang
                                </button>
                            </form>
                            <!-- Print Struk -->
                            <button type="button" onclick="window.open('cetak_tiket.php?id=<?= $ticket_data['ticket_code'] ?>&preview_keluar=1', '_blank')" class="w-full bg-surface border border-outline text-on-surface py-sm rounded-lg font-label-bold text-label-bold hover:bg-surface-container-low transition-colors flex items-center justify-center gap-sm mt-sm">
                                <span class="material-symbols-outlined">receipt_long</span>
                                Print Struk Keluar
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Camera Scanner Modal -->
    <div id="scannerModal" class="fixed inset-0 z-[100] bg-black/50 hidden flex items-center justify-center p-md backdrop-blur-sm transition-opacity">
        <div class="bg-surface-container-lowest w-full max-w-md rounded-xl shadow-lg border border-outline-variant overflow-hidden flex flex-col">
            <div class="px-lg py-md border-b border-surface-variant flex justify-between items-center bg-surface">
                <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-xs"><span class="material-symbols-outlined">barcode_scanner</span> Scan Karcis</h3>
                <button type="button" onclick="stopCameraScanner()" class="text-on-surface-variant hover:text-error transition-colors p-xs rounded-full hover:bg-error-container">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-md">
                <div id="reader" class="w-full rounded-lg overflow-hidden border border-outline-variant bg-black min-h-[250px]"></div>
                <p class="text-center font-body-sm text-on-surface-variant mt-sm">Arahkan barcode karcis masuk ke kamera.</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>

    <!-- Micro-interaction Script -->
    <script>
        // Camera Scanner Logic
        let html5QrCode;

        function startCameraScanner() {
            document.getElementById('scannerModal').classList.remove('hidden');
            html5QrCode = new Html5Qrcode("reader");
            
            // Konfigurasi scan dioptimalkan untuk Barcode 1D yang panjang
            const config = { 
                fps: 20, // Tingkatkan FPS agar lebih responsif
                // qrbox dihapus agar kamera memindai seluruh frame (sangat membantu barcode panjang)
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true // Gunakan Native API browser jika ada (jauh lebih cepat)
                }
            };
            
            // Prioritaskan kamera belakang (environment) jika di HP
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Akses Kamera',
                    text: 'Pastikan Anda memberikan izin akses kamera ke browser.',
                    confirmButtonColor: '#00236f'
                });
                stopCameraScanner();
            });
        }

        function stopCameraScanner() {
            document.getElementById('scannerModal').classList.add('hidden');
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => {
                    console.log("Error stopping camera", err);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            stopCameraScanner(); // Tutup kamera
            
            // Masukkan hasil teks barcode ke input form
            document.getElementById('ticket-input').value = decodedText;
            
            // Mainkan suara 'beep' (opsional) atau langsung submit
            let form = document.getElementById('ticket-input').closest('form');
            form.submit();
        }

        function confirmCheckout() {
            Swal.fire({
                title: 'Konfirmasi Pembayaran?',
                text: "Pastikan pelanggan sudah membayar sesuai total biaya.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#757682',
                confirmButtonText: 'Ya, Selesaikan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('checkoutForm').submit();
                }
            })
        }

        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const ticketCode = urlParams.get('ticket_code');

        if(status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Checkout Berhasil',
                text: 'Pembayaran telah diselesaikan dan kendaraan dapat keluar.',
                showCancelButton: true,
                confirmButtonColor: '#00236f',
                cancelButtonColor: '#757682',
                confirmButtonText: 'Cetak Struk Keluar',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed && ticketCode) {
                    window.open('cetak_tiket.php?id=' + ticketCode, '_blank');
                }
            });
            
            // Clean url
            window.history.replaceState({}, document.title, "gate_out.php");
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal memproses checkout.',
                confirmButtonColor: '#00236f'
            });
        }
    </script>
</body>
</html>
