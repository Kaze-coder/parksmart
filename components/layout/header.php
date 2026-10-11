<?php
// Fetch 5 recent tickets for notifications (ordered by latest activity, entry or exit)
$recent_tickets = [];
$notif_timestamps = [];
if (isset($koneksi)) {
    $q_notif = mysqli_query($koneksi, "SELECT id, plat_nomor, status, jam_masuk, jam_keluar, biaya, GREATEST(jam_masuk, IFNULL(jam_keluar, jam_masuk)) as last_activity FROM tickets ORDER BY last_activity DESC LIMIT 5");
    if ($q_notif) {
        while ($row_notif = mysqli_fetch_assoc($q_notif)) {
            $recent_tickets[] = $row_notif;
            $notif_timestamps[] = strtotime($row_notif['last_activity']);
        }
    }
}
?>
<header class="bg-surface text-primary docked full-width top-0 sticky z-40 border-b border-outline-variant shadow-sm flex justify-between items-center h-16 px-xl">
    <!-- Mobile Brand / Menu Area -->
    <div class="flex items-center gap-md">
        <button class="md:hidden text-on-surface-variant p-xs hover:bg-surface-container-high rounded-full">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <h2 class="font-headline-md text-headline-md font-bold text-primary md:hidden">ParkSmart</h2>
        
        <!-- Global Search Bar (Only on history.php) -->
        <?php if(basename($_SERVER['PHP_SELF']) == 'history.php'): ?>
        <div class="relative w-full max-w-sm hidden md:block">
            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <input id="globalSearchInput" class="w-full bg-surface border border-outline-variant text-on-surface rounded-full py-[10px] pl-[40px] pr-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-sm text-body-sm" placeholder="Cari ID Tiket atau Plat Nomor..." type="text"/>
        </div>
        <?php endif; ?>
    </div>

    <!-- Trailing Actions -->
    <div class="flex items-center gap-sm md:gap-md">
        <!-- Action Icons -->
        <div class="flex items-center gap-xs">
            
            <!-- Notification Dropdown -->
            <div class="relative">
                <button onclick="toggleDropdown('notifDropdownMenu')" id="notifDropdownBtn" class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-full cursor-pointer active:scale-95 transition-all relative">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <?php if(count($recent_tickets) > 0): ?>
                    <span id="notifDot" class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full ring-2 ring-surface hidden"></span>
                    <?php endif; ?>
                </button>
                
                <div id="notifDropdownMenu" class="absolute right-0 mt-2 w-80 bg-surface border border-outline-variant rounded-xl shadow-lg opacity-0 invisible translate-y-[-10px] transition-all duration-200 z-50 overflow-hidden header-dropdown">
                    <div class="px-md py-sm border-b border-surface-variant flex justify-between items-center bg-surface-container-lowest">
                        <h4 class="font-label-bold text-label-bold text-on-surface">Notifikasi Terbaru</h4>
                        <span id="notifBadge" class="text-[11px] font-bold text-white bg-primary px-2 py-0.5 rounded-full hidden">0 Baru</span>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <?php if(count($recent_tickets) > 0): ?>
                            <?php foreach($recent_tickets as $nticket): ?>
                                <div class="px-md py-sm border-b border-surface-variant last:border-0 hover:bg-surface-container-low transition-colors cursor-pointer">
                                    <div class="flex justify-between items-start mb-xs">
                                        <span class="font-label-bold text-label-bold text-on-surface flex items-center gap-xs">
                                            <?php if($nticket['status'] == 'parkir'): ?>
                                                <span class="material-symbols-outlined text-[16px] text-[#10b981]">login</span>
                                                Masuk: <?= htmlspecialchars($nticket['plat_nomor']) ?>
                                            <?php else: ?>
                                                <span class="material-symbols-outlined text-[16px] text-error">logout</span>
                                                Keluar: <?= htmlspecialchars($nticket['plat_nomor']) ?>
                                            <?php endif; ?>
                                        </span>
                                        <span class="text-[10px] text-on-surface-variant">
                                            <?php 
                                            if($nticket['status'] == 'parkir') {
                                                echo date('H:i', strtotime($nticket['jam_masuk']));
                                            } else {
                                                echo date('H:i', strtotime($nticket['jam_keluar']));
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <?php if($nticket['status'] == 'selesai'): ?>
                                        <p class="font-body-sm text-[12px] text-on-surface-variant">Pendapatan: Rp <?= number_format((int)$nticket['biaya'], 0, ',', '.') ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-md text-center text-on-surface-variant font-body-sm">Belum ada aktivitas.</div>
                        <?php endif; ?>
                    </div>
                    <a href="dashboard.php" class="block text-center py-xs bg-surface-container-lowest border-t border-surface-variant text-primary font-label-sm hover:underline">Lihat Semua di Dasbor</a>
                </div>
            </div>

            <!-- Help Dropdown -->
            <div class="relative">
                <button onclick="toggleDropdown('helpDropdownMenu')" id="helpDropdownBtn" class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-full cursor-pointer active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-[22px]">help</span>
                </button>
                
                <div id="helpDropdownMenu" class="absolute right-0 mt-2 w-64 bg-surface border border-outline-variant rounded-xl shadow-lg opacity-0 invisible translate-y-[-10px] transition-all duration-200 z-50 overflow-hidden header-dropdown">
                    <div class="px-md py-sm border-b border-surface-variant bg-surface-container-lowest">
                        <h4 class="font-label-bold text-label-bold text-on-surface">Pusat Bantuan</h4>
                    </div>
                    <div class="py-xs">
                        <a href="#" onclick="showGuide('entry', event)" class="flex items-center gap-sm px-md py-sm text-on-surface hover:bg-surface-container-low transition-colors font-body-sm text-body-sm">
                            <span class="material-symbols-outlined text-[18px] text-secondary">input</span>
                            Panduan Gate Entry
                        </a>
                        <a href="#" onclick="showGuide('exit', event)" class="flex items-center gap-sm px-md py-sm text-on-surface hover:bg-surface-container-low transition-colors font-body-sm text-body-sm">
                            <span class="material-symbols-outlined text-[18px] text-secondary">output</span>
                            Panduan Gate Exit
                        </a>
                        <a href="settings.php" class="flex items-center gap-sm px-md py-sm text-on-surface hover:bg-surface-container-low transition-colors font-body-sm text-body-sm">
                            <span class="material-symbols-outlined text-[18px] text-secondary">tune</span>
                            Konfigurasi Sistem
                        </a>
                        <div class="h-px bg-surface-variant my-xs"></div>
                        <div class="px-md py-sm text-on-surface-variant font-label-sm text-[11px] flex justify-between items-center">
                            <span>ParkSmart System</span>
                            <span class="bg-surface-container-high px-1.5 py-0.5 rounded">v2.1.0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Vertical Divider -->
        <div class="hidden md:block w-px h-8 bg-outline-variant opacity-50 mx-xs"></div>
        
        <!-- Profile Dropdown Section -->
        <div class="relative">
            <!-- Profile Button -->
            <button onclick="toggleProfileDropdown()" id="profileDropdownBtn" class="flex items-center gap-sm p-1 pr-3 rounded-full border border-outline-variant bg-surface-container-low hover:bg-surface-container-high transition-all cursor-pointer active:scale-95 shadow-sm">
                <!-- Avatar -->
                <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center border border-outline-variant shadow-sm overflow-hidden shrink-0">
                    <?php if (!empty($_SESSION['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile Picture" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="material-symbols-outlined text-on-primary-container text-[20px]">person</span>
                    <?php endif; ?>
                </div>
                
                <!-- User Info -->
                <div class="hidden md:flex flex-col items-start text-left">
                    <span class="font-label-bold text-label-bold text-on-surface leading-tight"><?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></span>
                    <span class="font-label-sm text-[11px] text-on-surface-variant leading-tight">Administrator</span>
                </div>
                
                <!-- Dropdown Icon -->
                <span class="material-symbols-outlined text-on-surface-variant text-[20px] hidden md:block transition-transform duration-200" id="profileDropdownIcon">expand_more</span>
            </button>

            <!-- Dropdown Menu -->
            <div id="profileDropdownMenu" class="absolute right-0 mt-2 w-48 bg-surface border border-outline-variant rounded-xl shadow-lg opacity-0 invisible translate-y-[-10px] transition-all duration-200 z-50 overflow-hidden">
                <div class="py-xs">
                    <a href="settings.php" class="flex items-center gap-sm px-md py-sm text-on-surface hover:bg-surface-container-low transition-colors font-body-sm text-body-sm">
                        <span class="material-symbols-outlined text-[20px] text-primary">settings</span>
                        Settings
                    </a>
                    <div class="h-px bg-surface-variant my-xs"></div>
                    <a href="#" onclick="confirmLogout(event)" class="flex items-center gap-sm px-md py-sm text-error hover:bg-error-container transition-colors font-body-sm text-body-sm">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Notification read state logic
    document.addEventListener('DOMContentLoaded', function() {
        const timestamps = <?= json_encode($notif_timestamps) ?>;
        const lastSeenActivity = parseInt(localStorage.getItem('lastSeenActivity') || '0');
        const notifDot = document.getElementById('notifDot');
        const notifBadge = document.getElementById('notifBadge');
        
        let newCount = 0;
        timestamps.forEach(ts => {
            if (ts > lastSeenActivity) {
                newCount++;
            }
        });

        if (newCount > 0) {
            if (notifDot) notifDot.classList.remove('hidden');
            if (notifBadge) {
                notifBadge.textContent = newCount + ' Baru';
                notifBadge.classList.remove('hidden');
                notifBadge.classList.add('bg-primary', 'text-white');
                notifBadge.classList.remove('bg-surface-variant', 'text-on-surface-variant');
            }
        } else {
            if (notifBadge) {
                notifBadge.textContent = '0 Baru';
                notifBadge.classList.remove('hidden');
                notifBadge.classList.add('bg-surface-variant', 'text-on-surface-variant');
                notifBadge.classList.remove('bg-primary', 'text-white');
            }
        }
    });

    function showGuide(type, event) {
        event.preventDefault();
        // Close dropdown
        document.getElementById('helpDropdownMenu').classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
        document.getElementById('helpDropdownMenu').classList.remove('opacity-100', 'translate-y-0');
        
        let title = '';
        let htmlContent = '';
        
        if (type === 'entry') {
            title = 'Panduan Gate Entry';
            htmlContent = `<div style="text-align: left; font-size: 14px; line-height: 1.6; color: #44474e;">
                <p style="margin-bottom: 8px;"><strong>1. Plat Nomor:</strong> Ketik plat nomor kendaraan pelanggan (tanpa spasi lebih disarankan).</p>
                <p style="margin-bottom: 8px;"><strong>2. Jenis Kendaraan:</strong> Pastikan Anda memilih ikon Mobil atau Motor.</p>
                <p style="margin-bottom: 8px;"><strong>3. Cetak:</strong> Klik <b>Cetak Tiket</b>. Karcis akan tercetak secara otomatis (bisa ditekan Ctrl+P jika preview muncul).</p>
                <p><strong>Tips:</strong> Data sudah masuk ke database sesaat setelah tombol ditekan.</p>
            </div>`;
        } else if (type === 'exit') {
            title = 'Panduan Gate Exit';
            htmlContent = `<div style="text-align: left; font-size: 14px; line-height: 1.6; color: #44474e;">
                <p style="margin-bottom: 8px;"><strong>1. Pencarian:</strong> Ketik Nomor Plat <b>ATAU</b> ID Tiket pelanggan.</p>
                <p style="margin-bottom: 8px;"><strong>2. Scanner:</strong> Anda juga bisa menyorot kursor ke kolom pencarian lalu men-scan barcode di karcis.</p>
                <p style="margin-bottom: 8px;"><strong>3. Tagihan:</strong> Sistem akan otomatis memunculkan durasi parkir dan total biaya yang harus dibayar.</p>
                <p><strong>4. Selesai:</strong> Terima uang pelanggan, lalu klik <b>Selesaikan Transaksi</b>.</p>
            </div>`;
        }
        
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                html: htmlContent,
                icon: 'info',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#004A77',
                background: '#faf8ff',
                color: '#1a1b21',
                customClass: {
                    htmlContainer: 'text-left'
                }
            });
        }
    }
    function toggleDropdown(menuId) {
        const menu = document.getElementById(menuId);
        const isProfile = menuId === 'profileDropdownMenu';
        const icon = isProfile ? document.getElementById('profileDropdownIcon') : null;
        
        // Close all other dropdowns
        const allDropdowns = document.querySelectorAll('.header-dropdown, #profileDropdownMenu');
        allDropdowns.forEach(d => {
            if (d.id !== menuId && !d.classList.contains('invisible')) {
                d.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
                d.classList.remove('opacity-100', 'translate-y-0');
                if (d.id === 'profileDropdownMenu') {
                    document.getElementById('profileDropdownIcon').classList.remove('rotate-180');
                }
            }
        });
        
        if (menu.classList.contains('invisible')) {
            // Show
            menu.classList.remove('invisible', 'opacity-0', 'translate-y-[-10px]');
            menu.classList.add('opacity-100', 'translate-y-0');
            if(icon) icon.classList.add('rotate-180');
            
            // Mark notif as read when opened
            if (menuId === 'notifDropdownMenu') {
                const latestActivity = <?= isset($notif_timestamps[0]) ? $notif_timestamps[0] : 0 ?>;
                localStorage.setItem('lastSeenActivity', latestActivity);
                
                const notifDot = document.getElementById('notifDot');
                if (notifDot) notifDot.classList.add('hidden');
            }
        } else {
            // Hide
            menu.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
            menu.classList.remove('opacity-100', 'translate-y-0');
            if(icon) icon.classList.remove('rotate-180');
        }
    }

    // Keep original wrapper for backward compatibility if any button calls it
    function toggleProfileDropdown() {
        toggleDropdown('profileDropdownMenu');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const profileBtn = document.getElementById('profileDropdownBtn');
        const notifBtn = document.getElementById('notifDropdownBtn');
        const helpBtn = document.getElementById('helpDropdownBtn');
        
        const profileMenu = document.getElementById('profileDropdownMenu');
        const notifMenu = document.getElementById('notifDropdownMenu');
        const helpMenu = document.getElementById('helpDropdownMenu');
        
        if (profileMenu && !profileMenu.classList.contains('invisible') && !profileBtn.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
            profileMenu.classList.remove('opacity-100', 'translate-y-0');
            document.getElementById('profileDropdownIcon').classList.remove('rotate-180');
        }
        
        if (notifMenu && !notifMenu.classList.contains('invisible') && !notifBtn.contains(event.target) && !notifMenu.contains(event.target)) {
            notifMenu.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
            notifMenu.classList.remove('opacity-100', 'translate-y-0');
        }

        if (helpMenu && !helpMenu.classList.contains('invisible') && !helpBtn.contains(event.target) && !helpMenu.contains(event.target)) {
            helpMenu.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]');
            helpMenu.classList.remove('opacity-100', 'translate-y-0');
        }
    });

    function confirmLogout(e) {
        e.preventDefault();
        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Anda yakin ingin keluar dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba1a1a',
                cancelButtonColor: '#757682',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                background: '#faf8ff',
                color: '#1a1b21'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        } else {
            if(confirm("Anda yakin ingin keluar?")) {
                window.location.href = 'logout.php';
            }
        }
    }
</script>
