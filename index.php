<?php
session_start();
if(isset($_SESSION['login'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Masuk - ParkSmart</title>
    <?php include 'components/layout/head.php'; ?>
</head>
<body class="bg-surface text-on-surface h-screen w-full font-body-md overflow-hidden">
    <!-- Minimalist Navigation/Header for Splash Screen (Suppressed Standard Shell) -->
    <header class="fixed top-0 w-full z-50 p-lg flex justify-between items-center">
        <div class="flex items-center gap-xs">
            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1; font-size: 32px;">directions_car</span>
            <span class="font-headline-md text-headline-md font-bold text-primary">ParkSmart</span>
        </div>
        <a class="font-label-bold text-label-bold text-on-surface-variant hover:text-primary transition-colors flex items-center gap-xs" href="#">
            <span class="material-symbols-outlined text-[20px]">help</span> Bantuan
        </a>
    </header>
    
    <main class="h-full w-full flex flex-col md:flex-row">
        <!-- Visual Section (Split Screen) -->
        <div class="hidden md:flex flex-1 relative bg-surface-variant overflow-hidden">
            <img alt="Modern Smart Parking Facility" class="absolute inset-0 w-full h-full object-cover opacity-90 mix-blend-overlay" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDMa7nCI02yLEWAh-tA_lzCSB1kL7Kz3I93Whn8LGA_-9_g7NN9DC1d1FD31-oBktnRadXVuTB0JMZUcldXeJwqhKRB7MFeaddDMhBqjZ88qUmCR8aqgzU4Au8q39P_t-RljrQ6MKduwXpgjEoqcjzB-VPlaCXa-9rtfE46Z1XDZrqFofRs33wZNB5Z6e_QO7I0vFld98pEnaX4axFk5xPx5sVW2hcEZqMbdE8qmbKeh-5dVwagzO3KBN1nc6sFgPcSv09Gzr71z_RL"/>
            <div class="absolute inset-0 bg-gradient-to-r from-primary/80 to-primary-container/40"></div>
            <div class="relative z-10 flex flex-col justify-end p-xl w-full text-on-primary">
                <h1 class="font-headline-xl text-headline-xl mb-md max-w-2xl">Manajemen Mobilitas Urban yang Efisien.</h1>
                <p class="font-body-lg text-body-lg text-on-primary/90 max-w-xl">
                    Sistem pemantauan lahan parkir real-time dengan akurasi tinggi, pelaporan pendapatan instan, dan kontrol akses cerdas untuk fasilitas modern.
                </p>
            </div>
        </div>
        
        <!-- Login Form Section -->
        <div class="flex-1 flex items-center justify-center p-md lg:p-xl bg-surface relative z-10">
            <div class="w-full max-w-md">
                <div class="mb-xl text-center md:text-left">
                    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Masuk ke Akun Anda</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Silakan masukkan kredensial Anda untuk melanjutkan ke dasbor operator.</p>
                </div>
                
                <form action="php/proses/login_proses.php" method="POST" class="space-y-md">
                    
                    <!-- Error Message -->
                    <?php if(isset($_GET['error']) && $_GET['error'] == 1): ?>
                    <div class="bg-error-container text-on-error-container px-sm py-xs rounded-lg font-body-sm flex items-start gap-xs border border-error/20">
                        <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
                        <p>Kredensial tidak valid. Silakan periksa kembali email/username dan kata sandi Anda.</p>
                    </div>
                    <?php endif; ?>

                    <!-- Email/Username Input -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs" for="username">Alamat Email / Nama Pengguna</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-sm text-outline">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </span>
                            <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg py-sm pl-xl pr-sm text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none" id="username" name="username" placeholder="admin@parksmart.com" type="text" required autofocus />
                        </div>
                    </div>
                    
                    <!-- Password Input -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs" for="password">Kata Sandi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-sm text-outline">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </span>
                            <input class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg py-sm pl-xl pr-sm text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none" id="password" name="password" placeholder="••••••••" type="password" required />
                            <button class="absolute inset-y-0 right-0 flex items-center pr-sm text-outline hover:text-on-surface transition-colors" type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'; this.innerHTML = '<span class=\'material-symbols-outlined text-[20px]\'>' + (p.type === 'password' ? 'visibility_off' : 'visibility') + '</span>';">
                                <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary bg-surface-container-lowest cursor-pointer" id="remember-me" name="remember-me" type="checkbox"/>
                            <label class="ml-2 block font-body-sm text-body-sm text-on-surface-variant cursor-pointer" for="remember-me">
                                Ingat Saya
                            </label>
                        </div>
                        <div class="text-sm">
                            <a class="font-label-bold text-label-bold text-primary hover:text-primary-container transition-colors" href="#">
                                Lupa Kata Sandi?
                            </a>
                        </div>
                    </div>
                    
                    <!-- Login Button -->
                    <div class="pt-sm">
                        <button class="w-full flex justify-center items-center gap-xs py-sm px-md border border-transparent rounded-lg shadow-sm text-on-primary bg-primary hover:bg-primary-container font-label-bold text-label-bold transition-all duration-200 hover:shadow-md active:scale-[0.98]" type="submit">
                            Masuk
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </div>
                </form>
                
                <div class="mt-xl text-center">
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        Sistem Manajemen Parkir Resmi © 2026 ParkSmart Inc.
                    </p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
