<?php
include '../../app/_lib/auth.php';
check_login();
check_role(['siswa']);
include '../../app/_config/database.php';
include '../../templates/header.php';

$id_siswa = $_SESSION['user_id'];
$user_name = $_SESSION['user_nama'] ?? 'Siswa';

// --- PHP Logic for Stats & Data ---

// Jurnal Stats
$total_jurnal = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ?");
$total_jurnal->execute([$id_siswa]);
$total_jurnal_count = $total_jurnal->fetchColumn();

$approved_jurnal = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ? AND status = 'approved'");
$approved_jurnal->execute([$id_siswa]);
$approved_jurnal_count = $approved_jurnal->fetchColumn();

$pending_jurnal = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ? AND status = 'pending'");
$pending_jurnal->execute([$id_siswa]);
$pending_jurnal_count = $pending_jurnal->fetchColumn();

$rejected_jurnal = $pdo->prepare("SELECT COUNT(*) FROM jurnal_harian WHERE id_siswa = ? AND status = 'rejected'");
$rejected_jurnal->execute([$id_siswa]);
$rejected_jurnal_count = $rejected_jurnal->fetchColumn();

// Fetch Jurnals for table
$stmt_jurnals = $pdo->prepare("SELECT * FROM jurnal_harian WHERE id_siswa = ? ORDER BY tanggal_kegiatan DESC, created_at DESC LIMIT 5");
$stmt_jurnals->execute([$id_siswa]);
$jurnals = $stmt_jurnals->fetchAll(PDO::FETCH_ASSOC);

// Fetch all Jurnals for chart data
$stmt_all_jurnals = $pdo->prepare("SELECT tanggal_kegiatan, status FROM jurnal_harian WHERE id_siswa = ? ORDER BY tanggal_kegiatan ASC");
$stmt_all_jurnals->execute([$id_siswa]);
$all_jurnals = $stmt_all_jurnals->fetchAll(PDO::FETCH_ASSOC);

// Fetch Announcements
$stmt_pengumuman = $pdo->prepare("SELECT * FROM pengumuman WHERE target_audien IN ('all', 'siswa') ORDER BY created_at DESC LIMIT 3");
$stmt_pengumuman->execute();
$pengumumans = $stmt_pengumuman->fetchAll(PDO::FETCH_ASSOC);

// Calculate percentages
$approved_percentage = $total_jurnal_count > 0 ? round(($approved_jurnal_count / $total_jurnal_count) * 100) : 0;
$pending_percentage = $total_jurnal_count > 0 ? round(($pending_jurnal_count / $total_jurnal_count) * 100) : 0;
$rejected_percentage = $total_jurnal_count > 0 ? round(($rejected_jurnal_count / $total_jurnal_count) * 100) : 0;

// Prepare chart data
$chart_labels = [];
$chart_approved = [];
$chart_pending = [];
$chart_rejected = [];

foreach ($all_jurnals as $j) {
    $date = $j['tanggal_kegiatan'];
    if (!in_array($date, $chart_labels)) {
        $chart_labels[] = date('d M', strtotime($date));
        $chart_approved[] = 0;
        $chart_pending[] = 0;
        $chart_rejected[] = 0;
    }
    
    $index = array_search($date, $chart_labels);
    if ($j['status'] == 'approved') $chart_approved[$index]++;
    elseif ($j['status'] == 'pending') $chart_pending[$index]++;
    else $chart_rejected[$index]++;
}

$chart_labels_json = json_encode($chart_labels);
$chart_approved_json = json_encode($chart_approved);
$chart_pending_json = json_encode($chart_pending);
$chart_rejected_json = json_encode($chart_rejected);
$status_data_json = json_encode([$approved_jurnal_count, $pending_jurnal_count, $rejected_jurnal_count]);

?>

<div x-data="dashboardApp()" class="min-h-screen transition-colors duration-300" :class="darkMode ? 'dark bg-gray-900' : 'bg-gradient-to-br from-slate-50 via-white to-slate-100'">
    
    <!-- Mobile Menu Backdrop -->
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 z-20 bg-black/50 md:hidden animate-fadeIn" style="display: none;"></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-30 w-64 transition-transform duration-300 md:translate-x-0 flex flex-col" :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'" :class="darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'" style="border-right: 1px solid currentColor;">
        
        <!-- Sidebar Header -->
        <div class="flex h-20 items-center justify-between px-6 border-b" :class="darkMode ? 'border-gray-700' : 'border-gray-100'">
            <a href="index.php" class="flex items-center gap-3 font-bold text-xl">
                <div class="p-2.5 rounded-lg gradient-primary text-white font-bold text-lg">E</div>
                <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Jurnal</span>
            </a>
            <button @click="mobileMenuOpen = false" class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
            <a href="index.php" class="flex items-center gap-3 rounded-xl px-4 py-3 font-semibold text-white gradient-primary transition-all duration-200 hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4-4"/></svg>
                Dashboard
            </a>
            <a href="jurnal_history.php" class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium transition-all duration-200" :class="darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Jurnal
            </a>
            <div class="my-2 border-t" :class="darkMode ? 'border-gray-700' : 'border-gray-200'"></div>
            <a href="../logout.php" class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-red-600 transition-all duration-200 hover:bg-red-50 dark:hover:bg-red-900/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="border-t p-4 space-y-3 border-gray-100 bg-gray-50">
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col md:ml-64">
        
        <!-- Top Header/Navbar -->
        <header class="sticky top-0 z-20 flex h-20 items-center justify-between px-6 border-b transition-colors duration-300" :class="darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'" style="backdrop-filter: blur(10px); background-color: rgba(255,255,255,0.8);">
            
            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </button>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-md mx-4">
                <div class="relative w-full">
                    <input type="text" placeholder="Cari jurnal..." class="w-full px-4 py-2.5 rounded-xl border transition-all duration-200" :class="darkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20' : 'bg-gray-100 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20'">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3 md:gap-4">
                
                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    </button>
                    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-80 rounded-xl shadow-xl z-50 animate-slideInUp" :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-100'">
                        <div class="p-4 border-b" :class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                            <h3 class="font-bold text-lg">Notifikasi</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <div class="p-4 border-b" :class="darkMode ? 'border-gray-700 hover:bg-gray-750' : 'border-gray-100 hover:bg-gray-50'" style="cursor: pointer;">
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-sm">Jurnal Disetujui</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Jurnal tanggal 10 November telah disetujui oleh pembimbing.</p>
                                        <p class="text-xs text-gray-400 mt-2">2 jam yang lalu</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <button class="w-full text-center text-sm text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Lihat Semua Notifikasi</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                        <img class="w-10 h-10 rounded-full object-cover border-2 border-indigo-600" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=667eea&color=fff" alt="Avatar">
                        <div class="hidden sm:flex flex-col text-left text-sm">
                            <span class="font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Siswa</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-56 rounded-xl shadow-xl z-50 animate-slideInUp" :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-100'">
                        <div class="p-4 border-b" :class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                            <p class="font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($user_name); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Siswa PKL</p>
                        </div>
                        <a href="../logout.php" class="block px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 border-t" :class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>


        <!-- Main Content Area -->
        <main class="flex-1 overflow-auto">
            <div class="p-6 md:p-8 space-y-6 md:space-y-8">
                
                <!-- Welcome Banner -->
                <div class="relative overflow-hidden rounded-2xl p-8 md:p-12 text-white shadow-xl gradient-primary animate-slideInUp">
                    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-black/10 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <div>
                                <h1 class="text-4xl md:text-5xl font-bold mb-2">Halo, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>! 👋</h1>
                                <p class="text-lg text-white/90 max-w-2xl leading-relaxed">Siap untuk mencatat kegiatan PKL Anda hari ini? Jaga semangat dan terus catat progres Anda dengan teratur.</p>
                            </div>
                            <a href="jurnal_buat.php" class="flex items-center gap-2 px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 hover:shadow-xl whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Buat Jurnal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 animate-slideInUp" style="animation-delay: 0.1s;">
                    <!-- Total Jurnal -->
                    <div class="group relative rounded-xl p-6 transition-all duration-300 shadow-lg hover:shadow-2xl" :class="darkMode ? 'bg-gradient-to-br from-blue-900/40 to-blue-800/40 border border-blue-700/50' : 'bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200'">
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Total Jurnal</span>
                                <div class="p-2.5 rounded-lg bg-blue-600/20">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1"><?php echo $total_jurnal_count; ?></p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Jurnal dibuat</p>
                        </div>
                    </div>

                    <!-- Approved -->
                    <div class="group relative rounded-xl p-6 transition-all duration-300 shadow-lg hover:shadow-2xl" :class="darkMode ? 'bg-gradient-to-br from-green-900/40 to-emerald-900/40 border border-green-700/50' : 'bg-gradient-to-br from-green-50 to-emerald-100 border border-green-200'">
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Disetujui</span>
                                <div class="p-2.5 rounded-lg bg-green-600/20">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold text-green-600 dark:text-green-400 mb-1"><?php echo $approved_jurnal_count; ?></p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full transition-all duration-500" style="width: <?php echo $approved_percentage; ?>%;"></div>
                                </div>
                                <span class="text-xs font-bold text-green-600 dark:text-green-400"><?php echo $approved_percentage; ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="group relative rounded-xl p-6 transition-all duration-300 shadow-lg hover:shadow-2xl" :class="darkMode ? 'bg-gradient-to-br from-amber-900/40 to-orange-900/40 border border-amber-700/50' : 'bg-gradient-to-br from-amber-50 to-orange-100 border border-amber-200'">
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Pending</span>
                                <div class="p-2.5 rounded-lg bg-amber-600/20">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold text-amber-600 dark:text-amber-400 mb-1"><?php echo $pending_jurnal_count; ?></p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500 rounded-full transition-all duration-500" style="width: <?php echo $pending_percentage; ?>%;"></div>
                                </div>
                                <span class="text-xs font-bold text-amber-600 dark:text-amber-400"><?php echo $pending_percentage; ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected -->
                    <div class="group relative rounded-xl p-6 transition-all duration-300 shadow-lg hover:shadow-2xl" :class="darkMode ? 'bg-gradient-to-br from-red-900/40 to-rose-900/40 border border-red-700/50' : 'bg-gradient-to-br from-red-50 to-rose-100 border border-red-200'">
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Ditolak</span>
                                <div class="p-2.5 rounded-lg bg-red-600/20">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6v-2m0 6a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold text-red-600 dark:text-red-400 mb-1"><?php echo $rejected_jurnal_count; ?></p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: <?php echo $rejected_percentage; ?>%;"></div>
                                </div>
                                <span class="text-xs font-bold text-red-600 dark:text-red-400"><?php echo $rejected_percentage; ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 animate-slideInUp" style="animation-delay: 0.2s;">
                    
                    <!-- Status Pie Chart -->
                    <div class="lg:col-span-1 rounded-2xl p-6 shadow-lg" :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Status Jurnal</span>
                        </h3>
                        <canvas id="statusChart" style="max-height: 250px;"></canvas>
                    </div>

                    <!-- Trend Line Chart -->
                    <div class="lg:col-span-2 rounded-2xl p-6 shadow-lg" :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>Tren Jurnal</span>
                        </h3>
                        <canvas id="trendChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>

                <!-- Announcements & Recent Journals -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 animate-slideInUp" style="animation-delay: 0.3s;">
                    
                    <!-- Announcements -->
                    <div class="lg:col-span-1 rounded-2xl p-6 shadow-lg" :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.144-6.363a1.76 1.76 0 01.592-2.145l6.364-2.144a1.76 1.76 0 012.144.592z"/></svg>
                            <span>Pengumuman</span>
                        </h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <?php if (empty($pengumumans)): ?>
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.144-6.363a1.76 1.76 0 01.592-2.145l6.364-2.144a1.76 1.76 0 012.144.592z"/></svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tidak ada pengumuman</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($pengumumans as $p): ?>
                                    <div class="p-4 rounded-lg border-l-4 border-indigo-600 transition-all duration-200 hover:shadow-md cursor-pointer" :class="darkMode ? 'bg-gray-700/50 hover:bg-gray-700' : 'bg-gray-50 hover:bg-gray-100'">
                                        <h4 class="font-semibold text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($p['judul']); ?></h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 line-clamp-2"><?php echo htmlspecialchars($p['isi']); ?></p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                            <?php echo date('d M Y', strtotime($p['created_at'])); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Journals -->
                    <div class="lg:col-span-2 rounded-2xl p-6 shadow-lg" :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m-7 12h-2m2-12a2 2 0 00-2-2H7a2 2 0 00-2 2v4l3 3 3-3V6z"/></svg>
                                <span>Jurnal Terbaru</span>
                            </h3>
                            <a href="jurnal_history.php" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Semua →</a>
                        </div>
                        
                        <?php if (empty($jurnals)): ?>
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m-7 12h-2m2-12a2 2 0 00-2-2H7a2 2 0 00-2 2v4l3 3 3-3V6z"/></svg>
                                <p class="mt-4 text-gray-600 dark:text-gray-400">Belum ada jurnal</p>
                                <a href="jurnal_buat.php" class="mt-4 inline-block gradient-primary text-white font-semibold py-2 px-6 rounded-lg hover:shadow-lg transition-all duration-200">Buat Jurnal Sekarang</a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($jurnals as $jurnal): ?>
                                    <div class="group p-4 rounded-xl border transition-all duration-200 hover:shadow-md cursor-pointer" :class="darkMode ? 'border-gray-700 bg-gray-700/50 hover:bg-gray-700' : 'border-gray-200 bg-gray-50 hover:bg-white'">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 dark:text-white"><?php echo date('l, d M Y', strtotime($jurnal['tanggal_kegiatan'])); ?></p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2"><?php echo htmlspecialchars(substr($jurnal['deskripsi_kegiatan'], 0, 100)); ?></p>
                                            </div>
                                            <span class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap <?php 
                                                if ($jurnal['status'] == 'approved') echo 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
                                                elseif ($jurnal['status'] == 'rejected') echo 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
                                                else echo 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'; 
                                            ?>">
                                                <?php 
                                                    if ($jurnal['status'] == 'approved') echo '✓ Disetujui';
                                                    elseif ($jurnal['status'] == 'rejected') echo '✗ Ditolak';
                                                    else echo '⏳ Pending'; 
                                                ?>
                                            </span>
                                        </div>
                                        <a href="jurnal_detail.php?id=<?php echo $jurnal['id']; ?>" class="mt-3 inline-block text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                            Lihat Detail →
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Floating Action Button -->
    <a href="jurnal_buat.php" class="fixed bottom-8 right-8 z-40 group flex items-center gap-2 gradient-primary text-white px-6 py-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-110 animate-slideInUp">
        <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span class="hidden sm:inline font-semibold">Jurnal Baru</span>
    </a>
</div>

<!-- Chart.js Scripts -->
<script>
    function dashboardApp() {
        return {
            darkMode: localStorage.getItem('darkMode') === 'true',
            mobileMenuOpen: false,
            init() {
                this.$watch('darkMode', (val) => {
                    localStorage.setItem('darkMode', val);
                    this.updateCharts();
                });
            },
            updateCharts() {
                // Reinitialize charts with new theme
                initCharts(this.darkMode);
            }
        }
    }

    function initCharts(darkMode = false) {
        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: {
                        color: darkMode ? '#e5e7eb' : '#1f2937',
                        font: { size: 12, weight: '600' },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: darkMode ? '#1f2937' : '#ffffff',
                    titleColor: darkMode ? '#f9fafb' : '#111827',
                    bodyColor: darkMode ? '#e5e7eb' : '#374151',
                    borderColor: darkMode ? '#4b5563' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        labelColor: function(context) {
                            return {
                                borderColor: context.borderColor || '#666',
                                backgroundColor: context.backgroundColor || '#666'
                            };
                        }
                    }
                }
            }
        };

        // Status Pie Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx && statusCtx.chart) {
            statusCtx.chart.destroy();
        }
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Disetujui', 'Pending', 'Ditolak'],
                datasets: [{
                    data: <?php echo $status_data_json; ?>,
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderColor: darkMode ? '#1f2937' : '#ffffff',
                    borderWidth: 3,
                    borderRadius: 8
                }]
            },
            options: {
                ...chartDefaults,
                cutout: '70%'
            }
        });

        // Trend Line Chart
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx && trendCtx.chart) {
            trendCtx.chart.destroy();
        }
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?php echo $chart_labels_json; ?>,
                datasets: [
                    {
                        label: 'Disetujui',
                        data: <?php echo $chart_approved_json; ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Pending',
                        data: <?php echo $chart_pending_json; ?>,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Ditolak',
                        data: <?php echo $chart_rejected_json; ?>,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                ...chartDefaults,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: darkMode ? 'rgba(107, 114, 128, 0.1)' : 'rgba(229, 231, 235, 0.5)'
                        },
                        ticks: {
                            color: darkMode ? '#d1d5db' : '#6b7280'
                        }
                    },
                    x: {
                        grid: {
                            color: darkMode ? 'rgba(107, 114, 128, 0.1)' : 'rgba(229, 231, 235, 0.5)'
                        },
                        ticks: {
                            color: darkMode ? '#d1d5db' : '#6b7280'
                        }
                    }
                }
            }
        });
    }

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        initCharts(localStorage.getItem('darkMode') === 'true');
    });
</script>

<?php include '../../templates/footer.php'; ?>
