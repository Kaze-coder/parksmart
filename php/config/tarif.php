<?php
// Konfigurasi Tarif Parkir Progresif Dinamis
require_once __DIR__ . '/../database/koneksi.php';

$q_settings = mysqli_query($koneksi, "SELECT * FROM settings");
$set = [];
while ($row = mysqli_fetch_assoc($q_settings)) {
    $set[$row['setting_key']] = $row['setting_value'];
}

$TARIF_CONFIG = [
    'motor' => [
        'jam_pertama' => isset($set['tarif_motor_1']) ? (int)$set['tarif_motor_1'] : 2000,
        'jam_berikutnya' => isset($set['tarif_motor_next']) ? (int)$set['tarif_motor_next'] : 1000,
        'maksimal_harian' => isset($set['tarif_motor_max']) ? (int)$set['tarif_motor_max'] : 15000
    ],
    'mobil' => [
        'jam_pertama' => isset($set['tarif_mobil_1']) ? (int)$set['tarif_mobil_1'] : 5000,
        'jam_berikutnya' => isset($set['tarif_mobil_next']) ? (int)$set['tarif_mobil_next'] : 2000,
        'maksimal_harian' => isset($set['tarif_mobil_max']) ? (int)$set['tarif_mobil_max'] : 30000
    ]
];

/**
 * Menghitung total biaya parkir dengan sistem tarif progresif dan pembulatan ceil.
 *
 * @param string $waktu_masuk Format: 'Y-m-d H:i:s'
 * @param mixed $waktu_keluar Format: 'Y-m-d H:i:s' atau timestamp int
 * @param string $jenis_kendaraan 'motor' atau 'mobil'
 * @return array
 */
function hitungTarifParkir($waktu_masuk, $waktu_keluar, $jenis_kendaraan) {
    global $TARIF_CONFIG;
    
    // Normalisasi jenis kendaraan
    $jenis = strtolower($jenis_kendaraan);
    if (!isset($TARIF_CONFIG[$jenis])) {
        $jenis = 'mobil'; // Fallback
    }
    
    $config = $TARIF_CONFIG[$jenis];
    
    // Hitung selisih detik
    $in = strtotime($waktu_masuk);
    $out = is_numeric($waktu_keluar) ? $waktu_keluar : strtotime($waktu_keluar);
    
    $diff_seconds = $out - $in;
    // Hindari nilai negatif jika waktu kacau
    if ($diff_seconds < 0) $diff_seconds = 0;
    
    // 1. Pembulatan ke Atas (Ceil)
    $total_jam_pembulatan = ceil($diff_seconds / 3600);
    
    // Ketentuan: kurang dari 1 jam dihitung 1 jam
    if ($total_jam_pembulatan < 1) {
        $total_jam_pembulatan = 1;
    }
    
    // Pecah ke hari dan sisa jam
    $hari = floor($total_jam_pembulatan / 24);
    $sisa_jam = $total_jam_pembulatan % 24;
    
    $total_biaya = 0;
    $teks_rumus = "";
    
    // Biaya hari penuh (jika ada)
    $biaya_hari_penuh = $hari * $config['maksimal_harian'];
    $total_biaya += $biaya_hari_penuh;
    
    // Biaya sisa jam
    $biaya_sisa_jam = 0;
    if ($sisa_jam > 0) {
        $biaya_sisa_jam += $config['jam_pertama']; // 1 jam pertama
        if ($sisa_jam > 1) {
            $biaya_sisa_jam += ($sisa_jam - 1) * $config['jam_berikutnya']; // jam berikutnya
        }
        
        // Batasi sisa jam jika melebihi maksimal harian
        if ($biaya_sisa_jam > $config['maksimal_harian']) {
            $biaya_sisa_jam = $config['maksimal_harian'];
        }
    }
    
    $total_biaya += $biaya_sisa_jam;
    
    // Membuat rincian perhitungan rumus (format teks)
    if ($hari > 0) {
        $teks_rumus = "($hari Hari x Rp " . number_format($config['maksimal_harian'], 0, ',', '.') . ")";
        if ($sisa_jam > 0) {
            $teks_rumus .= " + ";
        }
    }
    
    if ($sisa_jam > 0) {
        if ($biaya_sisa_jam == $config['maksimal_harian']) {
            $teks_rumus .= "Maksimal Harian (Rp " . number_format($config['maksimal_harian'], 0, ',', '.') . ")";
        } else {
            $teks_rumus .= "Rp " . number_format($config['jam_pertama'], 0, ',', '.');
            if ($sisa_jam > 1) {
                $teks_rumus .= " + (" . ($sisa_jam - 1) . " x Rp " . number_format($config['jam_berikutnya'], 0, ',', '.') . ")";
            }
        }
    }
    
    // Format durasi string: "11 Jam 29 Menit"
    $dur_hours = floor($diff_seconds / 3600);
    $dur_mins = floor(($diff_seconds % 3600) / 60);
    $durasi_asli = "$dur_hours Jam $dur_mins Menit";
    
    return [
        'durasi_asli' => $durasi_asli,
        'total_jam_pembulatan' => $total_jam_pembulatan,
        'total_biaya' => $total_biaya,
        'teks_rumus' => $teks_rumus
    ];
}
?>
