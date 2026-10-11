<?php
session_start();
require 'php/database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM tickets ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Tiket - ParkSmart</title>
    <?php include 'components/layout/head.php'; ?>
    <style>
        /* Force Table Styles to Override DataTables Defaults */
        table.dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
            margin: 0 !important;
        }
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
        table.dataTable thead th, table.dataTable thead td {
            border-bottom: none !important;
        }
        
        /* Remove excessive margins and padding injected by style.css and DataTables */
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Pagination Styling to match Bento-grid design */
        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 1px solid #c5c5d3 !important; /* outline-variant */
            border-radius: 4px !important;
            background: #ffffff !important;
            color: #444651 !important;
            margin: 0 !important;
            cursor: pointer;
            font-size: 14px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #00236f !important; /* primary */
            color: #ffffff !important;
            border-color: #00236f !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #f4f3fa !important;
            color: #1a1b21 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.5;
            cursor: default;
            background: #ffffff !important;
            color: #444651 !important;
        }
    </style>
</head>
<body class="bg-surface-container-lowest text-on-background font-body-md min-h-screen flex">
    
    <?php include 'components/layout/sidebar.php'; ?>

    <div class="flex-1 flex flex-col md:ml-64 h-full overflow-hidden">
        <?php include 'components/layout/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-md md:p-lg">
            <!-- Header Section -->
            <div class="mb-xl flex flex-col lg:flex-row justify-between items-start lg:items-start gap-md">
                <div class="pt-sm">
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Riwayat Tiket</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Laporan histori masuk dan keluar kendaraan.</p>
                </div>
                <div class="w-full lg:w-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-sm">
                        <input type="date" id="filterDate" class="bg-surface border border-outline-variant rounded-md px-sm py-[10px] text-body-sm font-body-sm text-on-surface focus:outline-none focus:border-primary w-full">
                        <select id="filterJenis" class="bg-surface border border-outline-variant rounded-md pl-sm pr-8 py-[10px] text-body-sm font-body-sm text-on-surface focus:outline-none focus:border-primary w-full">
                            <option>Semua Kendaraan</option>
                            <option>Mobil</option>
                            <option>Motor</option>
                        </select>
                        <button id="btnExport" class="bg-surface border border-outline-variant rounded-md px-md py-[10px] flex items-center justify-center gap-xs font-label-sm text-label-sm hover:bg-surface-container-low transition-colors w-full">
                            <span class="material-symbols-outlined text-[18px]">download</span> Ekspor
                        </button>
                        <button id="btnFilterLanjut" class="bg-surface border border-outline-variant rounded-md px-md py-[10px] flex items-center justify-center gap-xs font-label-sm text-label-sm hover:bg-surface-container-low transition-colors w-full">
                            <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter Lanjut
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-lg shadow-sm overflow-hidden">
                <table id="historyTable" class="w-full text-left border-collapse">
                    <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant">
                                <th class="font-label-bold text-label-bold text-on-surface-variant py-[14px] px-md">ID Tiket</th>
                                <th class="font-label-bold text-label-bold text-on-surface-variant py-[14px] px-md">Plat Nomor</th>
                                <th class="font-label-bold text-label-bold text-on-surface-variant py-[14px] px-md">Jenis</th>
                                <th class="font-label-bold text-label-bold text-on-surface-variant py-[14px] px-md">Jam Masuk</th>
                                <th class="font-label-bold text-label-bold text-on-surface-variant py-[14px] px-md">Jam Keluar</th>
                                <th class="font-label-bold text-label-bold text-on-surface-variant py-[14px] px-md">Durasi</th>
                                <th class="font-label-bold text-label-bold text-on-surface-variant py-[14px] px-md">Total Biaya</th>
                                <th class="font-label-bold text-label-bold text-on-surface-variant py-[14px] px-md">Status</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-sm text-body-sm text-on-surface">
                            <?php while($row = mysqli_fetch_assoc($query)): 
                                $biaya = $row['biaya'] ? 'Rp ' . number_format($row['biaya'], 0, ',', '.') : '-';
                                $jam_masuk = date('H:i:s', strtotime($row['jam_masuk']));
                                $jam_keluar = $row['jam_keluar'] ? date('H:i:s', strtotime($row['jam_keluar'])) : '-';
                                
                                // Calculate Durasi
                                $durasi = '-';
                                if($row['jam_keluar']){
                                    $in = strtotime($row['jam_masuk']);
                                    $out = strtotime($row['jam_keluar']);
                                    $diff = $out - $in;
                                    $hours = floor($diff / 3600);
                                    $minutes = floor(($diff % 3600) / 60);
                                    if($hours > 0) {
                                        $durasi = "{$hours}j {$minutes}m";
                                    } else {
                                        $durasi = "{$minutes}m";
                                    }
                                }
                            ?>
                            <tr class="border-b border-surface-variant hover:bg-surface-container-lowest transition-colors">
                                <td class="py-[14px] px-md text-primary font-label-bold text-label-bold"><?= $row['ticket_code'] ?></td>
                                <td class="py-[14px] px-md font-body-sm text-on-surface"><?= $row['plat_nomor'] ?></td>
                                <td class="py-[14px] px-md capitalize font-body-sm text-on-surface-variant"><?= $row['jenis_kendaraan'] ?></td>
                                <td class="py-[14px] px-md font-body-sm text-on-surface-variant" data-date="<?= date('Y-m-d', strtotime($row['jam_masuk'])) ?>"><?= $jam_masuk ?></td>
                                <td class="py-[14px] px-md font-body-sm text-on-surface-variant"><?= $jam_keluar ?></td>
                                <td class="py-[14px] px-md font-body-sm text-on-surface-variant"><?= $durasi ?></td>
                                <td class="py-[14px] px-md font-body-sm text-on-surface"><?= $biaya ?></td>
                                <td class="py-[14px] px-md">
                                    <?php if($row['status'] == 'selesai'): ?>
                                    <span class="inline-flex items-center px-sm py-[2px] rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm">
                                        Selesai
                                    </span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-sm py-[2px] rounded-full bg-surface-variant text-on-surface-variant font-label-sm text-label-sm border border-outline-variant">
                                        Parkir
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- DataTables & Interactions -->
    <script>
        $(document).ready(function() {
            var table = $('#historyTable').DataTable({
                "dom": '<"overflow-x-auto w-full"t><"flex flex-col md:flex-row justify-between items-center px-md py-[12px] border-t border-outline-variant text-body-sm text-on-surface-variant w-full bg-surface-container-lowest"ip>',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
                    "info": "Menampilkan _START_ - _END_ dari _TOTAL_ tiket",
                    "paginate": {
                        "previous": "<span class='material-symbols-outlined text-[18px]'>chevron_left</span>",
                        "next": "<span class='material-symbols-outlined text-[18px]'>chevron_right</span>"
                    }
                },
                "order": [[ 3, "desc" ]] // Order by Jam Masuk descending by default
            });

            // Filtering by Date
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex, rowData, counter) {
                    let filterDate = $('#filterDate').val();
                    if (!filterDate) return true; // Show all if no date selected
                    
                    // Retrieve date from data-date attribute
                    let rowDate = $(settings.aoData[dataIndex].nTr).find('td:eq(3)').attr('data-date');
                    return filterDate === rowDate;
                }
            );

            $('#filterDate').on('change', function() {
                table.draw();
            });

            // Filtering by Jenis
            $('#filterJenis').on('change', function() {
                let val = $(this).val();
                if(val === 'Semua Kendaraan') {
                    table.column(2).search('').draw();
                } else {
                    table.column(2).search('^' + val + '$', true, false).draw();
                }
            });

            // Global Search (bound to header search input)
            $('#globalSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Filter Lanjut (Reset Filters)
            $('#btnFilterLanjut').on('click', function() {
                $('#globalSearchInput').val('');
                $('#filterDate').val('');
                $('#filterJenis').val('Semua Kendaraan');
                table.search('').columns().search('').draw();
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Filter telah di-reset',
                    showConfirmButton: false,
                    timer: 1500
                });
            });

            // Export to CSV
            $('#btnExport').on('click', function() {
                let date = $('#filterDate').val();
                let jenis = $('#filterJenis').val();
                window.location.href = 'php/proses/export_csv.php?date=' + date + '&jenis=' + jenis;
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Riwayat?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba1a1a',
                cancelButtonColor: '#757682',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'php/proses/delete_ticket.php?id=' + id;
                }
            })
        }

        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('status') === 'deleted') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data riwayat berhasil dihapus.',
                confirmButtonColor: '#00236f'
            });
            window.history.replaceState({}, document.title, "history.php");
        }
    </script>
</body>
</html>
