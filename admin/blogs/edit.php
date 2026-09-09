<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$error = '';
$success = '';

// Get blog by ID
$blog = null;
if (isset($_GET['id'])) {
    $blog = getBlogById($_GET['id']);
    if (!$blog) {
        $adminBase = function_exists('getBaseUrl') ? getBaseUrl() . '/admin/' : '../';
        session_write_close();
        header('Location: ' . $adminBase . 'blogs/index.php?msg=error');
        exit();
    }
} else {
    $adminBase = function_exists('getBaseUrl') ? getBaseUrl() . '/admin/' : '../';
    session_write_close();
    header('Location: ' . $adminBase . 'blogs/index.php');
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$categories = getCategories();

// Decode sections if they exist
$sections = [];
if (!empty($blog['sections'])) {
    $sections = json_decode($blog['sections'], true);
}
// If blog has raw content but no sections JSON (e.g. seeded blogs or legacy format),
// populate default section from content so it NEVER wipes or displays blank!
if (empty($sections) && !empty($blog['content'])) {
    $sections = [
        [
            'type' => 'text',
            'heading' => 'Overview & Story',
            'level' => 'h2',
            'content' => $blog['content'],
            'order' => 0
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slugInput = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author = trim($_POST['author'] ?? ($_SESSION['admin_name'] ?? 'Admin'));
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords = trim($_POST['meta_keywords'] ?? '');
    $status = $_POST['status'] ?? ($blog['status'] ?? 'published');
    
    // Handle custom category
    if ($category === 'other' && !empty($_POST['category_custom'])) {
        $category = trim($_POST['category_custom']);
    }
    
    // Process all dynamic multi-type sections
    $rawSections = $_POST['sections'] ?? [];
    $updatedSections = processSubmittedBlogSections($rawSections, $_FILES['sections'] ?? null);
    if (!empty($updatedSections)) {
        $sections = $updatedSections;
    }
    
    // Retain entered values in case of validation error
    $blog['title'] = $title;
    $blog['slug'] = $slugInput;
    $blog['excerpt'] = $excerpt;
    $blog['category'] = $category;
    $blog['author'] = $author;
    $blog['meta_description'] = $meta_description;
    $blog['meta_keywords'] = $meta_keywords;
    $blog['status'] = $status;

    // Validate
    if (empty($title)) {
        $error = "Title is required.";
    } elseif (strlen($title) < 3) {
        $error = "Title must be at least 3 characters.";
    } elseif (empty($updatedSections)) {
        $error = "Please add at least one section.";
    } else {
        $featured_image = $blog['featured_image'];
        
        // Handle new featured image upload
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            if ($_FILES['featured_image']['size'] > 5 * 1024 * 1024) {
                $error = "Image size must be less than 5MB.";
            } else {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $file_type = mime_content_type($_FILES['featured_image']['tmp_name']);
                
                if (!in_array($file_type, $allowed_types)) {
                    $error = "Only JPG, PNG, GIF, and WEBP images are allowed.";
                } else {
                    $upload_result = uploadImage($_FILES['featured_image']);
                    if ($upload_result) {
                        if (!empty($featured_image)) {
                            deleteImage($featured_image);
                        }
                        $featured_image = $upload_result;
                    } else {
                        $error = "Failed to upload image.";
                    }
                }
            }
        }
        
        // Remove featured image if requested
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            if (!empty($featured_image)) {
                deleteImage($featured_image);
            }
            $featured_image = '';
        }
        
        if (empty($error)) {
            // Auto-generate excerpt if empty
            if (empty($excerpt)) {
                $textParts = [];
                foreach ($updatedSections as $s) {
                    if (!empty($s['content'])) $textParts[] = strip_tags($s['content']);
                    if (!empty($s['body'])) $textParts[] = strip_tags($s['body']);
                    if (!empty($s['quote'])) $textParts[] = strip_tags($s['quote']);
                }
                $combined = implode(' ', $textParts);
                $excerpt = substr($combined, 0, 200) . (strlen($combined) > 200 ? '...' : '');
            }
            
            // Compile sections to standalone semantic HTML for content column
            $compiledHtml = compileBlogSectionsToHtml($updatedSections, $blog['content'] ?? '');
            
            $data = [
                'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                'slug' => !empty($slugInput) ? createSlug($slugInput) : (!empty($blog['slug']) ? $blog['slug'] : createSlug($title)),
                'content' => $compiledHtml,
                'excerpt' => htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8'),
                'featured_image' => $featured_image,
                'category' => htmlspecialchars($category, ENT_QUOTES, 'UTF-8'),
                'author' => htmlspecialchars($author, ENT_QUOTES, 'UTF-8'),
                'meta_description' => htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8'),
                'meta_keywords' => htmlspecialchars($meta_keywords, ENT_QUOTES, 'UTF-8'),
                'status' => $status,
                'sections' => json_encode($updatedSections, JSON_UNESCAPED_UNICODE),
                'content_format' => 'html'
            ];
            
            if (updateBlog($blog['id'], $data)) {
                $adminBase = function_exists('getBaseUrl') ? getBaseUrl() . '/admin/' : '../';
                session_write_close();
                header('Location: ' . $adminBase . 'blogs/index.php?msg=updated');
                exit();
            } else {
                $error = "Failed to update blog. Please try again.";
            }
        }
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - Resort Admin Panel</title>
    <link rel="icon" type="image/png" href="../../images/dhr_logo_icon.png">
    <!-- Montserrat & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php include_once __DIR__ . '/../includes/admin_styles.php'; ?>
    <style>
        .sections-container {
            background: rgba(10, 19, 34, 0.6);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            margin-top: 10px;
        }

        .add-section-toolbar {
            background: rgba(22, 35, 64, 0.7);
            border: 1px solid rgba(93, 197, 227, 0.25);
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .add-section-toolbar .add-label {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--primary-light);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .add-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-add-sec {
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 9px 15px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.22s ease;
        }

        .btn-add-sec i {
            color: var(--primary-light);
        }

        .btn-add-sec:hover {
            background: rgba(93, 197, 227, 0.15);
            border-color: var(--primary-light);
            color: var(--primary-light);
            transform: translateY(-2px);
        }

        /* Section Item Card */
        .section-item {
            background: rgba(15, 26, 46, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .section-item.collapsed .section-body {
            display: none;
        }

        .section-item-header {
            background: rgba(22, 35, 64, 0.9);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            user-select: none;
        }

        .sec-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .sec-drag-icon {
            color: var(--text-muted);
            font-size: 14px;
        }

        .sec-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(93, 197, 227, 0.15);
            color: var(--primary-light);
            border: 1px solid rgba(93, 197, 227, 0.3);
            white-space: nowrap;
        }

        .sec-title-preview {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sec-header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-sec-ctrl {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .btn-sec-ctrl:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .btn-sec-ctrl.btn-danger:hover {
            background: var(--danger);
            border-color: var(--danger);
            color: #fff;
        }

        .section-body {
            padding: 22px;
        }

        /* Formatting Toolbar */
        .fmt-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 8px;
            padding: 6px 10px;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border);
            border-radius: 6px;
        }

        .fmt-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .fmt-btn:hover {
            background: var(--primary-light);
            color: var(--darker);
        }

        /* Dynamic Features Checklist */
        .checklist-builder {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .checklist-item-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checklist-item-row input {
            flex: 1;
        }

        .btn-remove-item {
            background: rgba(225, 112, 85, 0.15);
            color: #ff7675;
            border: 1px solid rgba(225, 112, 85, 0.3);
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-remove-item:hover {
            background: var(--danger);
            color: #fff;
        }

        .btn-add-item {
            background: rgba(93, 197, 227, 0.1);
            color: var(--primary-light);
            border: 1px dashed var(--primary-light);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            align-self: flex-start;
            margin-top: 6px;
            transition: all 0.2s;
        }

        .btn-add-item:hover {
            background: var(--primary-light);
            color: var(--darker);
            border-style: solid;
        }

        .current-image {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .current-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--border);
        }

        .current-image label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
            margin: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .char-counter {
            font-size: 12px;
            color: var(--text-muted);
            text-align: right;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <?php include_once __DIR__ . '/../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Glassmorphism Top Header -->
            <div class="header">
                <h1><i class="fas fa-edit"></i> Edit Blog</h1>
                <div style="display:flex; align-items:center; gap: 12px;">
                    <a href="builder.php?id=<?php echo $blog['id']; ?>" class="btn btn-primary" style="background:linear-gradient(135deg,#5DC5E3 0%,#1D285C 100%); border:1px solid rgba(93,197,227,0.4);"><i class="fas fa-magic"></i> Live Preview Builder</a>
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></strong></span>
                    </div>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data" id="blogForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="form-group">
                        <label><i class="fas fa-heading"></i> <span class="required">Blog Title</span></label>
                        <input type="text" name="title" id="titleInput" required value="<?php echo htmlspecialchars($blog['title']); ?>" placeholder="Enter blog title" maxlength="200">
                        <div class="char-counter"><?php echo strlen($blog['title']); ?>/200 characters</div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-link"></i> Blog Slug (SEO Clean URL)</label>
                        <input type="text" name="slug" id="slugInput" value="<?php echo htmlspecialchars($blog['slug'] ?? createSlug($blog['title'])); ?>" placeholder="auto-generated-from-title" maxlength="200">
                        <small style="color:var(--text-muted);display:block;margin-top:4px;">Live Clean URL Preview: <span id="slugPreview" style="color:var(--primary-light);font-weight:600;"><?php echo getBlogUrl($blog); ?></span></small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-paragraph"></i> Excerpt (Summary)</label>
                        <textarea name="excerpt" placeholder="Short summary of your blog" maxlength="500" rows="3"><?php echo htmlspecialchars($blog['excerpt']); ?></textarea>
                        <small style="color:var(--text-muted);display:block;margin-top:4px;">If left empty, will be auto-generated from first section</small>
                        <div class="char-counter"><?php echo strlen($blog['excerpt']); ?>/500 characters</div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Category</label>
                            <select name="category" id="categorySelect">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($blog['category'] == $cat['name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="other">Other (type below)</option>
                            </select>
                            <input type="text" name="category_custom" id="categoryCustom" placeholder="Enter custom category" style="margin-top: 10px; display: none;" value="<?php echo !in_array($blog['category'], array_column($categories, 'name')) && $blog['category'] ? htmlspecialchars($blog['category']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Author</label>
                            <input type="text" name="author" value="<?php echo htmlspecialchars($blog['author']); ?>" maxlength="100">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-image"></i> Featured Image</label>
                        <?php if (!empty($blog['featured_image'])): ?>
                            <div class="current-image">
                                <img src="<?php echo getBlogImageUrl($blog['featured_image']); ?>" alt="Current Image" id="currentImage" onerror="this.src='../images/default-blog.jpg'">
                                <label>
                                    <input type="checkbox" name="remove_image" value="1"> <span>Remove current image</span>
                                </label>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="featured_image" accept="image/*" id="featured_image" style="margin-top:10px;">
                        <small style="color:var(--text-muted);display:block;margin-top:4px;">Max size: 5MB. Allowed: JPG, PNG, GIF, WEBP. Leave empty to keep current image.</small>
                        <div id="image-preview"></div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-toggle-on"></i> Status</label>
                        <select name="status">
                            <option value="published" <?php echo $blog['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo $blog['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    
                    <!-- Advanced Blog Sections Builder -->
                    <div class="form-group">
                        <label><i class="fas fa-layer-group"></i> <span class="required">Blog Sections (Advanced Multi-Type Builder)</span></label>
                        
                        <div class="sections-container">
                            <!-- Add Section Toolbar -->
                            <div class="add-section-toolbar">
                                <div class="add-label"><i class="fas fa-plus-circle"></i> Add New Section to Blog:</div>
                                <div class="add-btn-group">
                                    <button type="button" class="btn-add-sec" onclick="addSection('text')"><i class="fas fa-paragraph"></i> 📝 Text & Story</button>
                                    <button type="button" class="btn-add-sec" onclick="addSection('image')"><i class="fas fa-image"></i> 🖼️ Single Image</button>
                                    <button type="button" class="btn-add-sec" onclick="addSection('gallery_2col')"><i class="fas fa-images"></i> 📸 2-Col Gallery</button>
                                    <button type="button" class="btn-add-sec" onclick="addSection('quote')"><i class="fas fa-quote-left"></i> 💬 Pull Quote</button>
                                    <button type="button" class="btn-add-sec" onclick="addSection('features')"><i class="fas fa-list-check"></i> ⭐ Key Highlights</button>
                                    <button type="button" class="btn-add-sec" onclick="addSection('callout')"><i class="fas fa-lightbulb"></i> 💡 Pro Tip / Callout</button>
                                    <button type="button" class="btn-add-sec" onclick="addSection('faq')"><i class="fas fa-circle-question"></i> ❓ FAQ Item</button>
                                    <button type="button" class="btn-add-sec" onclick="addSection('video')"><i class="fas fa-video"></i> 🎥 Video Tour</button>
                                    <button type="button" class="btn-add-sec" onclick="addSection('cta')"><i class="fas fa-bullhorn"></i> 📞 Booking CTA</button>
                                </div>
                            </div>
                            
                            <div id="sections-list"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-search"></i> Meta Description (SEO)</label>
                        <textarea name="meta_description" placeholder="SEO description" maxlength="160" rows="2"><?php echo htmlspecialchars($blog['meta_description']); ?></textarea>
                        <div class="char-counter"><?php echo strlen($blog['meta_description']); ?>/160 characters</div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Meta Keywords (SEO)</label>
                        <input type="text" name="meta_keywords" value="<?php echo htmlspecialchars($blog['meta_keywords']); ?>" placeholder="tag1, tag2, tag3" maxlength="200">
                        <small style="color:var(--text-muted);display:block;margin-top:4px;">Separate keywords with commas</small>
                    </div>
                    
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Blog</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const BASE_URL = '<?php echo getBaseUrl(); ?>';
        let sectionCounter = 0;

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Apply formatting tags to textarea
        function applyFormat(btn, prefix, suffix, placeholder) {
            const container = btn.closest('.section-body');
            if (!container) return;
            const textarea = container.querySelector('textarea');
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selected = text.substring(start, end);
            const replacement = prefix + (selected || placeholder) + suffix;

            textarea.value = text.substring(0, start) + replacement + text.substring(end);
            textarea.focus();
            textarea.setSelectionRange(start + prefix.length, start + replacement.length - suffix.length);
        }

        // Add Section of specific type
        function addSection(type = 'text', data = {}) {
            const list = document.getElementById('sections-list');
            const secId = 'sec_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const idx = sectionCounter++;

            let badgeHtml = '';
            let bodyHtml = '';

            switch (type) {
                case 'text':
                    badgeHtml = '<i class="fas fa-paragraph"></i> Text / Story';
                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="text">
                        <div class="form-row" style="margin-bottom: 16px;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Section Heading (Optional)</label>
                                <input type="text" name="sections[${idx}][heading]" class="sec-input-title" placeholder="e.g. Majestic Himalayan Architecture..." value="${escapeHtml(data.heading || data.title || '')}" oninput="updateSecTitle(this)">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Heading Hierarchy Level</label>
                                <select name="sections[${idx}][level]" style="background-color:#162340; color:#fff;">
                                    <option value="h2" ${(data.level === 'h2' || !data.level) ? 'selected' : ''}>H2 - Major Section Title</option>
                                    <option value="h3" ${data.level === 'h3' ? 'selected' : ''}>H3 - Subtitle / Subsection</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Content (Generous spacing and clean paragraphs automatically applied)</label>
                            <div class="fmt-toolbar">
                                <button type="button" class="fmt-btn" onclick="applyFormat(this, '**', '**', 'bold text')"><b>B</b></button>
                                <button type="button" class="fmt-btn" onclick="applyFormat(this, '*', '*', 'italic text')"><i>I</i></button>
                                <button type="button" class="fmt-btn" onclick="applyFormat(this, '## ', '', 'Section Title')">H2</button>
                                <button type="button" class="fmt-btn" onclick="applyFormat(this, '### ', '', 'Subsection')">H3</button>
                                <button type="button" class="fmt-btn" onclick="applyFormat(this, '- ', '', 'List item')">• List</button>
                                <button type="button" class="fmt-btn" onclick="applyFormat(this, '> ', '', 'Quote text')">❝ Quote</button>
                                <button type="button" class="fmt-btn" onclick="applyFormat(this, '[Link Text](', ')', 'https://')">🔗 Link</button>
                            </div>
                            <textarea name="sections[${idx}][content]" rows="6" placeholder="Write paragraphs freely. Blank lines between paragraphs are automatically spaced and styled with luxury typography...">${escapeHtml(data.content || data.body || '')}</textarea>
                        </div>
                    `;
                    break;

                case 'image':
                    badgeHtml = '<i class="fas fa-image"></i> Single Image';
                    const existingImgUrl = data.image_url || (Array.isArray(data.images) && data.images[0] ? data.images[0] : (typeof data.image === 'string' ? data.image : ''));
                    const cleanImgThumb = existingImgUrl ? existingImgUrl.replace(/^\/+/, '') : '';
                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="image">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Upload New Image File (Optional)</label>
                                <input type="file" name="section_image_${idx}" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>Or Existing Image Path / URL</label>
                                <input type="text" name="sections[${idx}][image_url]" class="sec-input-title" placeholder="images/room1.jpg or uploads/..." value="${escapeHtml(existingImgUrl)}" oninput="updateSecTitle(this)">
                                ${existingImgUrl ? `
                                    <div style="margin-top:8px; display:flex; align-items:center; gap:10px;">
                                        <img src="${BASE_URL}/${escapeHtml(cleanImgThumb)}" style="width:60px; height:60px; object-fit:cover; border-radius:6px; border:1px solid rgba(255,255,255,0.15);" onerror="this.style.display='none'">
                                        <small style="color:var(--text-muted); font-size:11px;">Current Image Saved</small>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Image Caption (With camera icon)</label>
                                <input type="text" name="sections[${idx}][caption]" placeholder="e.g. Scenic mountain view from Presidential Suite balcony" value="${escapeHtml(data.caption || '')}">
                            </div>
                            <div class="form-group">
                                <label>Layout</label>
                                <select name="sections[${idx}][layout]" style="background-color:#162340; color:#fff;">
                                    <option value="full" ${data.layout === 'full' ? 'selected' : ''}>Full Width</option>
                                    <option value="contained" ${data.layout === 'contained' ? 'selected' : ''}>Contained</option>
                                    <option value="centered" ${data.layout === 'centered' ? 'selected' : ''}>Centered</option>
                                </select>
                            </div>
                        </div>
                    `;
                    break;

                case 'gallery_2col':
                    badgeHtml = '<i class="fas fa-images"></i> 2-Col Gallery';
                    const gImg1 = data.image_url || (Array.isArray(data.images) && data.images[0] ? data.images[0] : '');
                    const gImg2 = data.image_url_2 || (Array.isArray(data.images) && data.images[1] ? data.images[1] : '');
                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="gallery_2col">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Left Image 1 (File or Path)</label>
                                <input type="file" name="section_file1_${idx}" accept="image/*" style="margin-bottom:6px;">
                                <input type="text" name="sections[${idx}][image_url]" placeholder="images/... or uploads/..." value="${escapeHtml(gImg1)}">
                                ${gImg1 ? `
                                    <div style="margin-top:6px; display:flex; align-items:center; gap:8px;">
                                        <img src="${BASE_URL}/${escapeHtml(gImg1.replace(/^\/+/, ''))}" style="width:50px; height:50px; object-fit:cover; border-radius:6px; border:1px solid rgba(255,255,255,0.15);" onerror="this.style.display='none'">
                                        <small style="color:var(--text-muted); font-size:11px;">Saved Left Image</small>
                                    </div>
                                ` : ''}
                                <input type="text" name="sections[${idx}][caption]" placeholder="Left photo caption..." value="${escapeHtml(data.caption || '')}" style="margin-top:6px;">
                            </div>
                            <div class="form-group">
                                <label>Right Image 2 (File or Path)</label>
                                <input type="file" name="section_file2_${idx}" accept="image/*" style="margin-bottom:6px;">
                                <input type="text" name="sections[${idx}][image_url_2]" placeholder="images/... or uploads/..." value="${escapeHtml(gImg2)}">
                                ${gImg2 ? `
                                    <div style="margin-top:6px; display:flex; align-items:center; gap:8px;">
                                        <img src="${BASE_URL}/${escapeHtml(gImg2.replace(/^\/+/, ''))}" style="width:50px; height:50px; object-fit:cover; border-radius:6px; border:1px solid rgba(255,255,255,0.15);" onerror="this.style.display='none'">
                                        <small style="color:var(--text-muted); font-size:11px;">Saved Right Image</small>
                                    </div>
                                ` : ''}
                                <input type="text" name="sections[${idx}][caption_2]" placeholder="Right photo caption..." value="${escapeHtml(data.caption_2 || '')}" style="margin-top:6px;">
                            </div>
                        </div>
                    `;
                    break;

                case 'quote':
                    badgeHtml = '<i class="fas fa-quote-left"></i> Pull Quote';
                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="quote">
                        <div class="form-group">
                            <label>Quote Statement (Rendered in Cormorant Garamond Serif)</label>
                            <textarea name="sections[${idx}][quote]" class="sec-input-title" rows="3" placeholder="Enter an inspiring quote or review snippet..." oninput="updateSecTitle(this)">${escapeHtml(data.quote || '')}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Author / Source Attribution</label>
                            <input type="text" name="sections[${idx}][author]" placeholder="e.g. Travel & Leisure Magazine, or Guest Reviewer" value="${escapeHtml(data.author || '')}">
                        </div>
                    `;
                    break;

                case 'features':
                    badgeHtml = '<i class="fas fa-list-check"></i> Key Highlights';
                    let itemsHtml = '';
                    const items = Array.isArray(data.items) ? data.items : ['Scenic Dhauladhar Mountain View', 'Complimentary Buffet Breakfast & High Tea', '24-Hour Concierge & Room Service'];
                    items.forEach(it => {
                        itemsHtml += `
                            <div class="checklist-item-row">
                                <input type="text" name="sections[${idx}][items][]" value="${escapeHtml(it)}" placeholder="Highlight feature point...">
                                <button type="button" class="btn-remove-item" onclick="this.parentElement.remove()" title="Remove point"><i class="fas fa-times"></i></button>
                            </div>
                        `;
                    });

                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="features">
                        <div class="form-group">
                            <label>Highlights Box Heading</label>
                            <input type="text" name="sections[${idx}][heading]" class="sec-input-title" placeholder="e.g. Key Features & Inclusions" value="${escapeHtml(data.heading || 'Key Features & Amenities')}" oninput="updateSecTitle(this)">
                        </div>
                        <div class="form-group">
                            <label>Checklist Highlight Points (Styled with cyan checkmarks)</label>
                            <div class="checklist-builder" id="checklist_${idx}">
                                ${itemsHtml}
                            </div>
                            <button type="button" class="btn-add-item" onclick="addChecklistItem(${idx})"><i class="fas fa-plus"></i> Add Highlight Point</button>
                        </div>
                    `;
                    break;

                case 'callout':
                    badgeHtml = '<i class="fas fa-lightbulb"></i> Pro Tip / Callout';
                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="callout">
                        <div class="form-group">
                            <label>Callout Box Title</label>
                            <input type="text" name="sections[${idx}][title]" class="sec-input-title" placeholder="e.g. Pro Travel Tip: Golden Hour Photos" value="${escapeHtml(data.title || 'Pro Tip / Note')}" oninput="updateSecTitle(this)">
                        </div>
                        <div class="form-group">
                            <label>Callout Message / Recommendation</label>
                            <textarea name="sections[${idx}][body]" rows="3" placeholder="Write helpful advice or special announcements...">${escapeHtml(data.body || '')}</textarea>
                        </div>
                    `;
                    break;

                case 'faq':
                    badgeHtml = '<i class="fas fa-circle-question"></i> FAQ Item';
                    const qVal = data.question || (Array.isArray(data.items) && data.items[0] ? data.items[0].question : '');
                    const aVal = data.answer || (Array.isArray(data.items) && data.items[0] ? data.items[0].answer : '');
                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="faq">
                        <div class="form-group">
                            <label>Question</label>
                            <input type="text" name="sections[${idx}][question]" class="sec-input-title" placeholder="e.g. What is the best time to plan an outdoor lawn wedding?" value="${escapeHtml(qVal)}" oninput="updateSecTitle(this)">
                        </div>
                        <div class="form-group">
                            <label>Answer (Interactive Expand/Collapse on blog page)</label>
                            <textarea name="sections[${idx}][answer]" rows="3" placeholder="Provide a helpful and thorough answer...">${escapeHtml(aVal)}</textarea>
                        </div>
                    `;
                    break;

                case 'video':
                    badgeHtml = '<i class="fas fa-video"></i> Video Tour';
                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="video">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Video Heading / Title</label>
                                <input type="text" name="sections[${idx}][heading]" class="sec-input-title" placeholder="e.g. Virtual Resort Tour" value="${escapeHtml(data.heading || 'Resort Video Tour')}" oninput="updateSecTitle(this)">
                            </div>
                            <div class="form-group">
                                <label>YouTube URL / Video Link</label>
                                <input type="text" name="sections[${idx}][video_url]" placeholder="https://www.youtube.com/watch?v=... or .mp4 URL" value="${escapeHtml(data.video_url || '')}">
                            </div>
                        </div>
                    `;
                    break;

                case 'cta':
                    badgeHtml = '<i class="fas fa-bullhorn"></i> Booking CTA';
                    bodyHtml = `
                        <input type="hidden" name="sections[${idx}][type]" value="cta">
                        <div class="form-group">
                            <label>CTA Headline</label>
                            <input type="text" name="sections[${idx}][heading]" class="sec-input-title" placeholder="e.g. Plan Your Event or Mountain Stay" value="${escapeHtml(data.heading || 'Plan Your Event or Mountain Stay')}" oninput="updateSecTitle(this)">
                        </div>
                        <div class="form-group">
                            <label>CTA Description</label>
                            <textarea name="sections[${idx}][body]" rows="2" placeholder="Experience panoramic Himalayan luxury, grand banquet halls, and 5-star hospitality in Dharamshala...">${escapeHtml(data.body || 'Experience panoramic Himalayan luxury, banquet halls, and five-star hospitality in Dharamshala.')}</textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Call Direct Phone</label>
                                <input type="text" name="sections[${idx}][phone]" value="${escapeHtml(data.phone || '+917018841900')}">
                            </div>
                            <div class="form-group">
                                <label>WhatsApp Number</label>
                                <input type="text" name="sections[${idx}][whatsapp]" value="${escapeHtml(data.whatsapp || '917018841900')}">
                            </div>
                        </div>
                    `;
                    break;
            }

            const initialTitle = data.heading || data.title || data.question || (data.quote ? data.quote.substring(0, 30) + '...' : '') || type.toUpperCase();

            const secCard = document.createElement('div');
            secCard.className = 'section-item';
            secCard.id = secId;
            secCard.innerHTML = `
                <div class="section-item-header">
                    <div class="sec-header-left">
                        <i class="fas fa-grip-vertical sec-drag-icon"></i>
                        <span class="sec-type-badge">${badgeHtml}</span>
                        <span class="sec-title-preview">${escapeHtml(initialTitle)}</span>
                    </div>
                    <div class="sec-header-actions">
                        <button type="button" class="btn-sec-ctrl" onclick="moveSectionUp(this)" title="Move Up"><i class="fas fa-arrow-up"></i></button>
                        <button type="button" class="btn-sec-ctrl" onclick="moveSectionDown(this)" title="Move Down"><i class="fas fa-arrow-down"></i></button>
                        <button type="button" class="btn-sec-ctrl" onclick="toggleSectionCollapse(this)" title="Collapse / Expand"><i class="fas fa-chevron-down"></i></button>
                        <button type="button" class="btn-sec-ctrl btn-danger" onclick="removeSection(this)" title="Delete Section"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="section-body">
                    ${bodyHtml}
                </div>
            `;

            list.appendChild(secCard);
        }

        function updateSecTitle(input) {
            const card = input.closest('.section-item');
            if (!card) return;
            const preview = card.querySelector('.sec-title-preview');
            if (preview) {
                preview.textContent = input.value.trim() || 'Untitled Section';
            }
        }

        function moveSectionUp(btn) {
            const item = btn.closest('.section-item');
            const prev = item.previousElementSibling;
            if (prev) {
                item.parentNode.insertBefore(item, prev);
            }
        }

        function moveSectionDown(btn) {
            const item = btn.closest('.section-item');
            const next = item.nextElementSibling;
            if (next) {
                item.parentNode.insertBefore(next, item);
            }
        }

        function toggleSectionCollapse(btn) {
            const item = btn.closest('.section-item');
            item.classList.toggle('collapsed');
            const icon = btn.querySelector('i');
            if (item.classList.contains('collapsed')) {
                icon.className = 'fas fa-chevron-right';
            } else {
                icon.className = 'fas fa-chevron-down';
            }
        }

        function removeSection(btn) {
            if (confirm('Are you sure you want to remove this section?')) {
                const item = btn.closest('.section-item');
                item.remove();
            }
        }

        function addChecklistItem(idx) {
            const container = document.getElementById(`checklist_${idx}`);
            if (!container) return;
            const row = document.createElement('div');
            row.className = 'checklist-item-row';
            row.innerHTML = `
                <input type="text" name="sections[${idx}][items][]" placeholder="New highlight point...">
                <button type="button" class="btn-remove-item" onclick="this.parentElement.remove()" title="Remove point"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(row);
        }

        // Live Auto-Slug Generation & Preview
        const titleInput = document.getElementById('titleInput');
        const slugInput = document.getElementById('slugInput');
        const slugPreview = document.getElementById('slugPreview');
        const categorySelect = document.getElementById('categorySelect');
        const categoryCustom = document.getElementById('categoryCustom');
        const baseUrl = '<?php echo getBaseUrl(); ?>';

        function createCleanSlug(text) {
            return text.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function updateSlugPreview() {
            if (!slugPreview) return;
            const currentSlug = slugInput && slugInput.value.trim() ? createCleanSlug(slugInput.value) : (titleInput ? createCleanSlug(titleInput.value) : '');
            let catName = 'general';
            if (categorySelect && categorySelect.value) {
                catName = categorySelect.value === 'other' ? (categoryCustom ? categoryCustom.value : 'general') : categorySelect.value;
            }
            const catSlug = createCleanSlug(catName) || 'general';
            slugPreview.textContent = `${baseUrl}/blog/${catSlug}/${currentSlug || 'your-slug'}`;
        }

        if (titleInput && slugInput) {
            let isManualSlug = <?php echo !empty($blog['slug']) ? 'true' : 'false'; ?>;
            slugInput.addEventListener('input', function() {
                isManualSlug = true;
                updateSlugPreview();
            });
            titleInput.addEventListener('input', function() {
                if (!isManualSlug) {
                    slugInput.value = createCleanSlug(this.value);
                }
                updateSlugPreview();
            });
            updateSlugPreview();
        }

        if (categorySelect && categoryCustom) {
            const categories = <?php echo json_encode(array_column($categories, 'name')); ?>;
            if (categoryCustom.value && !categories.includes(categoryCustom.value)) {
                categorySelect.value = 'other';
                categoryCustom.style.display = 'block';
            } else {
                categoryCustom.style.display = 'none';
            }

            categorySelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    categoryCustom.style.display = 'block';
                } else {
                    categoryCustom.style.display = 'none';
                    categoryCustom.value = '';
                }
                updateSlugPreview();
            });
        }

        // Featured image preview
        document.getElementById('featured_image')?.addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '200px';
                    img.style.marginTop = '10px';
                    img.style.borderRadius = '8px';
                    img.style.border = '2px solid var(--border)';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Form submit handler - re-index sections so names match order
        document.getElementById('blogForm').addEventListener('submit', function(e) {
            const sections = document.querySelectorAll('.section-item');
            if (sections.length === 0) {
                e.preventDefault();
                alert('Please add at least one section to your blog.');
                return false;
            }

            // Re-index all section inputs according to current DOM order
            sections.forEach((sec, newIdx) => {
                sec.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.name && input.name.startsWith('sections[')) {
                        input.name = input.name.replace(/^sections\[\d+\]/, `sections[${newIdx}]`);
                    }
                });
            });
            
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Blog...';
                submitBtn.disabled = true;
            }
        });

        // Load existing sections
        const existingSections = <?php echo json_encode(!empty($sections) ? $sections : [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;

        window.addEventListener('DOMContentLoaded', () => {
            if (existingSections && Array.isArray(existingSections) && existingSections.length > 0) {
                existingSections.forEach(sec => {
                    const secType = sec.type || (sec.images && sec.images.length > 1 ? 'gallery_2col' : (sec.images && sec.images.length === 1 ? 'image' : 'text'));
                    addSection(secType, sec);
                });
            } else {
                addSection('text', {
                    heading: 'Overview & Story',
                    content: ''
                });
            }
        });
    </script>
</body>
</html>
