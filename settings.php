<?php
session_start();
require 'php/database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Fetch current settings from database
$query_settings = mysqli_query($koneksi, "SELECT * FROM settings");
$db_settings = [];
while ($row = mysqli_fetch_assoc($query_settings)) {
    $db_settings[$row['setting_key']] = $row['setting_value'];
}

// Fetch admin email from users table
$user_id = $_SESSION['user_id'];
$query_user = mysqli_query($koneksi, "SELECT email FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($query_user);
$email = $user_data['email'] ?? 'admin@parksmart.com';

$loc = $db_settings['location'] ?? 'Gedung ParkSmart Center, Jl. Teknologi No. 88, Kota Modern';
$t_motor_1 = $db_settings['tarif_motor_1'] ?? 2000;
$t_motor_next = $db_settings['tarif_motor_next'] ?? 1000;
$t_motor_max = $db_settings['tarif_motor_max'] ?? 15000;
$t_mobil_1 = $db_settings['tarif_mobil_1'] ?? 5000;
$t_mobil_next = $db_settings['tarif_mobil_next'] ?? 2000;
$t_mobil_max = $db_settings['tarif_mobil_max'] ?? 30000;

// Fetch all admins
$query_admins = mysqli_query($koneksi, "SELECT id, username, email, name, profile_picture FROM users ORDER BY id ASC");
$admins = [];
if ($query_admins) {
    while ($row = mysqli_fetch_assoc($query_admins)) {
        $admins[] = $row;
    }
}


?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Settings - ParkSmart</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-fixed-dim": "#b6c4ff",
                        "inverse-on-surface": "#f1f0f7",
                        "outline-variant": "#c5c5d3",
                        "inverse-primary": "#b6c4ff",
                        "surface-dim": "#dad9e1",
                        "error-container": "#ffdad6",
                        "tertiary-container": "#6e2c00",
                        "on-tertiary-fixed-variant": "#773205",
                        "secondary-fixed-dim": "#b7c8e1",
                        "surface-container-lowest": "#ffffff",
                        "on-secondary-fixed": "#0b1c30",
                        "surface-container-low": "#f4f3fa",
                        "surface-container": "#eeedf4",
                        "outline": "#757682",
                        "surface-variant": "#e3e1e9",
                        "primary-container": "#1e3a8a",
                        "secondary-fixed": "#d3e4fe",
                        "on-primary-fixed": "#00164e",
                        "primary-fixed": "#dce1ff",
                        "on-primary-fixed-variant": "#264191",
                        "on-background": "#1a1b21",
                        "on-error-container": "#93000a",
                        "primary": "#00236f",
                        "surface-bright": "#faf8ff",
                        "surface": "#faf8ff",
                        "background": "#faf8ff",
                        "tertiary-fixed-dim": "#ffb691",
                        "secondary-container": "#d0e1fb",
                        "on-primary-container": "#90a8ff",
                        "on-primary": "#ffffff",
                        "surface-container-high": "#e9e7ef",
                        "on-tertiary": "#ffffff",
                        "on-tertiary-container": "#f39461",
                        "on-secondary": "#ffffff",
                        "secondary": "#505f76",
                        "on-surface-variant": "#444651",
                        "on-tertiary-fixed": "#341100",
                        "on-secondary-container": "#54647a",
                        "error": "#ba1a1a",
                        "tertiary-fixed": "#ffdbcb",
                        "surface-tint": "#4059aa",
                        "surface-container-highest": "#e3e1e9",
                        "on-surface": "#1a1b21",
                        "on-secondary-fixed-variant": "#38485d",
                        "inverse-surface": "#2f3036",
                        "tertiary": "#4b1c00",
                        "on-error": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "24px",
                        "container-max": "1440px",
                        "xs": "4px",
                        "sm": "12px",
                        "md": "24px",
                        "base": "8px",
                        "xl": "64px",
                        "lg": "40px"
                    },
                    "fontFamily": {
                        "body-sm": ["Inter"],
                        "headline-xl": ["Hanken Grotesk"],
                        "label-bold": ["Inter"],
                        "body-lg": ["Inter"],
                        "headline-lg": ["Hanken Grotesk"],
                        "label-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "headline-md": ["Hanken Grotesk"],
                        "headline-lg-mobile": ["Hanken Grotesk"]
                    },
                    "fontSize": {
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "headline-xl": ["40px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "label-bold": ["14px", { "lineHeight": "20px", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700" }],
                        "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "500" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                        "headline-lg-mobile": ["24px", { "lineHeight": "32px", "fontWeight": "700" }]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #dad9e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #c5c5d3;
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md text-body-md h-screen flex overflow-hidden">
    <!-- SideNavBar -->
    <?php include 'components/layout/sidebar.php'; ?>
    
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col md:ml-64 w-full h-full">
        <!-- TopAppBar -->
        <?php include 'components/layout/header.php'; ?>
        
        <!-- Main Canvas -->
        <main class="flex-1 overflow-y-auto p-md lg:p-lg bg-surface-container-lowest md:bg-transparent relative">
            <form action="php/proses/settings_proses.php" method="POST" enctype="multipart/form-data" class="max-w-container-max mx-auto h-full flex flex-col">
                <div class="mb-lg">
                    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">System Configuration</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Manage global settings, rates, and security preferences.</p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-md flex-1">
                    <!-- Left Column: General & Rates -->
                    <div class="lg:col-span-8 flex flex-col gap-md">
                        
                        <!-- General Settings Card -->
                        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-md">
                            <div class="flex items-center gap-sm mb-md pb-sm border-b border-surface-variant">
                                <span class="material-symbols-outlined text-primary">storefront</span>
                                <h3 class="font-headline-md text-headline-md text-on-surface">General Settings</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-md mb-md">
                                <div class="flex flex-col gap-xs">
                                    <label class="font-label-sm text-label-sm text-on-surface-variant" for="admin-name">Admin Name</label>
                                    <input class="border border-outline-variant rounded-lg p-sm font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors w-full bg-surface" id="admin-name" name="admin_name" type="text" value="<?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?>" required/>
                                </div>
                                <div class="flex flex-col gap-xs">
                                    <label class="font-label-sm text-label-sm text-on-surface-variant" for="contact-email">Admin Login Email</label>
                                    <input class="border border-outline-variant rounded-lg p-sm font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors w-full bg-surface" id="contact-email" name="admin_email" type="email" value="<?= htmlspecialchars($email) ?>" required/>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                                <div class="flex flex-col gap-xs">
                                    <label class="font-label-sm text-label-sm text-on-surface-variant" for="location">Location (Cetak Struk)</label>
                                    <input class="border border-outline-variant rounded-lg p-sm font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors w-full bg-surface" id="location" name="location" type="text" value="<?= htmlspecialchars($loc) ?>" required/>
                                </div>
                                <div class="flex flex-col gap-xs">
                                    <label class="font-label-sm text-label-sm text-on-surface-variant" for="profile-pic">Profile Picture (JPG/PNG)</label>
                                    <input class="border border-outline-variant rounded-lg p-sm font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors w-full bg-surface file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-container file:text-on-primary-container hover:file:bg-primary hover:file:text-white" id="profile-pic" name="profile_picture" type="file" accept="image/png, image/jpeg, image/jpg"/>
                                </div>
                            </div>
                        </section>
                        
                        <!-- Parking Rates Card -->
                        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-md">
                            <div class="flex items-center justify-between mb-md pb-sm border-b border-surface-variant">
                                <div class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-primary">payments</span>
                                    <h3 class="font-headline-md text-headline-md text-on-surface">Hourly Parking Rates (Progressive)</h3>
                                </div>
                                <button type="button" onclick="openRatesModal()" class="text-primary font-label-bold text-label-bold hover:underline">Edit All</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-outline-variant text-on-surface-variant font-label-bold text-label-bold">
                                            <th class="py-sm px-xs w-1/4">Vehicle Type</th>
                                            <th class="py-sm px-xs w-1/4">1st Hour (Rp)</th>
                                            <th class="py-sm px-xs w-1/4">Next Hour (Rp)</th>
                                            <th class="py-sm px-xs w-1/4">Daily Max (Rp)</th>
                                            <th class="py-sm px-xs text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-body-md text-body-md">
                                        <!-- Display rows logic -->
                                        <tr class="border-b border-surface-variant last:border-0 hover:bg-surface transition-colors">
                                            <td class="py-sm px-xs flex items-center gap-sm">
                                                <div class="w-8 h-8 rounded bg-surface-variant flex items-center justify-center text-secondary">
                                                    <span class="material-symbols-outlined text-sm">two_wheeler</span>
                                                </div>
                                                Motor
                                            </td>
                                            <td class="py-sm px-xs"><span id="display_motor_1"><?= number_format($t_motor_1, 0, ',', '.') ?></span></td>
                                            <td class="py-sm px-xs">+<span id="display_motor_next"><?= number_format($t_motor_next, 0, ',', '.') ?></span></td>
                                            <td class="py-sm px-xs"><span id="display_motor_max"><?= number_format($t_motor_max, 0, ',', '.') ?></span></td>
                                            <td class="py-sm px-xs text-right">
                                                <button type="button" onclick="openRatesModal()" class="text-secondary hover:text-primary transition-colors p-1 rounded hover:bg-surface-container-low"><span class="material-symbols-outlined">edit</span></button>
                                            </td>
                                        </tr>
                                        <tr class="border-b border-surface-variant last:border-0 hover:bg-surface transition-colors">
                                            <td class="py-sm px-xs flex items-center gap-sm">
                                                <div class="w-8 h-8 rounded bg-surface-variant flex items-center justify-center text-secondary">
                                                    <span class="material-symbols-outlined text-sm">directions_car</span>
                                                </div>
                                                Mobil
                                            </td>
                                            <td class="py-sm px-xs"><span id="display_mobil_1"><?= number_format($t_mobil_1, 0, ',', '.') ?></span></td>
                                            <td class="py-sm px-xs">+<span id="display_mobil_next"><?= number_format($t_mobil_next, 0, ',', '.') ?></span></td>
                                            <td class="py-sm px-xs"><span id="display_mobil_max"><?= number_format($t_mobil_max, 0, ',', '.') ?></span></td>
                                            <td class="py-sm px-xs text-right">
                                                <button type="button" onclick="openRatesModal()" class="text-secondary hover:text-primary transition-colors p-1 rounded hover:bg-surface-container-low"><span class="material-symbols-outlined">edit</span></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        
                        <!-- Admin Management Card -->
                        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-md">
                            <div class="flex items-center justify-between mb-md pb-sm border-b border-surface-variant">
                                <div class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-primary">manage_accounts</span>
                                    <h3 class="font-headline-md text-headline-md text-on-surface">Administrator Management</h3>
                                </div>
                                <button type="button" onclick="openAdminModal()" class="px-3 py-1 bg-primary text-white font-label-bold text-label-bold rounded hover:bg-primary-container hover:text-on-primary-container transition-colors flex items-center gap-xs text-sm">
                                    <span class="material-symbols-outlined text-[18px]">person_add</span> Tambah Admin
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-outline-variant text-on-surface-variant font-label-bold text-label-bold">
                                            <th class="py-sm px-xs w-1/3">Admin Name</th>
                                            <th class="py-sm px-xs w-1/4">Username</th>
                                            <th class="py-sm px-xs w-1/4">Email</th>
                                            <th class="py-sm px-xs text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-body-sm text-body-sm">
                                        <?php foreach ($admins as $adm): ?>
                                        <tr class="border-b border-surface-variant last:border-0 hover:bg-surface transition-colors">
                                            <td class="py-sm px-xs flex items-center gap-sm">
                                                <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center border border-outline-variant shadow-sm overflow-hidden shrink-0">
                                                    <?php if (!empty($adm['profile_picture'])): ?>
                                                        <img src="<?= htmlspecialchars($adm['profile_picture']) ?>" alt="Profile" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <span class="material-symbols-outlined text-on-primary-container text-[16px]">person</span>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="font-label-bold text-label-bold text-on-surface"><?= htmlspecialchars($adm['name']) ?></span>
                                            </td>
                                            <td class="py-sm px-xs"><?= htmlspecialchars($adm['username']) ?></td>
                                            <td class="py-sm px-xs truncate max-w-[120px]"><?= htmlspecialchars($adm['email'] ?? '-') ?></td>
                                            <td class="py-sm px-xs text-right">
                                                <?php if ($adm['id'] != $_SESSION['user_id']): ?>
                                                    <form action="php/proses/delete_admin_proses.php" method="POST" class="inline" onsubmit="return confirmDeleteAdmin(event)">
                                                        <input type="hidden" name="delete_id" value="<?= $adm['id'] ?>">
                                                        <button type="submit" class="text-error hover:text-on-error-container transition-colors p-1 rounded hover:bg-error-container" title="Hapus Admin">
                                                            <span class="material-symbols-outlined">delete</span>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-on-surface-variant italic text-[11px]">Current</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                    
                    <!-- Right Column: Security & System Info -->
                    <div class="lg:col-span-4 flex flex-col gap-md">
                        
                        <!-- Security Card -->
                        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-md">
                            <div class="flex items-center gap-sm mb-md pb-sm border-b border-surface-variant">
                                <span class="material-symbols-outlined text-primary">security</span>
                                <h3 class="font-headline-md text-headline-md text-on-surface">Security</h3>
                            </div>
                            <div class="flex flex-col gap-md">
                                <button type="button" onclick="openPasswordModal()" class="w-full flex items-center justify-between p-sm border border-outline-variant rounded-lg hover:bg-surface-container-low transition-colors group">
                                    <div class="flex flex-col text-left">
                                        <span class="font-label-bold text-label-bold text-on-surface group-hover:text-primary transition-colors">Change Admin Password</span>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">Update your login credentials</span>
                                    </div>
                                    <span class="material-symbols-outlined text-outline-variant group-hover:text-primary transition-colors">chevron_right</span>
                                </button>
                                
                                <div class="flex items-center justify-between p-sm bg-surface-container-low rounded-lg border border-surface-variant">
                                    <div class="flex flex-col">
                                        <span class="font-label-bold text-label-bold text-on-surface">Two-Factor Auth</span>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">SMS or Authenticator App</span>
                                    </div>
                                    <!-- Toggle Switch -->
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input class="sr-only peer" type="checkbox" value=""/>
                                        <div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-surface-variant after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                            </div>
                        </section>
                        
                        <!-- System Information Card -->
                        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-md">
                            <div class="flex items-center gap-sm mb-md pb-sm border-b border-surface-variant">
                                <span class="material-symbols-outlined text-primary">info</span>
                                <h3 class="font-headline-md text-headline-md text-on-surface">System Information</h3>
                            </div>
                            <ul class="flex flex-col gap-sm">
                                <li class="flex justify-between items-center py-xs border-b border-surface-variant last:border-0">
                                    <span class="font-label-bold text-label-bold text-on-surface-variant">App Version</span>
                                    <span class="font-body-md text-body-md text-on-surface font-mono bg-surface-container-low px-2 py-0.5 rounded">v2.1.0</span>
                                </li>
                                <li class="flex justify-between items-center py-xs border-b border-surface-variant last:border-0">
                                    <span class="font-label-bold text-label-bold text-on-surface-variant">Database Status</span>
                                    <div class="flex items-center gap-xs">
                                        <div class="w-2 h-2 rounded-full bg-[#10b981]"></div>
                                        <span class="font-body-md text-body-md text-on-surface">Connected</span>
                                    </div>
                                </li>
                                <li class="flex justify-between items-center py-xs border-b border-surface-variant last:border-0">
                                    <span class="font-label-bold text-label-bold text-on-surface-variant">Last Sync</span>
                                    <span class="font-body-md text-body-md text-on-surface">Today, <?php echo date('h:i A'); ?></span>
                                </li>
                            </ul>
                        </section>
                    </div>
                </div>
                
                <!-- Action Bar -->
                <div class="mt-xl flex justify-end gap-sm border-t border-outline-variant pt-md mb-md">
                    <button type="button" onclick="window.location.reload()" class="px-md py-sm border border-outline-variant text-on-surface-variant font-label-bold text-label-bold rounded-lg hover:bg-surface-container-low transition-colors active:scale-95">Discard Changes</button>
                    <button type="submit" class="px-md py-sm bg-primary text-white font-label-bold text-label-bold rounded-lg shadow-sm hover:bg-primary-container hover:text-on-primary-container transition-colors flex items-center gap-xs active:scale-95">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Save Changes
                    </button>
                </div>

                <!-- Hidden Modal for Editing Rates (Inside form so inputs submit) -->
                <div id="ratesModal" class="fixed inset-0 z-[100] bg-black/50 hidden flex items-center justify-center p-md backdrop-blur-sm transition-opacity">
                    <div class="bg-surface-container-lowest w-full max-w-2xl rounded-xl shadow-lg border border-outline-variant overflow-hidden flex flex-col max-h-full">
                        <div class="px-lg py-md border-b border-surface-variant flex justify-between items-center bg-surface">
                            <h3 class="font-headline-md text-headline-md text-on-surface">Edit Progressive Rates</h3>
                            <button type="button" onclick="closeRatesModal()" class="text-on-surface-variant hover:text-error transition-colors p-xs rounded-full hover:bg-error-container">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-lg overflow-y-auto flex-1">
                            <h4 class="font-label-bold text-label-bold text-primary mb-sm flex items-center gap-xs"><span class="material-symbols-outlined text-[18px]">two_wheeler</span> Motor Rates</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-md mb-lg p-md bg-surface-container-low rounded-lg border border-surface-variant">
                                <div>
                                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">1st Hour (Rp)</label>
                                    <input type="number" name="tarif_motor_1" id="tarif_motor_1" value="<?= $t_motor_1 ?>" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                </div>
                                <div>
                                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Next Hour (Rp)</label>
                                    <input type="number" name="tarif_motor_next" id="tarif_motor_next" value="<?= $t_motor_next ?>" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                </div>
                                <div>
                                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Daily Max (Rp)</label>
                                    <input type="number" name="tarif_motor_max" id="tarif_motor_max" value="<?= $t_motor_max ?>" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                </div>
                            </div>

                            <h4 class="font-label-bold text-label-bold text-primary mb-sm flex items-center gap-xs"><span class="material-symbols-outlined text-[18px]">directions_car</span> Mobil Rates</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-md p-md bg-surface-container-low rounded-lg border border-surface-variant">
                                <div>
                                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">1st Hour (Rp)</label>
                                    <input type="number" name="tarif_mobil_1" id="tarif_mobil_1" value="<?= $t_mobil_1 ?>" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                </div>
                                <div>
                                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Next Hour (Rp)</label>
                                    <input type="number" name="tarif_mobil_next" id="tarif_mobil_next" value="<?= $t_mobil_next ?>" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                </div>
                                <div>
                                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Daily Max (Rp)</label>
                                    <input type="number" name="tarif_mobil_max" id="tarif_mobil_max" value="<?= $t_mobil_max ?>" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                </div>
                            </div>
                        </div>
                        <div class="px-lg py-md border-t border-surface-variant bg-surface flex justify-end gap-sm">
                            <button type="button" onclick="closeRatesModal()" class="px-md py-sm border border-outline-variant text-on-surface-variant font-label-bold text-label-bold rounded-lg hover:bg-surface-container-low transition-colors">Batal</button>
                            <button type="button" onclick="applyRatesPreview()" class="px-md py-sm bg-primary text-white font-label-bold text-label-bold rounded-lg shadow-sm hover:bg-primary-container hover:text-on-primary-container transition-colors">Terapkan (Preview)</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Password Change Modal (Separate form) -->
            <div id="passwordModal" class="fixed inset-0 z-[100] bg-black/50 hidden flex items-center justify-center p-md backdrop-blur-sm transition-opacity">
                <div class="bg-surface-container-lowest w-full max-w-md rounded-xl shadow-lg border border-outline-variant overflow-hidden flex flex-col">
                    <form action="php/proses/password_proses.php" method="POST">
                        <div class="px-lg py-md border-b border-surface-variant flex justify-between items-center bg-surface">
                            <h3 class="font-headline-md text-headline-md text-on-surface">Change Password</h3>
                            <button type="button" onclick="closePasswordModal()" class="text-on-surface-variant hover:text-error transition-colors p-xs rounded-full hover:bg-error-container">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-lg flex flex-col gap-md">
                            <div>
                                <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Current Password</label>
                                <input type="password" name="old_password" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none" required>
                            </div>
                            <div>
                                <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">New Password</label>
                                <input type="password" name="new_password" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none" required>
                            </div>
                            <div>
                                <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none" required>
                            </div>
                        </div>
                        <div class="px-lg py-md border-t border-surface-variant bg-surface flex justify-end gap-sm">
                            <button type="button" onclick="closePasswordModal()" class="px-md py-sm border border-outline-variant text-on-surface-variant font-label-bold text-label-bold rounded-lg hover:bg-surface-container-low transition-colors">Cancel</button>
                            <button type="submit" class="px-md py-sm bg-primary text-white font-label-bold text-label-bold rounded-lg shadow-sm hover:bg-primary-container hover:text-on-primary-container transition-colors">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add Admin Modal -->
            <div id="addAdminModal" class="fixed inset-0 z-[100] bg-black/50 hidden flex items-center justify-center p-md backdrop-blur-sm transition-opacity">
                <div class="bg-surface-container-lowest w-full max-w-md rounded-xl shadow-lg border border-outline-variant overflow-hidden flex flex-col">
                    <form action="php/proses/add_admin_proses.php" method="POST">
                        <div class="px-lg py-md border-b border-surface-variant flex justify-between items-center bg-surface">
                            <h3 class="font-headline-md text-headline-md text-on-surface">Tambah Admin Baru</h3>
                            <button type="button" onclick="closeAdminModal()" class="text-on-surface-variant hover:text-error transition-colors p-xs rounded-full hover:bg-error-container">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="p-lg flex flex-col gap-md">
                            <div>
                                <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Nama Lengkap</label>
                                <input type="text" name="admin_name" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none" required placeholder="Cth: Kasir 2">
                            </div>
                            <div>
                                <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Username</label>
                                <input type="text" name="admin_username" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none" required placeholder="Cth: kasir_2">
                            </div>
                            <div>
                                <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Email (Opsional)</label>
                                <input type="email" name="admin_email" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none" placeholder="kasir2@parksmart.com">
                            </div>
                            <div>
                                <label class="font-label-sm text-label-sm text-on-surface-variant block mb-xs">Password (Min. 6 Karakter)</label>
                                <input type="password" name="admin_password" class="w-full border border-outline-variant rounded p-sm bg-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none" required minlength="6">
                            </div>
                        </div>
                        <div class="px-lg py-md border-t border-surface-variant bg-surface flex justify-end gap-sm">
                            <button type="button" onclick="closeAdminModal()" class="px-md py-sm border border-outline-variant text-on-surface-variant font-label-bold text-label-bold rounded-lg hover:bg-surface-container-low transition-colors">Batal</button>
                            <button type="submit" class="px-md py-sm bg-primary text-white font-label-bold text-label-bold rounded-lg shadow-sm hover:bg-primary-container hover:text-on-primary-container transition-colors">Buat Akun</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function openRatesModal() {
            document.getElementById('ratesModal').classList.remove('hidden');
        }

        function closeRatesModal() {
            document.getElementById('ratesModal').classList.add('hidden');
        }

        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        function openAdminModal() {
            document.getElementById('addAdminModal').classList.remove('hidden');
        }

        function closeAdminModal() {
            document.getElementById('addAdminModal').classList.add('hidden');
        }

        function confirmDeleteAdmin(event) {
            event.preventDefault();
            const form = event.target;
            Swal.fire({
                title: 'Hapus Admin?',
                text: "Akun admin ini akan dihapus secara permanen dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba1a1a',
                cancelButtonColor: '#757682',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                background: '#faf8ff',
                color: '#1a1b21'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function applyRatesPreview() {
            // Get values from modal inputs
            const tm1 = document.getElementById('tarif_motor_1').value;
            const tmn = document.getElementById('tarif_motor_next').value;
            const tmm = document.getElementById('tarif_motor_max').value;
            const tb1 = document.getElementById('tarif_mobil_1').value;
            const tbn = document.getElementById('tarif_mobil_next').value;
            const tbm = document.getElementById('tarif_mobil_max').value;

            // Update UI table 
            document.getElementById('display_motor_1').innerText = Number(tm1).toLocaleString('id-ID');
            document.getElementById('display_motor_next').innerText = Number(tmn).toLocaleString('id-ID');
            document.getElementById('display_motor_max').innerText = Number(tmm).toLocaleString('id-ID');
            document.getElementById('display_mobil_1').innerText = Number(tb1).toLocaleString('id-ID');
            document.getElementById('display_mobil_next').innerText = Number(tbn).toLocaleString('id-ID');
            document.getElementById('display_mobil_max').innerText = Number(tbm).toLocaleString('id-ID');

            closeRatesModal();
        }

        // Show SweetAlert on success
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') == 1) {
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: 'Pengaturan sistem berhasil diperbarui.',
                confirmButtonColor: '#00236f',
                background: '#faf8ff',
                color: '#1a1b21'
            });
            window.history.replaceState({}, document.title, "settings.php");
        }
        
        if (urlParams.get('pw_success') == 1) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Password berhasil diubah. Silakan gunakan password baru pada sesi berikutnya.',
                confirmButtonColor: '#00236f',
                background: '#faf8ff',
                color: '#1a1b21'
            });
            window.history.replaceState({}, document.title, "settings.php");
        }

        if (urlParams.get('pw_error')) {
            let msg = 'Gagal mengubah password.';
            if (urlParams.get('pw_error') == 'mismatch') msg = 'Password baru dan konfirmasi tidak cocok.';
            if (urlParams.get('pw_error') == 'wrong') msg = 'Password saat ini salah.';
            if (urlParams.get('pw_error') == 'short') msg = 'Password baru minimal 6 karakter.';
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg,
                confirmButtonColor: '#00236f',
                background: '#faf8ff',
                color: '#1a1b21'
            });
            window.history.replaceState({}, document.title, "settings.php");
        }

        // Admin Management Alerts
        if (urlParams.get('admin_success') == 1) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Akun Admin baru berhasil dibuat.',
                confirmButtonColor: '#00236f',
                background: '#faf8ff',
                color: '#1a1b21'
            });
            window.history.replaceState({}, document.title, "settings.php");
        }
        
        if (urlParams.get('admin_delete') == 1) {
            Swal.fire({
                icon: 'success',
                title: 'Dihapus!',
                text: 'Akun Admin berhasil dihapus.',
                confirmButtonColor: '#00236f',
                background: '#faf8ff',
                color: '#1a1b21'
            });
            window.history.replaceState({}, document.title, "settings.php");
        }

        if (urlParams.get('admin_error')) {
            let msg = 'Gagal memproses data admin.';
            if (urlParams.get('admin_error') == 'exists') msg = 'Username tersebut sudah terdaftar!';
            if (urlParams.get('admin_error') == 'short_pw') msg = 'Password minimal harus 6 karakter!';
            if (urlParams.get('admin_error') == 'self_delete') msg = 'Anda tidak bisa menghapus akun yang sedang Anda gunakan!';
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg,
                confirmButtonColor: '#ba1a1a',
                background: '#faf8ff',
                color: '#1a1b21'
            });
            window.history.replaceState({}, document.title, "settings.php");
        }
    </script>
</body>
</html>
