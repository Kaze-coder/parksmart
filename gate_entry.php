<?php
session_start();
require 'php/database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Get recent entries
$q_recent = mysqli_query($koneksi, "SELECT * FROM tickets WHERE status='parkir' ORDER BY id DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Gate Entry - ParkSmart</title>
    <?php include 'components/layout/head.php'; ?>
</head>
<body class="bg-surface text-on-surface font-body-md min-h-screen flex">
    
    <?php include 'components/layout/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include 'components/layout/header.php'; ?>

        <main class="flex-1 p-md md:p-xl overflow-y-auto">
            <div class="max-w-container-max mx-auto grid grid-cols-1 lg:grid-cols-12 gap-lg">
                
                <!-- Left Column: Primary Data Entry (8 cols) -->
                <section class="lg:col-span-7 xl:col-span-8 flex flex-col gap-md">
                    <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant soft-shadow h-full flex flex-col justify-between">
                        <form action="php/proses/entry_proses.php" method="POST" id="entryForm" class="flex flex-col h-full">
                            <div>
                                <h2 class="font-headline-lg text-headline-lg text-primary mb-xs">Entri Kendaraan</h2>
                                <p class="font-body-md text-body-md text-on-surface-variant mb-lg">Silakan masukkan nomor plat untuk mencetak tiket parkir baru.</p>
                                
                                <!-- Input Plat Nomor -->
                                <div class="mb-xl">
                                    <label class="block font-label-bold text-label-bold text-on-surface mb-sm uppercase tracking-wider" for="plate-number">Nomor Plat Kendaraan</label>
                                    <input autocomplete="off" autofocus class="w-full bg-surface-container-low border-2 border-outline-variant rounded-lg p-md font-headline-xl text-headline-xl text-center tracking-widest text-on-surface focus:border-primary focus:ring-0 outline-none transition-all duration-200 uppercase" id="plate-number" name="plat_nomor" placeholder="B 1234 XY" required type="text" maxlength="12"/>
                                </div>
                                
                                <!-- Toggle Jenis Kendaraan -->
                                <div class="mb-xl">
                                    <span class="block font-label-bold text-label-bold text-on-surface mb-sm uppercase tracking-wider">Jenis Kendaraan</span>
                                    <div class="grid grid-cols-2 gap-md">
                                        <!-- Selected State (Mobil) -->
                                        <label class="cursor-pointer relative">
                                            <input checked class="peer sr-only" name="jenis_kendaraan" type="radio" value="mobil"/>
                                            <div class="flex flex-col items-center justify-center p-md rounded-lg border border-outline-variant bg-surface text-on-surface-variant peer-checked:border-2 peer-checked:border-primary peer-checked:bg-secondary-container peer-checked:text-primary transition-all hover:bg-surface-container-high duration-200 label-card">
                                                <span class="material-symbols-outlined text-4xl mb-xs icon-unfilled">directions_car</span>
                                                <span class="font-headline-md text-headline-md">Mobil</span>
                                            </div>
                                        </label>
                                        
                                        <!-- Unselected State (Motor) -->
                                        <label class="cursor-pointer relative">
                                            <input class="peer sr-only" name="jenis_kendaraan" type="radio" value="motor"/>
                                            <div class="flex flex-col items-center justify-center p-md rounded-lg border border-outline-variant bg-surface text-on-surface-variant peer-checked:border-2 peer-checked:border-primary peer-checked:bg-secondary-container peer-checked:text-primary transition-all hover:bg-surface-container-high duration-200 label-card">
                                                <span class="material-symbols-outlined text-4xl mb-xs icon-unfilled">two_wheeler</span>
                                                <span class="font-headline-md text-headline-md">Motor</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-primary text-on-primary py-md rounded-lg font-headline-md text-headline-md hover:bg-primary-container hover:text-on-primary-container hover:shadow-lg active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-sm mt-auto">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">print</span>
                                Cetak Tiket
                            </button>
                        </form>
                    </div>
                </section>

                <!-- Right Column: Recent Activity (4 cols) -->
                <section class="lg:col-span-5 xl:col-span-4 flex flex-col gap-md">
                    <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant soft-shadow h-full">
                        <div class="flex justify-between items-center mb-lg pb-sm border-b border-outline-variant">
                            <h3 class="font-headline-md text-headline-md text-primary">Entri Terakhir</h3>
                            <button onclick="location.reload()" class="text-primary hover:bg-surface-container p-xs rounded-full transition-colors flex items-center justify-center" title="Refresh">
                                <span class="material-symbols-outlined">refresh</span>
                            </button>
                        </div>
                        
                        <!-- Activity List -->
                        <div class="flex flex-col gap-sm">
                            <?php while($act = mysqli_fetch_assoc($q_recent)): ?>
                            <div class="flex items-center justify-between p-sm rounded-lg hover:bg-surface-container-low transition-colors group cursor-default">
                                <div class="flex items-center gap-md">
                                    <?php if($act['jenis_kendaraan'] == 'mobil'): ?>
                                    <div class="w-10 h-10 rounded-full bg-secondary-fixed flex items-center justify-center text-primary-fixed-dim">
                                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">directions_car</span>
                                    </div>
                                    <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant">
                                        <span class="material-symbols-outlined">two_wheeler</span>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-label-bold text-label-bold text-on-surface"><?= $act['plat_nomor'] ?></p>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">Hari ini, <?= date('H:i', strtotime($act['jam_masuk'])) ?></p>
                                    </div>
                                </div>
                                <span class="px-sm py-xs rounded-full bg-surface-variant text-on-surface-variant font-label-sm text-label-sm"><?= ucfirst($act['jenis_kendaraan']) ?></span>
                            </div>
                            <?php endwhile; ?>
                            <?php if(mysqli_num_rows($q_recent) == 0): ?>
                                <p class="text-center text-on-surface-variant py-sm">Belum ada entri kendaraan.</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-md text-center">
                            <a class="font-label-bold text-label-bold text-primary hover:underline" href="history.php">Lihat Semua Riwayat</a>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <!-- Micro-interaction Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const plateInput = document.getElementById('plate-number');
            if(plateInput) {
                plateInput.addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                });
            }

            const entryForm = document.getElementById('entryForm');
            if (entryForm) {
                entryForm.addEventListener('submit', function(e) {
                    const plat = document.getElementById('plate-number').value.trim();
                    // Regex validasi plat nomor Indonesia: 1-2 huruf, spasi opsional, 1-4 angka, spasi opsional, 1-4 huruf
                    const regex = /^[A-Z]{1,2}\s?\d{1,4}\s?[A-Z]{1,4}$/;
                    
                    if (!regex.test(plat)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Format Tidak Sesuai',
                            text: 'Format plat nomor harus valid (Contoh: B 1234 XY). Maksimal 12 karakter.',
                            confirmButtonColor: '#ba1a1a',
                            background: '#faf8ff',
                            color: '#1a1b21'
                        });
                    }
                });
            }

            const radios = document.querySelectorAll('input[name="jenis_kendaraan"]');
            radios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    radios.forEach(r => {
                        const icon = r.nextElementSibling.querySelector('.material-symbols-outlined');
                        if(r.checked) {
                            icon.style.fontVariationSettings = "'FILL' 1";
                        } else {
                            icon.style.fontVariationSettings = "'FILL' 0";
                        }
                    });
                });
            });

            // Initial state for filled icon
            const checkedRadio = document.querySelector('input[name="jenis_kendaraan"]:checked');
            if(checkedRadio) {
                checkedRadio.nextElementSibling.querySelector('.material-symbols-outlined').style.fontVariationSettings = "'FILL' 1";
            }
        });

        // Handle alerts
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const print = urlParams.get('print');

        if(status === 'full') {
            Swal.fire({
                icon: 'error',
                title: 'Parkir Penuh',
                text: 'Kapasitas parkir untuk jenis kendaraan ini sudah penuh!',
                confirmButtonColor: '#00236f'
            });
        } else if (status === 'success' && print) {
            Swal.fire({
                icon: 'success',
                title: 'Tiket Berhasil Dicetak',
                text: 'Nomor Tiket: ' + print,
                confirmButtonColor: '#00236f'
            }).then(() => {
                // Here you would normally trigger a print dialog or redirect to a print page
                window.open('cetak_tiket.php?id=' + print, '_blank');
            });
        }
    </script>
</body>
</html>
