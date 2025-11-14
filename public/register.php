<?php include '../templates/header.php'; ?>

<main class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <!-- Back Button -->
        <div class="mb-6 flex items-center justify-between">
            <a href="index.php" class="inline-flex items-center gap-2 px-4 py-2 text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header Gradient -->
            <div class="h-32 bg-gradient-to-r from-green-600 to-teal-600 flex items-center justify-center relative">
                <div class="text-white text-4xl opacity-20 absolute">📝</div>
                <h1 class="text-white text-2xl font-bold relative z-10">Buat Akun Baru</h1>
            </div>

            <!-- Form Content -->
            <div class="p-8">
                <!-- Error Message -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="../app/_logic/auth_register.php" class="space-y-5" id="registerForm">
                    <!-- Full Name Input -->
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-green-600 mr-2"></i>Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            id="nama_lengkap" 
                            name="nama_lengkap" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="Nama Anda"
                        >
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-green-600 mr-2"></i>Email Address
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="email@example.com"
                        >
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-green-600 mr-2"></i>Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="Minimal 8 karakter"
                        >
                        <p class="text-xs text-gray-500 mt-1">Gunakan kombinasi huruf, angka, dan simbol</p>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="flex items-start gap-3">
                        <input 
                            type="checkbox" 
                            id="terms" 
                            name="terms" 
                            required
                            class="h-4 w-4 text-green-600 rounded border-gray-300 cursor-pointer mt-1"
                        >
                        <label for="terms" class="text-sm text-gray-600 cursor-pointer">
                            Saya setuju dengan <a href="#" class="text-green-600 font-semibold hover:text-green-700">Syarat & Ketentuan</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full py-3 mt-2 bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-teal-700 transition shadow-md hover:shadow-lg"
                    >
                        <i class="fas fa-user-plus mr-2"></i>Buat Akun
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center gap-4">
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <span class="text-xs text-gray-500">atau</span>
                    <div class="flex-1 h-px bg-gray-300"></div>
                </div>

                <!-- Social Sign Up -->
                <button type="button" class="w-full py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    <i class="fab fa-google text-red-500 mr-2"></i>Daftar dengan Google
                </button>
            </div>

            <!-- Footer -->
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-600">
                    Sudah punya akun? <a href="login.php" class="text-green-600 font-semibold hover:text-green-700">Masuk di sini</a>
                </p>
            </div>
        </div>

        <!-- Benefits Info -->
        <div class="mt-8 grid grid-cols-3 gap-4 text-center">
            <div>
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition">
                    <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                    <p class="text-xs font-semibold text-gray-700">Mudah</p>
                </div>
            </div>
            <div>
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition">
                    <i class="fas fa-lock text-green-600 text-2xl mb-2"></i>
                    <p class="text-xs font-semibold text-gray-700">Aman</p>
                </div>
            </div>
            <div>
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition">
                    <i class="fas fa-rocket text-teal-600 text-2xl mb-2"></i>
                    <p class="text-xs font-semibold text-gray-700">Cepat</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.getElementById('registerForm')?.addEventListener('submit', function(e) {
        const nama = document.getElementById('nama_lengkap').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        const terms = document.getElementById('terms').checked;
        
        if (!nama || !email || !password || !terms) {
            e.preventDefault();
            alert('Silakan isi semua field dan setujui syarat & ketentuan');
            return;
        }
        if (password.length < 8) {
            e.preventDefault();
            alert('Password harus minimal 8 karakter');
            return;
        }
    });
</script>

<?php include '../templates/footer.php'; ?>
