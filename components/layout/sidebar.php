<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="bg-surface text-primary h-screen w-64 fixed left-0 top-0 border-r border-outline-variant shadow-sm flex flex-col pt-lg pb-sm z-50 hidden md:flex">
    <!-- Header -->
    <div class="mb-xl mt-sm flex items-center justify-center gap-sm">
        <img src="assets/Logo.png" alt="ParkSmart Logo" class="h-12 w-auto object-contain">
        <div class="flex flex-col justify-center">
            <h1 class="font-headline-md text-[26px] font-bold text-primary leading-none tracking-tight">ParkSmart</h1>
            <p class="font-body-sm text-[12px] text-on-surface-variant leading-tight mt-1">Parking Management</p>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 flex flex-col gap-base">
        <a class="flex items-center gap-md <?= $current_page == 'gate_entry.php' ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> px-md py-sm transition-all duration-200 ease-in-out" href="gate_entry.php">
            <span class="material-symbols-outlined" <?= $current_page == 'gate_entry.php' ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>>login</span>
            <span class="font-label-bold text-label-bold">Gate Entry</span>
        </a>

        <a class="flex items-center gap-md <?= $current_page == 'gate_out.php' ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> px-md py-sm transition-all duration-200 ease-in-out" href="gate_out.php">
            <span class="material-symbols-outlined" <?= $current_page == 'gate_out.php' ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>>logout</span>
            <span class="font-label-bold text-label-bold">Gate Exit</span>
        </a>

        <a class="flex items-center gap-md <?= $current_page == 'dashboard.php' ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> px-md py-sm transition-all duration-200 ease-in-out" href="dashboard.php">
            <span class="material-symbols-outlined" <?= $current_page == 'dashboard.php' ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>>dashboard_customize</span>
            <span class="font-label-bold text-label-bold">Monitoring</span>
        </a>

        <a class="flex items-center gap-md <?= $current_page == 'history.php' ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> px-md py-sm transition-all duration-200 ease-in-out" href="history.php">
            <span class="material-symbols-outlined" <?= $current_page == 'history.php' ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>>history</span>
            <span class="font-label-bold text-label-bold">Ticket History</span>
        </a>

        <a class="flex items-center gap-md <?= $current_page == 'revenue.php' ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> px-md py-sm transition-all duration-200 ease-in-out" href="revenue.php">
            <span class="material-symbols-outlined" <?= $current_page == 'revenue.php' ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>>payments</span>
            <span class="font-label-bold text-label-bold">Revenue Reports</span>
        </a>


    </nav>

    <!-- Footer Tabs -->
    <div class="mt-auto flex flex-col gap-base px-sm mb-xs">
        <a class="flex items-center gap-md <?= $current_page == 'settings.php' ? 'bg-secondary-container text-on-secondary-container border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> px-md py-sm transition-all duration-200 ease-in-out rounded-lg" href="settings.php">
            <span class="material-symbols-outlined" <?= $current_page == 'settings.php' ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>>settings</span>
            <span class="font-label-bold text-label-bold">Settings</span>
        </a>
        <a class="flex items-center gap-md text-error px-md py-sm hover:bg-error-container hover:text-on-error-container transition-colors transition-all duration-200 ease-in-out rounded-lg" href="logout.php">
            <span class="material-symbols-outlined">logout</span>
            <span class="font-label-bold text-label-bold">Logout</span>
        </a>
    </div>
</aside>
