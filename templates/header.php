<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="E-Jurnal PKL - Platform Manajemen Jurnal Praktik Kerja Lapangan">
    <meta name="theme-color" content="#667eea">
    <title>E-Jurnal PKL - Platform Manajemen Jurnal PKL</title>
    
    <?php
    // Compute public root path so asset links resolve correctly when included from subfolders
    $script = str_replace('\\','/', $_SERVER['SCRIPT_NAME'] ?? '');
    $pos = strpos($script, '/public');
    $public_root = $pos !== false ? substr($script, 0, $pos + 7) : '';
    ?>

    <!-- Tailwind CSS v3 CDN -->
    <script>
        tailwind.config = {
            theme: {
                extend: {}
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@heroicons/react@2/dist/index.min.js"></script>
    
    <!-- Font Awesome untuk Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Premium CSS System -->
    <link href="<?php echo $public_root; ?>/assets/css/premium.css" rel="stylesheet">
    <!-- Modern CSS -->
    <link href="<?php echo $public_root; ?>/assets/css/modern.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $public_root; ?>/assets/css/style.css" rel="stylesheet">
    
    <!-- Alpine JS untuk interaktivitas -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js untuk visualisasi data -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>
    
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5568d3;
            --secondary: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
    </style>
    
    <style>
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .btn-modern {
            transition: all 0.3s ease;
        }
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
    </head>
    <body class="bg-gradient-to-br from-slate-50 via-white to-slate-100 text-slate-900 antialiased">

        <?php
        if (session_status() == PHP_SESSION_NONE) session_start();
        // Compute public root path (up through /public) so links work from nested scripts
        $script = str_replace('\\','/', $_SERVER['SCRIPT_NAME'] ?? '');
        $pos = strpos($script, '/public');
        $public_root = $pos !== false ? substr($script, 0, $pos + 7) : '';
        $home_link = $public_root . '/index.php';
        $register_link = $public_root . '/register.php';
        $login_link = $public_root . '/login.php';
        $logout_link = $public_root . '/logout.php';
        ?>

        <!-- Shared Navbar (rendered from templates/header.php) -->
        <nav class="navbar">
            <div class="navbar-content">
                <a href="<?php echo $home_link; ?>" class="navbar-brand">
                    <div class="navbar-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span>E-Jurnal PKL</span>
                </a>
                <div class="navbar-buttons">
                    <a href="<?php echo $register_link; ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Daftar
                    </a>
                </div>
            </div>
        </nav>


