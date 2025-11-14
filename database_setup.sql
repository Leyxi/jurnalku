-- Database Setup for PKL Hero Hub v2.0
-- Run this script in phpMyAdmin or MySQL command line

-- Create database
CREATE DATABASE IF NOT EXISTS pkl_hero_db;
USE pkl_hero_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pembimbing', 'siswa') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Jurnal Harian table
CREATE TABLE jurnal_harian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT NOT NULL,
    tanggal_kegiatan DATE NOT NULL,
    deskripsi_kegiatan TEXT NOT NULL,
    kendala TEXT,
    solusi TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    komentar_pembimbing TEXT,
    tanggal_review TIMESTAMP NULL DEFAULT NULL,
    nilai_apresiasi INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_siswa) REFERENCES users(id) ON DELETE CASCADE
);

-- Jurnal Foto table
CREATE TABLE jurnal_foto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_jurnal INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jurnal) REFERENCES jurnal_harian(id) ON DELETE CASCADE
);

-- Relasi Bimbingan table
CREATE TABLE relasi_bimbingan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pembimbing INT NOT NULL,
    id_siswa INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pembimbing) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_siswa) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_relasi (id_pembimbing, id_siswa)
);

-- Audit table for relation changes
CREATE TABLE IF NOT EXISTS relasi_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_type VARCHAR(50) NOT NULL,
    performed_by INT NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Pengumuman table
CREATE TABLE pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    target_audien ENUM('all', 'siswa', 'pembimbing') NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert test users
-- Admin account
INSERT INTO users (nama_lengkap, email, password, role) VALUES
('Admin PKL', 'admin@pklhero.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Pembimbing accounts
INSERT INTO users (nama_lengkap, email, password, role) VALUES
('Pak Ahmad Pembimbing', 'ahmad@pembimbing.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pembimbing'),
('Bu Siti Pembimbing', 'siti@pembimbing.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pembimbing');

-- Siswa accounts
INSERT INTO users (nama_lengkap, email, password, role) VALUES
('Ahmad Siswa', 'ahmad@siswa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'siswa'),
('Budi Siswa', 'budi@siswa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'siswa'),
('Citra Siswa', 'citra@siswa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'siswa');

-- Relasi Bimbingan
-- Pak Ahmad membimbing Ahmad dan Budi
INSERT INTO relasi_bimbingan (id_pembimbing, id_siswa) VALUES
(2, 4), -- Ahmad pembimbing -> Ahmad siswa
(2, 5); -- Ahmad pembimbing -> Budi siswa

-- Bu Siti membimbing Citra
INSERT INTO relasi_bimbingan (id_pembimbing, id_siswa) VALUES
(3, 6); -- Siti pembimbing -> Citra siswa

-- Sample Pengumuman
INSERT INTO pengumuman (judul, isi, target_audien, created_by) VALUES
('Selamat Datang di PKL Hero Hub v2.0', 'Selamat datang di sistem manajemen PKL terbaru. Pastikan untuk mengisi jurnal harian setiap hari.', 'all', 1),
('Pengingat untuk Siswa', 'Jangan lupa upload foto bukti kegiatan PKL setiap hari.', 'siswa', 1),
('Info untuk Pembimbing', 'Silakan review jurnal siswa yang masih pending setiap hari.', 'pembimbing', 1);

-- Sample Jurnal (untuk testing)
INSERT INTO jurnal_harian (id_siswa, tanggal_kegiatan, deskripsi_kegiatan, kendala, solusi, status) VALUES
(4, '2024-01-15', 'Hari pertama PKL di perusahaan software. Belajar tentang HTML dan CSS.', 'Kesulitan memahami flexbox', 'Bertanya ke senior dan mencari tutorial online', 'approved'),
(4, '2024-01-16', 'Melanjutkan belajar CSS dan mulai JavaScript dasar.', NULL, NULL, 'pending'),
(5, '2024-01-15', 'Orientasi di perusahaan dan pengenalan tools development.', 'Belum familiar dengan Git', 'Mengikuti tutorial Git di YouTube', 'approved'),
(6, '2024-01-15', 'Belajar tentang database MySQL dan PHP.', 'Error koneksi database', 'Cek konfigurasi XAMPP dan port MySQL', 'pending');

-- Sample Foto Jurnal (path contoh)
INSERT INTO jurnal_foto (id_jurnal, file_path) VALUES
(1, 'sample_foto1.jpg'),
(1, 'sample_foto2.jpg'),
(3, 'sample_foto3.jpg');

-- Password untuk semua akun testing: password
-- (Hash di atas adalah bcrypt hash untuk "password")
