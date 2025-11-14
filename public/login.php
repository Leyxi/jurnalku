<?php include_once '../app/_lib/auth.php'; ?>
<?php include '../templates/header.php'; ?>

<main class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <!-- Back Button -->
        <div class="mb-6 flex items-center justify-between">
            <a href="index.php" class="inline-flex items-center gap-2 px-4 py-2 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header Gradient -->
            <div class="h-32 bg-gradient-to-r from-indigo-600 to-blue-600 flex items-center justify-center relative">
                <div class="text-white text-4xl opacity-20 absolute">📖</div>
                <h1 class="text-white text-2xl font-bold relative z-10">Masuk ke Akun Anda</h1>
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

                <form method="POST" action="../app/_logic/auth_login.php" class="space-y-5" id="loginForm">
                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-indigo-600 mr-2"></i>Email Address
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="nama@example.com"
                        >
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-indigo-600 mr-2"></i>Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="••••••••"
                        >
                        <a href="#" class="text-xs text-indigo-600 hover:text-indigo-700 mt-1 inline-block">Lupa password?</a>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember" 
                            class="h-4 w-4 text-indigo-600 rounded border-gray-300 cursor-pointer"
                        >
                        <label for="remember" class="text-sm text-gray-600 cursor-pointer">Ingat saya di perangkat ini</label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full py-3 mt-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-blue-700 transition shadow-md hover:shadow-lg"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk Sekarang
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center gap-4">
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <span class="text-xs text-gray-500">atau</span>
                    <div class="flex-1 h-px bg-gray-300"></div>
                </div>

                <!-- Social Login -->
                <button type="button" class="w-full py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    <i class="fab fa-google text-red-500 mr-2"></i>Masuk dengan Google
                </button>
            </div>

            <!-- Footer -->
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun? <a href="register.php" class="text-indigo-600 font-semibold hover:text-indigo-700">Daftar di sini</a>
                </p>
            </div>
        </div>

        <!-- Features Info -->
        <div class="mt-8 grid grid-cols-3 gap-4 text-center">
            <div>
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition">
                    <i class="fas fa-shield-alt text-indigo-600 text-2xl mb-2"></i>
                    <p class="text-xs font-semibold text-gray-700">Aman</p>
                </div>
            </div>
            <div>
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition">
                    <i class="fas fa-bolt text-blue-600 text-2xl mb-2"></i>
                    <p class="text-xs font-semibold text-gray-700">Cepat</p>
                </div>
            </div>
            <div>
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition">
                    <i class="fas fa-mobile-alt text-indigo-600 text-2xl mb-2"></i>
                    <p class="text-xs font-semibold text-gray-700">Mobile</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        if (!email || !password) {
            e.preventDefault();
            alert('Silakan isi semua field yang wajib diisi');
        }
    });
</script>

<?php include '../templates/footer.php'; ?>
