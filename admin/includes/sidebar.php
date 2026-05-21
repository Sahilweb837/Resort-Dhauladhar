<?php
// Get the current page filename to determine active class and paths
$current_page = basename($_SERVER['SCRIPT_NAME']);
$current_dir = basename(dirname($_SERVER['SCRIPT_NAME']));
$in_subdir = ($current_dir !== 'admin');
$prefix = $in_subdir ? '../' : '';
$images_prefix = $in_subdir ? '../../' : '../';
?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="<?php echo $images_prefix; ?>images/dhr_logo_full_white_720.png" alt="Dhauladhar Heights Resort" style="height:50px;margin-bottom:8px;object-fit:contain;">
        <small style="display:block;font-size:11px;color:var(--text-muted,#6c7086);text-transform:uppercase;letter-spacing:2px;margin-top:4px;">Resort Admin Panel</small>
    </div>

    <nav class="sidebar-nav" style="flex:1;padding:16px 0;display:flex;flex-direction:column;gap:4px;">
        <a href="<?php echo $prefix; ?>dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> <span>Dashboard</span>
        </a>
        <a href="<?php echo $prefix; ?>blogs/index.php" class="<?php echo ($current_page == 'index.php' && $current_dir == 'blogs') || $current_page == 'edit.php' && $current_dir == 'blogs' ? 'active' : ''; ?>">
            <i class="fas fa-newspaper"></i> <span>All Blogs</span>
        </a>
        <a href="<?php echo $prefix; ?>blogs/create.php" class="<?php echo $current_page == 'create.php' && $current_dir == 'blogs' ? 'active' : ''; ?>">
            <i class="fas fa-pen-fancy"></i> <span>Create Blog</span>
        </a>
        <a href="<?php echo $prefix; ?>reviews/index.php" class="<?php echo ($current_page == 'index.php' && $current_dir == 'reviews') || $current_page == 'edit.php' && $current_dir == 'reviews' ? 'active' : ''; ?>">
            <i class="fas fa-star"></i> <span>Reviews</span>
        </a>
        <a href="<?php echo $prefix; ?>reviews/create.php" class="<?php echo $current_page == 'create.php' && $current_dir == 'reviews' ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle"></i> <span>Add Review</span>
        </a>
        <a href="<?php echo $prefix; ?>logout.php" style="margin-top:auto; border-top:1px solid var(--border,rgba(255,255,255,0.06));">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </nav>
</aside>
