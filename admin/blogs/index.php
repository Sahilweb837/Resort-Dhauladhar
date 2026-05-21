<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$blogs = getAllBlogs();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs - Resort Admin Panel</title>
    <!-- Montserrat & Cormorant Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=Montserrat:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php include_once __DIR__ . '/../includes/admin_styles.php'; ?>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Glassmorphism Top Header -->
            <div class="header">
                <h1><i class="fas fa-newspaper"></i> Manage Blogs</h1>
                <div style="display:flex; align-items:center; gap: 15px;">
                    <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create New Blog</a>
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></strong></span>
                    </div>
                </div>
            </div>
            
            <!-- Success/Feedback Alerts -->
            <?php if ($msg == 'created'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> Blog post created successfully!</div>
            <?php elseif ($msg == 'updated'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> Blog post updated successfully!</div>
            <?php elseif ($msg == 'deleted'): ?>
                <div class="alert alert-danger"><i class="fas fa-trash-alt"></i> Blog post deleted successfully!</div>
            <?php endif; ?>
            
            <!-- Premium Dark Table Container -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th style="width: 80px;">Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th style="width: 150px;">Status</th>
                            <th>Date Published</th>
                            <th style="width: 200px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blogs)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px 0;">
                                    <i class="fas fa-folder-open" style="font-size: 32px; margin-bottom: 12px; display: block; color: var(--border);"></i>
                                    No blogs found. Start by creating a new post!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($blogs as $blog): ?>
                            <tr>
                                <td style="font-family: monospace; font-size: 13px; color: var(--primary-light);"><?php echo $blog['id']; ?></td>
                                <td>
                                    <?php if (!empty($blog['featured_image'])): ?>
                                        <img src="<?php echo getBlogImageUrl($blog['featured_image']); ?>" style="width: 48px; height: 36px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);" alt="Blog Image">
                                    <?php else: ?>
                                        <div style="width: 48px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.02); border-radius: 6px; border: 1px solid var(--border); color: var(--text-muted); font-size: 12px;"><i class="fas fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 500; color: #fff;"><?php echo htmlspecialchars($blog['title']); ?></td>
                                <td><span style="background: rgba(255,255,255,0.03); padding: 4px 8px; border-radius: 4px; font-size: 13px; border: 1px solid var(--border);"><?php echo htmlspecialchars($blog['category'] ?? 'Uncategorized'); ?></span></td>
                                <td><span class="status-badge status-<?php echo $blog['status']; ?>"><?php echo ucfirst($blog['status']); ?></span></td>
                                <td style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 8px; justify-content: flex-end;">
                                        <a href="edit.php?id=<?php echo $blog['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="delete.php?id=<?php echo $blog['id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Are you sure you want to delete this blog post?')"><i class="fas fa-trash"></i> Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
