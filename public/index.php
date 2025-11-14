<?php
// Homepage E-Jurnal PKL - Premium Edition (Inline Styles Only)
?>
<?php include '../templates/header.php'; ?>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #111827;
            background: #ffffff;
        }



        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        /* Navbar */
        .navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }

        .navbar-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }

        .navbar-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.05rem;
        }

        .btn-outline {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-outline:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            width: fit-content;
        }

        /* Hero Section */
        .hero {
            padding: 8rem 1rem 5rem;
            position: relative;
            overflow: hidden;
            background: white;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 80px;
            right: 0;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0.05;
            border-radius: 50%;
            filter: blur(100px);
            z-index: -1;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin: 1rem 0;
            line-height: 1.1;
            color: #111827;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #4b5563;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.5rem;
        }

        /* Tentang Section */
        .tentang-section {
            padding: 6rem 1rem;
            background: #f9fafb;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 1rem 0;
            color: #111827;
        }

        .section-header p {
            font-size: 1.1rem;
            color: #4b5563;
            max-width: 600px;
            margin: 0 auto;
        }

        .tentang-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 200ms;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .card-icon.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .card-icon.blue {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        }

        .card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 1rem 0 0.5rem;
            color: #111827;
        }

        .card p {
            color: #4b5563;
            line-height: 1.6;
        }

        /* Fitur Section */
        .fitur-section {
            padding: 6rem 1rem;
            background: white;
        }

        .fitur-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .fitur-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 200ms;
        }

        .fitur-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .fitur-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .fitur-card h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .fitur-card p {
            font-size: 0.875rem;
            color: #4b5563;
        }

        /* Role Section */
        .role-section {
            padding: 6rem 1rem;
            background: #f9fafb;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .role-card {
            background: white;
            border-radius: 0.875rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 200ms;
            display: flex;
            flex-direction: column;
        }

        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .role-header {
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .role-header.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .role-header.blue {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        }

        .role-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .role-body h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #111827;
        }

        .role-body p {
            color: #4b5563;
            margin-bottom: 1.5rem;
        }

        .role-list {
            list-style: none;
            margin-bottom: 1.5rem;
            flex: 1;
        }

        .role-list li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            color: #374151;
            font-size: 0.9rem;
        }

        .role-list i {
            color: #667eea;
            min-width: 16px;
        }

        .role-body .btn {
            width: 100%;
            justify-content: center;
        }

        /* CTA Section */
        .cta-section {
            padding: 4rem 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            color: white;
        }

        .cta-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }

        .btn-white {
            background: white;
            color: #667eea;
        }

        .btn-white:hover {
            background: #f3f4f6;
        }

        /* Footer */
        .footer {
            background: #111827;
            color: #9ca3af;
            padding: 4rem 1rem 2rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .footer-col h4 {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col li {
            margin-bottom: 0.75rem;
        }

        .footer-col a {
            color: #9ca3af;
            transition: color 200ms;
        }

        .footer-col a:hover {
            color: #667eea;
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            padding-top: 2rem;
            text-align: center;
            max-width: 1400px;
            margin: 0 auto;
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }

            .section-header h2 {
                font-size: 1.75rem;
            }

            .cta-section h2 {
                font-size: 1.75rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .cta-buttons .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <!-- Navbar is now rendered from templates/header.php -->

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="badge">
                    <i class="fas fa-star"></i> Solusi Terdepan untuk PKL
                </div>
                <h1>Kelola <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Jurnal PKL</span> Anda</h1>
                <p class="hero-subtitle">
                    Platform enterprise-grade untuk siswa melaporkan dan pembimbing mengevaluasi jurnal Praktik Kerja Lapangan dengan efisiensi maksimal.
                </p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket"></i> Mulai Sekarang
                    </a>
                    <a href="login.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Akses Pembimbing
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Pengguna Aktif</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Jurnal Tercatat</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Section -->
    <section class="tentang-section" id="tentang">
        <div class="container">
            <div class="section-header">
                <div class="badge">Tentang Platform</div>
                <h2>Mengapa Memilih E-Jurnal PKL?</h2>
                <p>Solusi komprehensif yang dirancang khusus untuk kebutuhan manajemen Praktik Kerja Lapangan modern</p>
            </div>
            <div class="tentang-grid">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-pen"></i>
                    </div>
                    <h3>Pencatatan Mudah</h3>
                    <p>Form yang intuitif dan responsif untuk mencatat kegiatan harian PKL dengan detail lengkap.</p>
                </div>
                <div class="card">
                    <div class="card-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Review Profesional</h3>
                    <p>Sistem evaluasi terstruktur dengan feedback real-time dari pembimbing Anda.</p>
                </div>
                <div class="card">
                    <div class="card-icon blue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics Mendalam</h3>
                    <p>Dashboard analytics lengkap untuk memantau perkembangan dan progress PKL Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Section -->
    <section class="fitur-section" id="fitur">
        <div class="container">
            <div class="section-header">
                <div class="badge">Fitur Unggulan</div>
                <h2>Dilengkapi Fitur-Fitur Canggih</h2>
                <p>Semua yang Anda butuhkan untuk mengelola PKL dengan profesional</p>
            </div>
            <div class="fitur-grid">
                <div class="fitur-card">
                    <div class="fitur-icon">🎯</div>
                    <h4>Interface Intuitif</h4>
                    <p>Desain yang mudah digunakan untuk semua kalangan</p>
                </div>
                <div class="fitur-card">
                    <div class="fitur-icon">🔐</div>
                    <h4>Keamanan Enterprise</h4>
                    <p>Enkripsi data dan proteksi berlapis</p>
                </div>
                <div class="fitur-card">
                    <div class="fitur-icon">📱</div>
                    <h4>Fully Responsive</h4>
                    <p>Sempurna di semua device dan ukuran layar</p>
                </div>
                <div class="fitur-card">
                    <div class="fitur-icon">⚡</div>
                    <h4>Real-Time Updates</h4>
                    <p>Notifikasi instan untuk setiap update</p>
                </div>
                <div class="fitur-card">
                    <div class="fitur-icon">🤝</div>
                    <h4>Kolaborasi Seamless</h4>
                    <p>Interaksi lancar antara siswa dan pembimbing</p>
                </div>
                <div class="fitur-card">
                    <div class="fitur-icon">📊</div>
                    <h4>Advanced Analytics</h4>
                    <p>Visualisasi data yang komprehensif</p>
                </div>
                <div class="fitur-card">
                    <div class="fitur-icon">☁️</div>
                    <h4>Cloud Based</h4>
                    <p>Akses dari mana saja kapan saja</p>
                </div>
                <div class="fitur-card">
                    <div class="fitur-icon">🌙</div>
                    <h4>Mode Gelap</h4>
                    <p>Desain eye-friendly untuk kenyamanan maksimal</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Role Section -->
    <section class="role-section" id="role">
        <div class="container">
            <div class="section-header">
                <div class="badge">Pilih Peran Anda</div>
                <h2>Bergabung Sesuai Peran Anda</h2>
                <p>Akses yang disesuaikan untuk setiap jenis pengguna</p>
            </div>
            <div class="role-grid">
                <!-- Siswa -->
                <div class="role-card">
                    <div class="role-header">👨‍🎓</div>
                    <div class="role-body">
                        <h3>Siswa</h3>
                        <p>Kelola laporan aktivitas PKL Anda dengan mudah dan profesional.</p>
                        <ul class="role-list">
                            <li><i class="fas fa-check"></i> Buat jurnal harian</li>
                            <li><i class="fas fa-check"></i> Kelola riwayat lengkap</li>
                            <li><i class="fas fa-check"></i> Terima feedback langsung</li>
                            <li><i class="fas fa-check"></i> Lihat analytics progress</li>
                        </ul>
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Daftar Sebagai Siswa
                        </a>
                    </div>
                </div>

                <!-- Pembimbing -->
                <div class="role-card">
                    <div class="role-header green">👨‍🏫</div>
                    <div class="role-body">
                        <h3>Pembimbing</h3>
                        <p>Pantau dan evaluasi perkembangan siswa Anda secara real-time.</p>
                        <ul class="role-list">
                            <li><i class="fas fa-check" style="color: #10b981;"></i> Review jurnal siswa</li>
                            <li><i class="fas fa-check" style="color: #10b981;"></i> Berikan feedback detail</li>
                            <li><i class="fas fa-check" style="color: #10b981;"></i> Pantau progress siswa</li>
                            <li><i class="fas fa-check" style="color: #10b981;"></i> Kelola daftar siswa</li>
                        </ul>
                        <a href="login.php" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);">
                            <i class="fas fa-sign-in-alt"></i> Masuk Sebagai Pembimbing
                        </a>
                    </div>
                </div>

                <!-- Admin -->
                <div class="role-card">
                    <div class="role-header blue">👨‍💼</div>
                    <div class="role-body">
                        <h3>Administrator</h3>
                        <p>Kelola sistem dan pengguna dengan dashboard manajemen canggih.</p>
                        <ul class="role-list">
                            <li><i class="fas fa-check" style="color: #3b82f6;"></i> Kelola semua pengguna</li>
                            <li><i class="fas fa-check" style="color: #3b82f6;"></i> Posting pengumuman</li>
                            <li><i class="fas fa-check" style="color: #3b82f6;"></i> Monitor sistem real-time</li>
                            <li><i class="fas fa-check" style="color: #3b82f6;"></i> Akses analytics lengkap</li>
                        </ul>
                        <a href="login.php" class="btn btn-primary" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
                            <i class="fas fa-sign-in-alt"></i> Masuk Sebagai Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <h2>Siap Memulai Perjalanan Anda?</h2>
        <p>Bergabunglah dengan ribuan siswa dan pembimbing yang telah merasakan manfaat E-Jurnal PKL</p>
        <div class="cta-buttons">
            <a href="register.php" class="btn btn-lg btn-white">
                <i class="fas fa-rocket"></i> Daftar Sekarang
            </a>
            <a href="login.php" class="btn btn-lg" style="background: transparent; border: 2px solid white; color: white;">
                <i class="fas fa-sign-in-alt"></i> Sudah Punya Akun?
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span style="font-weight: 700; color: white; font-size: 1.1rem;">E-Jurnal PKL</span>
                </div>
                <p style="margin-bottom: 1rem;">Platform manajemen jurnal PKL yang modern, aman, dan terpercaya untuk institusi pendidikan.</p>
                <div style="display: flex; gap: 1rem;">
                    <a href="#" style="color: #9ca3af; transition: color 200ms;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color: #9ca3af; transition: color 200ms;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color: #9ca3af; transition: color 200ms;"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Untuk Siswa</h4>
                <ul>
                    <li><a href="register.php">Daftar</a></li>
                    <li><a href="login.php">Masuk</a></li>
                    <li><a href="#">Panduan Pengguna</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Untuk Pembimbing</h4>
                <ul>
                    <li><a href="login.php">Masuk</a></li>
                    <li><a href="#">Dokumentasi</a></li>
                    <li><a href="#">Best Practices</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Lainnya</h4>
                <ul>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Syarat Layanan</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 E-Jurnal PKL. All rights reserved. | Made with <i class="fas fa-heart" style="color: #ef4444;"></i> by Professional Web Development Team</p>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

<?php include '../templates/footer.php'; ?>
