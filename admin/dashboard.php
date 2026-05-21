<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

// Fetch dashboard stats
$stats = getDashboardStats();
$recentBlogs = getRecentBlogs(5);
$adminName = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Dhauladhar Resort Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary: #1D285C;
            --primary-light: #5DC5E3;
            --primary-dark: #0B162C;
            --accent: #cf9a2c;
            --accent-glow: rgba(207,154,44,0.25);
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #e17055;
            --dark: #0f1a2e;
            --darker: #0a1322;
            --darkest: #060e1a;
            --surface: #0f1a2e;
            --surface-light: #162340;
            --text: #cdd6f4;
            --text-muted: #6c7086;
            --border: rgba(255,255,255,0.06);
            --sidebar-w: 270px;
            --radius: 14px;
            --shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--darkest);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--darker);
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 28px 24px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .sidebar-brand h2 {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .sidebar-brand small {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .sidebar-nav { flex: 1; padding: 16px 0; }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 24px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all .25s ease;
            border-left: 3px solid transparent;
            margin: 2px 0;
        }

        .sidebar-nav a i { width: 20px; text-align: center; font-size: 16px; }

        .sidebar-nav a:hover {
            color: var(--text);
            background: rgba(108,92,231,0.08);
            border-left-color: var(--primary-light);
        }

        .sidebar-nav a.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(108,92,231,0.2), transparent);
            border-left-color: var(--primary);
        }

        .sidebar-nav a.active i { color: var(--primary-light); }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--danger);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            opacity: .8;
            transition: opacity .2s;
        }

        .sidebar-footer a:hover { opacity: 1; }

        /* ========== MAIN ========== */
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            padding: 0;
        }

        /* Top Bar */
        .topbar {
            padding: 20px 36px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            background: var(--darker);
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(16px);
        }

        .topbar h1 {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .admin-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            color: #fff;
        }

        .admin-info span {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        .admin-info small {
            font-size: 11px;
            color: var(--text-muted);
            display: block;
        }

        /* Content */
        .content { padding: 32px 36px; }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, #1a3a5c 50%, var(--primary-light) 100%);
            border-radius: var(--radius);
            padding: 36px 40px;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%; right: -20%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -60%; left: 10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(0,206,201,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .welcome-banner h2 {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .welcome-banner p {
            color: rgba(255,255,255,0.8);
            font-size: 15px;
            position: relative;
            z-index: 1;
        }

        .welcome-banner .date-badge {
            position: absolute;
            top: 28px; right: 32px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 50px;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            z-index: 1;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            transition: all .3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
            border-color: rgba(108,92,231,0.3);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 0 0 0 80px;
            opacity: 0.08;
        }

        .stat-card:nth-child(1)::after { background: var(--primary); }
        .stat-card:nth-child(2)::after { background: var(--success); }
        .stat-card:nth-child(3)::after { background: var(--warning); }
        .stat-card:nth-child(4)::after { background: var(--accent); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
        }

        .stat-card:nth-child(1) .stat-icon { background: rgba(108,92,231,0.15); color: var(--primary-light); }
        .stat-card:nth-child(2) .stat-icon { background: rgba(0,184,148,0.15); color: var(--success); }
        .stat-card:nth-child(3) .stat-icon { background: rgba(253,203,110,0.15); color: var(--warning); }
        .stat-card:nth-child(4) .stat-icon { background: rgba(0,206,201,0.15); color: var(--accent); }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        .card-header a {
            color: var(--primary-light);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .card-body { padding: 20px 24px; }

        /* Recent Blogs Table */
        .blogs-table { width: 100%; border-collapse: collapse; }

        .blogs-table th {
            text-align: left;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--border);
        }

        .blogs-table td {
            padding: 14px 12px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .blogs-table tr:last-child td { border-bottom: none; }

        .blogs-table tr:hover td { background: rgba(108,92,231,0.04); }

        .blog-title-cell {
            font-weight: 600;
            color: #fff;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-published { background: rgba(0,184,148,0.15); color: var(--success); }
        .badge-draft { background: rgba(253,203,110,0.15); color: var(--warning); }

        .table-action {
            color: var(--primary-light);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            margin-right: 10px;
            transition: color .2s;
        }

        .table-action:hover { color: #fff; }
        .table-action.delete { color: var(--danger); }

        /* Quick Actions */
        .quick-actions { display: flex; flex-direction: column; gap: 12px; }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            background: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all .25s ease;
        }

        .action-btn:hover {
            background: rgba(108,92,231,0.1);
            border-color: var(--primary);
            transform: translateX(4px);
            color: #fff;
        }

        .action-btn i {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .action-btn:nth-child(1) i { background: rgba(108,92,231,0.15); color: var(--primary-light); }
        .action-btn:nth-child(2) i { background: rgba(0,184,148,0.15); color: var(--success); }
        .action-btn:nth-child(3) i { background: rgba(0,206,201,0.15); color: var(--accent); }
        .action-btn:nth-child(4) i { background: rgba(225,112,85,0.15); color: var(--danger); }

        .action-btn .arrow {
            margin-left: auto;
            color: var(--text-muted);
            font-size: 12px;
            transition: transform .2s;
        }

        .action-btn:hover .arrow { transform: translateX(4px); color: var(--primary-light); }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i { font-size: 40px; margin-bottom: 12px; opacity: .4; }
        .empty-state p { font-size: 14px; }

        /* Footer */
        .dashboard-footer {
            text-align: center;
            padding: 24px;
            color: var(--text-muted);
            font-size: 12px;
            border-top: 1px solid var(--border);
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .welcome-banner { animation: fadeInUp .5s ease; }
        .stat-card:nth-child(1) { animation: fadeInUp .5s ease .1s both; }
        .stat-card:nth-child(2) { animation: fadeInUp .5s ease .2s both; }
        .stat-card:nth-child(3) { animation: fadeInUp .5s ease .3s both; }
        .stat-card:nth-child(4) { animation: fadeInUp .5s ease .4s both; }
        .dashboard-grid { animation: fadeInUp .5s ease .5s both; }

        /* Mobile Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .topbar, .content { padding-left: 20px; padding-right: 20px; }
            .menu-toggle { display: block; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .welcome-banner .date-badge { display: none; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="../images/dhr_logo_full_white_720.png" alt="Dhauladhar Heights Resort" style="height:50px;margin-bottom:8px;">
            <small>Resort Admin Panel</small>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="active">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="blogs/index.php">
                <i class="fas fa-newspaper"></i> All Blogs
            </a>
            <a href="blogs/create.php">
                <i class="fas fa-pen-fancy"></i> Create Blog
            </a>
            <a href="reviews/index.php">
                <i class="fas fa-star"></i> Manage Reviews
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php"><i class="fas fa-arrow-right-from-bracket"></i> Sign Out</a>
        </div>
    </aside>

    <!-- Main -->
    <div class="main">
        <!-- Top Bar -->
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Dashboard</h1>
            </div>
            <div class="topbar-right">
                <div class="admin-info" style="text-align:right;">
                    <span><?php echo $adminName; ?></span>
                    <small>Administrator</small>
                </div>
                <div class="admin-avatar"><?php echo strtoupper(substr($adminName, 0, 1)); ?></div>
            </div>
        </header>

        <!-- Content -->
        <div class="content">

            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <h2>Welcome back, <?php echo $adminName; ?>! 👋</h2>
                <p>Here's an overview of your resort blog and content management.</p>
                <div class="date-badge">
                    <i class="fas fa-calendar-day"></i>&nbsp;
                    <?php echo date('l, F j, Y'); ?>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="stat-label">Total Blogs</div>
                    <div class="stat-value"><?php echo $stats['total_blogs'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-label">Published</div>
                    <div class="stat-value"><?php echo $stats['published_blogs'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-pencil-alt"></i></div>
                    <div class="stat-label">Drafts</div>
                    <div class="stat-value"><?php echo $stats['draft_blogs'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-tags"></i></div>
                    <div class="stat-label">Categories</div>
                    <div class="stat-value">
                        <?php
                        try {
                            $cats = getCategories();
                            echo count($cats);
                        } catch (Exception $e) {
                            echo '0';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Grid: Recent Blogs + Quick Actions -->
            <div class="dashboard-grid">
                <!-- Recent Blogs -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-clock" style="color:var(--primary-light);margin-right:8px;"></i> Recent Blogs</h3>
                        <a href="blogs/index.php">View All →</a>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <?php if (!empty($recentBlogs)): ?>
                        <table class="blogs-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBlogs as $blog): ?>
                                <tr>
                                    <td class="blog-title-cell"><?php echo htmlspecialchars($blog['title']); ?></td>
                                    <td><span style="color:var(--text-muted);font-size:13px;"><?php echo htmlspecialchars($blog['category'] ?? '—'); ?></span></td>
                                    <td>
                                        <span class="badge badge-<?php echo $blog['status']; ?>">
                                            <?php echo ucfirst($blog['status']); ?>
                                        </span>
                                    </td>
                                    <td style="color:var(--text-muted);font-size:13px;white-space:nowrap;">
                                        <?php echo date('M d, Y', strtotime($blog['created_at'])); ?>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        <a href="blogs/edit.php?id=<?php echo $blog['id']; ?>" class="table-action"><i class="fas fa-edit"></i></a>
                                        <a href="blogs/delete.php?id=<?php echo $blog['id']; ?>" class="table-action delete" onclick="return confirm('Delete this blog?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No blogs yet. Create your first blog post!</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-bolt" style="color:var(--warning);margin-right:8px;"></i> Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="blogs/create.php" class="action-btn">
                                <i class="fas fa-plus"></i>
                                <span>Create New Blog</span>
                                <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                            </a>
                            <a href="blogs/index.php" class="action-btn">
                                <i class="fas fa-list-ul"></i>
                                <span>Manage All Blogs</span>
                                <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                            </a>
                            <a href="../index.php" target="_blank" class="action-btn">
                                <i class="fas fa-external-link-alt"></i>
                                <span>View Website</span>
                                <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                            </a>
                            <a href="logout.php" class="action-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Sign Out</span>
                                <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="dashboard-footer">
            &copy; <?php echo date('Y'); ?> Dhauladhar Resort — Admin Panel. All rights reserved.
        </div>
    </div>

</body>
</html>