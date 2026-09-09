<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$success = '';
$isEdit = false;
$blogId = null;
$blog = null;

// Ensure session csrf_token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$categories = getCategories();
$recentBlogs = getRecentBlogs(5);
$baseUrl = function_exists('getBaseUrl') ? getBaseUrl() : '';
$adminBase = function_exists('getBaseUrl') ? getBaseUrl() . '/admin/' : '../';

// Check if editing an existing blog
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $blogId = (int)$_GET['id'];
    $blog = getBlogById($blogId);
    if ($blog) {
        $isEdit = true;
    }
}

// Decode sections for edit mode
$sections = [];
if ($isEdit && !empty($blog['sections'])) {
    $sections = json_decode($blog['sections'], true) ?: [];
}
if ($isEdit && empty($sections) && !empty($blog['content'])) {
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

// Handle Form Submission (Save & Publish or Save Draft)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slugInput = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author = trim($_POST['author'] ?? ($_SESSION['admin_name'] ?? 'Admin'));
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords = trim($_POST['meta_keywords'] ?? '');
    $status = $_POST['status'] ?? 'published';
    
    // Custom category
    if ($category === 'other' && !empty($_POST['category_custom'])) {
        $category = trim($_POST['category_custom']);
    }
    
    // Process sections
    $rawSections = $_POST['sections'] ?? [];
    $processedSections = processSubmittedBlogSections($rawSections, $_FILES['sections'] ?? null);
    
    // Validate
    if (empty($title)) {
        $error = "Blog Title is required.";
    } elseif (strlen($title) < 3) {
        $error = "Title must be at least 3 characters.";
    } elseif (empty($processedSections)) {
        $error = "Please add at least one content section to your blog.";
    }
    
    if (empty($error)) {
        $featured_image = $isEdit ? ($blog['featured_image'] ?? '') : '';
        
        // Handle featured image upload
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
            if ($_FILES['featured_image']['size'] > 5 * 1024 * 1024) {
                $error = "Featured image must be less than 5MB.";
            } else {
                $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $mime = mime_content_type($_FILES['featured_image']['tmp_name']);
                if (!in_array($mime, $allowed)) {
                    $error = "Only JPG, PNG, GIF, and WEBP images are allowed.";
                } else {
                    $uploaded = uploadImage($_FILES['featured_image']);
                    if ($uploaded) {
                        if ($isEdit && !empty($featured_image)) {
                            deleteImage($featured_image);
                        }
                        $featured_image = $uploaded;
                    } else {
                        $error = "Failed to upload featured image.";
                    }
                }
            }
        } elseif (!empty($_POST['featured_image_url']) && empty($featured_image)) {
            $featured_image = trim($_POST['featured_image_url']);
        }
        
        if (empty($error)) {
            // Auto excerpt if empty
            if (empty($excerpt)) {
                $textParts = [];
                foreach ($processedSections as $s) {
                    if (!empty($s['content'])) $textParts[] = strip_tags($s['content']);
                    if (!empty($s['body'])) $textParts[] = strip_tags($s['body']);
                    if (!empty($s['quote'])) $textParts[] = strip_tags($s['quote']);
                }
                $combined = implode(' ', $textParts);
                $excerpt = substr($combined, 0, 200) . (strlen($combined) > 200 ? '...' : '');
            }
            
            // Compile sections to HTML for content column
            $compiledHtml = compileBlogSectionsToHtml($processedSections);
            
            $data = [
                'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                'slug' => !empty($slugInput) ? createSlug($slugInput) : createSlug($title),
                'content' => $compiledHtml,
                'excerpt' => htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8'),
                'featured_image' => $featured_image,
                'category' => htmlspecialchars($category, ENT_QUOTES, 'UTF-8'),
                'author' => htmlspecialchars($author, ENT_QUOTES, 'UTF-8'),
                'meta_description' => htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8'),
                'meta_keywords' => htmlspecialchars($meta_keywords, ENT_QUOTES, 'UTF-8'),
                'status' => $status,
                'sections' => json_encode($processedSections, JSON_UNESCAPED_UNICODE),
                'content_format' => 'html'
            ];
            
            if ($isEdit) {
                if (updateBlog($blogId, $data)) {
                    $success = "Blog updated successfully!";
                    $blog = getBlogById($blogId);
                    $sections = $processedSections;
                } else {
                    $error = "Failed to update blog. Please try again.";
                }
            } else {
                if (createBlog($data)) {
                    header('Location: ' . $adminBase . 'blogs/index.php?msg=created');
                    exit();
                } else {
                    $error = "Failed to create blog. Please try again.";
                }
            }
        }
    }
}

// Initial values for form
$formTitle = $isEdit ? ($blog['title'] ?? '') : ($_POST['title'] ?? '');
$formSlug = $isEdit ? ($blog['slug'] ?? '') : ($_POST['slug'] ?? '');
$formExcerpt = $isEdit ? ($blog['excerpt'] ?? '') : ($_POST['excerpt'] ?? '');
$formCategory = $isEdit ? ($blog['category'] ?? 'Resort & Travel') : ($_POST['category'] ?? 'Resort & Travel');
$formAuthor = $isEdit ? ($blog['author'] ?? 'Admin') : ($_POST['author'] ?? ($_SESSION['admin_name'] ?? 'Admin'));
$formStatus = $isEdit ? ($blog['status'] ?? 'published') : ($_POST['status'] ?? 'published');
$formMetaDesc = $isEdit ? ($blog['meta_description'] ?? '') : ($_POST['meta_description'] ?? '');
$formMetaKeys = $isEdit ? ($blog['meta_keywords'] ?? '') : ($_POST['meta_keywords'] ?? '');
$formFeaturedImg = $isEdit ? ($blog['featured_image'] ?? '') : '';
$formFeaturedImgUrl = !empty($formFeaturedImg) ? getBlogImageUrl($formFeaturedImg) : '';
$publishDateFormatted = $isEdit && !empty($blog['created_at']) ? date('F d, Y', strtotime($blog['created_at'])) : date('F d, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Blog' : 'Create Blog'; ?> - Live Builder & Device Preview | Resort Admin</title>
    <link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>/images/dhr_logo_icon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Admin Base Styles -->
    <?php include_once __DIR__ . '/../includes/admin_styles.php'; ?>
    
    <!-- Resort Dhauladhar Live Preview Stylesheet -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/style.css">
    
    <style>
        /* ===== BUILDER FULLSCREEN SHELL ===== */
        body {
            background: #060e1a;
            color: #cdd6f4;
            overflow-x: hidden;
            font-family: 'Inter', sans-serif;
        }

        .builder-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(10, 19, 34, 0.96);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(93, 197, 227, 0.2);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .builder-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .builder-brand img {
            height: 38px;
            object-fit: contain;
        }

        .builder-brand-title {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .builder-brand-title .badge-live {
            background: linear-gradient(135deg, #5DC5E3 0%, #1D285C 100%);
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            box-shadow: 0 0 10px rgba(93, 197, 227, 0.4);
        }

        .builder-center-tools {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(15, 26, 46, 0.95);
            padding: 4px 8px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .tool-group {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .tool-divider {
            width: 1px;
            height: 20px;
            background: rgba(255, 255, 255, 0.15);
            margin: 0 4px;
        }

        .device-btn {
            background: transparent;
            border: none;
            color: #8892b0;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .device-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
        }

        .device-btn.active {
            background: #5DC5E3;
            color: #0B162C;
            font-weight: 700;
            box-shadow: 0 2px 10px rgba(93, 197, 227, 0.35);
        }

        /* Mobile Sticky Tabs Bar (for tablet & phone viewports) */
        .mobile-tabs-bar {
            display: none;
            position: sticky;
            top: 57px;
            z-index: 999;
            background: #0c182b;
            border-bottom: 1px solid rgba(93, 197, 227, 0.25);
            padding: 8px 14px;
            gap: 8px;
        }

        .mobile-tab-btn {
            flex: 1;
            padding: 9px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: #8892b0;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .mobile-tab-btn.active {
            background: #5DC5E3;
            color: #0B162C;
            border-color: #5DC5E3;
            box-shadow: 0 2px 10px rgba(93, 197, 227, 0.35);
        }

        .tab-live-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #00b894;
            display: inline-block;
            box-shadow: 0 0 6px #00b894;
        }

        .builder-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-builder-save {
            background: linear-gradient(135deg, #5DC5E3 0%, #1D285C 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 13.5px;
            padding: 9px 18px;
            border-radius: 8px;
            border: 1px solid rgba(93, 197, 227, 0.4);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.22s ease;
            white-space: nowrap;
        }

        .btn-builder-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(93, 197, 227, 0.35);
        }

        .btn-builder-back {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #cdd6f4;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-builder-back:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        /* ===== SPLIT SCREEN WORKSPACE WITH VIEW MODES ===== */
        .builder-workspace {
            display: grid;
            grid-template-columns: minmax(420px, 48%) minmax(460px, 52%);
            min-height: calc(100vh - 64px);
            transition: all 0.3s ease;
        }

        .builder-workspace.mode-editor-only {
            grid-template-columns: 1fr !important;
        }
        .builder-workspace.mode-editor-only .builder-preview-pane {
            display: none !important;
        }

        .builder-workspace.mode-preview-only {
            grid-template-columns: 1fr !important;
        }
        .builder-workspace.mode-preview-only .builder-editor-pane {
            display: none !important;
        }

        /* Left Column: Form & Section Editor */
        .builder-editor-pane {
            background: #08111e;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            padding: 24px 28px 80px;
            height: calc(100vh - 64px);
            overflow-y: auto;
        }

        /* Right Column: Live Resort Preview Pane */
        .builder-preview-pane {
            background: #060d18;
            height: calc(100vh - 64px);
            overflow-y: auto;
            position: relative;
            padding: 24px 20px 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Preview Viewport Header Banner */
        .preview-pane-info-bar {
            width: 100%;
            max-width: 1200px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 16px;
            background: rgba(15, 26, 46, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #8892b0;
        }

        .preview-pane-info-bar strong {
            color: #5DC5E3;
        }

        /* ===== DESKTOP PREVIEW CONTAINER ===== */
        .preview-canvas-desktop {
            width: 100%;
            max-width: 1180px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.45);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #404650;
        }

        /* ===== TABLET MOCKUP FRAME (768px) ===== */
        .preview-canvas-tablet {
            width: min(768px, 100%);
            min-height: 800px;
            background: #0b111e;
            border: 12px solid #1f293d;
            border-radius: 36px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .preview-canvas-tablet .phone-screen-scroll {
            background: #ffffff;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            color: #404650;
        }

        /* ===== MOBILE SMARTPHONE MOCKUP FRAME ===== */
        .preview-canvas-mobile {
            width: min(395px, 100%);
            min-height: 780px;
            background: #0b111e;
            border: 12px solid #1f293d;
            border-radius: 46px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        /* Phone Notch / Dynamic Island */
        .phone-notch-bar {
            height: 32px;
            background: #0a1322;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            z-index: 10;
        }

        .phone-speaker-pill {
            width: 90px;
            height: 14px;
            background: #000;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .phone-lens {
            width: 7px;
            height: 7px;
            background: #0c1a2f;
            border-radius: 50%;
            border: 1px solid #1d2a44;
        }

        /* Phone Screen Content Wrapper */
        .phone-screen-scroll {
            background: #ffffff;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            color: #404650;
        }

        /* Phone Home Indicator Bar at Bottom */
        .phone-home-indicator {
            height: 20px;
            background: #0a1322;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .phone-home-bar {
            width: 120px;
            height: 4px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 4px;
        }

        /* ===== IN-PREVIEW SIMULATED RESORT HEADER ===== */
        .preview-site-header {
            background: #0B162C;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .preview-site-logo img {
            height: 36px;
            object-fit: contain;
        }

        .preview-site-nav {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .preview-site-nav span {
            color: #c4d6ea;
        }

        /* Overrides inside preview to prevent absolute fixed conflicts */
        .preview-canvas-desktop .blog-detail-wrapper,
        .preview-canvas-mobile .blog-detail-wrapper {
            margin: 24px auto 50px !important;
            padding: 0 20px !important;
        }

        .preview-canvas-mobile .blog-detail-wrapper {
            grid-template-columns: 1fr !important;
            gap: 24px !important;
            padding: 0 12px !important;
        }

        .preview-canvas-mobile .blog-detail-post-header h2 {
            font-size: 24px !important;
        }

        .preview-canvas-mobile .blog-detail-content {
            padding: 20px 16px !important;
        }

        .preview-canvas-mobile .related-blog-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }

        /* ===== SECTION CARDS & TOOLBAR IN BUILDER FORM ===== */
        .builder-card {
            background: rgba(15, 26, 46, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 22px;
        }

        .builder-card-title {
            font-size: 14px;
            font-weight: 700;
            color: #5DC5E3;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #cdd6f4;
            margin-bottom: 6px;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            background: #0e192c;
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13.5px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #5DC5E3;
            box-shadow: 0 0 0 3px rgba(93, 197, 227, 0.18);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Add Block Toolbar */
        .add-block-palette {
            background: rgba(22, 35, 64, 0.65);
            border: 1px solid rgba(93, 197, 227, 0.2);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .palette-label {
            font-size: 12px;
            font-weight: 700;
            color: #5DC5E3;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .palette-btn-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .btn-palette-add {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #ffffff;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            text-align: left;
        }

        .btn-palette-add i {
            color: #5DC5E3;
            font-size: 13px;
        }

        .btn-palette-add:hover {
            background: rgba(93, 197, 227, 0.15);
            border-color: #5DC5E3;
            color: #5DC5E3;
            transform: translateY(-2px);
        }

        /* Draggable Section Item */
        .builder-section-item {
            background: rgba(15, 26, 46, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 10px;
            margin-bottom: 14px;
            overflow: hidden;
            transition: all 0.22s ease;
        }

        .builder-section-header {
            background: rgba(22, 35, 64, 0.9);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            cursor: pointer;
            user-select: none;
        }

        .builder-sec-type-badge {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 4px;
            background: rgba(93, 197, 227, 0.15);
            color: #5DC5E3;
            border: 1px solid rgba(93, 197, 227, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .builder-sec-title-summary {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .builder-sec-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-sec-act {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #8892b0;
            width: 28px;
            height: 28px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.18s ease;
        }

        .btn-sec-act:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .btn-sec-act.btn-delete:hover {
            background: #e17055;
            color: #ffffff;
            border-color: #e17055;
        }

        .builder-sec-body {
            padding: 16px;
        }

        .builder-section-item.collapsed .builder-sec-body {
            display: none;
        }

        /* Format toolbar for textareas */
        .mini-fmt-toolbar {
            display: flex;
            gap: 4px;
            margin-bottom: 6px;
            background: rgba(0, 0, 0, 0.3);
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .mini-fmt-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
        }

        .mini-fmt-btn:hover {
            background: #5DC5E3;
            color: #0B162C;
        }

        /* Checkbox / Dynamic items */
        .dynamic-item-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .btn-item-del {
            background: rgba(225, 112, 85, 0.15);
            border: 1px solid rgba(225, 112, 85, 0.3);
            color: #ff7675;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
        }

        .btn-item-del:hover {
            background: #e17055;
            color: #fff;
        }

        .btn-item-add {
            background: rgba(93, 197, 227, 0.1);
            border: 1px dashed #5DC5E3;
            color: #5DC5E3;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-item-add:hover {
            background: #5DC5E3;
            color: #0B162C;
            border-style: solid;
        }

        .sample-template-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: rgba(93, 197, 227, 0.08);
            border: 1px dashed rgba(93, 197, 227, 0.3);
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .sample-template-box span {
            font-size: 12.5px;
            color: #c4d6ea;
        }

        .btn-load-sample {
            background: #1D285C;
            color: #5DC5E3;
            border: 1px solid #5DC5E3;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-load-sample:hover {
            background: #5DC5E3;
            color: #0B162C;
        }

        /* Alert notifications */
        .live-alert {
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .live-alert-danger {
            background: rgba(225, 112, 85, 0.2);
            border: 1px solid #e17055;
            color: #ff7675;
        }
        .live-alert-success {
            background: rgba(0, 184, 148, 0.2);
            border: 1px solid #00b894;
            color: #55efc4;
        }

        /* Responsive Layout Overrides */
        @media (max-width: 1440px) {
            .builder-workspace:not(.mode-preview-only) .preview-canvas-desktop .blog-detail-wrapper {
                grid-template-columns: 1fr !important;
                gap: 25px !important;
            }
        }

        @media (max-width: 1100px) {
            .mobile-tabs-bar {
                display: flex;
            }
            .layout-group,
            .tool-divider {
                display: none !important;
            }
            .builder-workspace {
                grid-template-columns: 1fr !important;
            }
            .builder-workspace.tab-show-editor .builder-preview-pane {
                display: none !important;
            }
            .builder-workspace.tab-show-preview .builder-editor-pane {
                display: none !important;
            }
            .builder-editor-pane,
            .builder-preview-pane {
                height: auto;
                min-height: calc(100vh - 120px);
                padding: 18px 16px 80px;
            }
        }

        @media (max-width: 920px) {
            .builder-header {
                flex-wrap: wrap;
                padding: 10px 16px;
                gap: 10px;
            }
            .builder-brand-title span:first-child {
                font-size: 14px;
            }
            .builder-brand-title .badge-live {
                display: none;
            }
            .builder-center-tools {
                order: 3;
                width: 100%;
                justify-content: center;
                background: rgba(15, 26, 46, 0.95);
            }
            .builder-header-actions {
                margin-left: auto;
                gap: 8px;
            }
            .btn-builder-save {
                padding: 8px 14px;
                font-size: 12.5px;
            }
            .btn-builder-back {
                padding: 8px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 680px) {
            .palette-btn-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .form-row {
                grid-template-columns: 1fr !important;
            }
            .preview-pane-info-bar {
                flex-direction: column;
                gap: 6px;
                text-align: center;
            }
            .builder-section-header {
                flex-wrap: wrap;
                gap: 8px;
            }
            .builder-sec-controls {
                margin-left: auto;
            }
            .device-btn {
                padding: 5px 10px;
                font-size: 11px;
            }
            .tool-text {
                display: none;
            }
            .sample-template-box {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .sample-template-box button {
                width: 100%;
            }
        }

        @media (max-width: 420px) {
            .palette-btn-grid {
                grid-template-columns: 1fr !important;
            }
            .builder-brand img {
                height: 28px;
            }
            .btn-builder-save span {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- TOP HEADER -->
    <header class="builder-header">
        <div class="builder-brand">
            <a href="<?php echo $adminBase; ?>dashboard.php">
                <img src="<?php echo $baseUrl; ?>/images/dhr_logo_full_white_720.png" alt="Resort Dhauladhar">
            </a>
            <div class="builder-brand-title">
                <span>Blog Builder</span>
                <span class="badge-live"><i class="fa-solid fa-bolt"></i> Real-time Live Preview</span>
            </div>
        </div>

        <!-- CENTER VIEWPORT & LAYOUT TOOLS -->
        <div class="builder-center-tools">
            <!-- Layout Switcher (Desktop) -->
            <div class="tool-group layout-group">
                <button type="button" class="device-btn btn-layout-mode active" id="btnModeSplit" onclick="switchLayoutMode('split')" title="Split View (Form + Preview)">
                    <i class="fa-solid fa-columns"></i> <span class="tool-text">Split</span>
                </button>
                <button type="button" class="device-btn btn-layout-mode" id="btnModeEditor" onclick="switchLayoutMode('editor')" title="Editor Only">
                    <i class="fa-solid fa-pen-to-square"></i> <span class="tool-text">Editor</span>
                </button>
                <button type="button" class="device-btn btn-layout-mode" id="btnModePreview" onclick="switchLayoutMode('preview')" title="Preview Only">
                    <i class="fa-solid fa-eye"></i> <span class="tool-text">Preview</span>
                </button>
            </div>
            <div class="tool-divider"></div>
            <!-- Device Switcher -->
            <div class="tool-group device-group">
                <button type="button" class="device-btn active" id="btnDesktopView" onclick="switchPreviewDevice('desktop')" title="Desktop View">
                    <i class="fa-solid fa-laptop"></i> <span class="tool-text">Desktop</span>
                </button>
                <button type="button" class="device-btn" id="btnTabletView" onclick="switchPreviewDevice('tablet')" title="Tablet View (768px)">
                    <i class="fa-solid fa-tablet-screen-button"></i> <span class="tool-text">Tablet</span>
                </button>
                <button type="button" class="device-btn" id="btnMobileView" onclick="switchPreviewDevice('mobile')" title="Mobile Smartphone View (390px)">
                    <i class="fa-solid fa-mobile-screen-button"></i> <span class="tool-text">Mobile</span>
                </button>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="builder-header-actions">
            <?php if ($isEdit && !empty($blog['slug'])): ?>
                <a href="<?php echo $baseUrl; ?>/blog-detail.php?slug=<?php echo urlencode($blog['slug']); ?>" target="_blank" class="btn-builder-back" title="View live published page">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> <span class="btn-text">View Live</span>
                </a>
            <?php endif; ?>
            <a href="index.php" class="btn-builder-back" title="Exit to Blogs List">
                <i class="fa-solid fa-arrow-left"></i> <span class="btn-text">Exit</span>
            </a>
            <button type="button" class="btn-builder-save" onclick="submitBuilderForm()">
                <i class="fa-solid fa-cloud-arrow-up"></i> <span><?php echo $isEdit ? 'Update Post' : 'Publish Blog'; ?></span>
            </button>
        </div>
    </header>

    <!-- STICKY MOBILE / TABLET TABS BAR -->
    <div class="mobile-tabs-bar" id="mobileTabsBar">
        <button type="button" class="mobile-tab-btn active" id="tabBtnEditor" onclick="switchMobileTab('editor')">
            <i class="fa-solid fa-pen-to-square"></i> Form Editor
        </button>
        <button type="button" class="mobile-tab-btn" id="tabBtnPreview" onclick="switchMobileTab('preview')">
            <i class="fa-solid fa-eye"></i> Live Preview <span class="tab-live-pulse"></span>
        </button>
    </div>

    <!-- MAIN DUAL PANE WORKSPACE -->
    <main class="builder-workspace mode-split tab-show-editor" id="builderWorkspace">
        
        <!-- LEFT PANE: FORM & SECTION MANAGER -->
        <section class="builder-editor-pane">
            <?php if ($error): ?>
                <div class="live-alert live-alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="live-alert live-alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Sample Template Loader -->
            <div class="sample-template-box">
                <div>
                    <strong style="color:#ffffff; font-size:13px;"><i class="fa-solid fa-wand-magic-sparkles" style="color:#5DC5E3;"></i> Quick Start Template</strong>
                    <span>Load pre-crafted Himalayan Resort story layout with images, quotes & FAQs</span>
                </div>
                <button type="button" class="btn-load-sample" onclick="loadSampleTemplate()">Load Sample</button>
            </div>

            <form method="POST" enctype="multipart/form-data" id="builderForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- POST BASICS CARD -->
                <div class="builder-card">
                    <div class="builder-card-title"><i class="fa-solid fa-newspaper"></i> Post Details</div>
                    
                    <div class="form-group">
                        <label>Blog Title <span style="color:#ff7675;">*</span></label>
                        <input type="text" name="title" id="inputTitle" required value="<?php echo htmlspecialchars($formTitle); ?>" placeholder="e.g. Dream Destination Weddings in Dharamshala at Dhauladhar Heights" oninput="onTitleChange(this.value)">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" id="selectCategory" onchange="onCategoryChange(this.value)">
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c['name']); ?>" <?php echo ($formCategory === $c['name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="other">Other (type custom)</option>
                            </select>
                            <input type="text" name="category_custom" id="inputCategoryCustom" placeholder="Enter custom category name" style="margin-top:8px; display:none;" oninput="onLiveUpdate()">
                        </div>

                        <div class="form-group">
                            <label>Author</label>
                            <input type="text" name="author" id="inputAuthor" value="<?php echo htmlspecialchars($formAuthor); ?>" placeholder="e.g. Resort Curators" oninput="onLiveUpdate()">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>SEO Clean Slug</label>
                            <input type="text" name="slug" id="inputSlug" value="<?php echo htmlspecialchars($formSlug); ?>" placeholder="auto-generated-from-title">
                        </div>
                        <div class="form-group">
                            <label>Publish Status</label>
                            <select name="status" id="selectStatus">
                                <option value="published" <?php echo ($formStatus === 'published') ? 'selected' : ''; ?>>Published</option>
                                <option value="draft" <?php echo ($formStatus === 'draft') ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <!-- Featured Image Input -->
                    <div class="form-group">
                        <label>Featured Cover Image</label>
                        <input type="file" name="featured_image" id="inputFileFeatured" accept="image/*" onchange="handleFeaturedImageUpload(this)">
                        <input type="hidden" name="featured_image_url" id="inputFeaturedImgUrl" value="<?php echo htmlspecialchars($formFeaturedImgUrl); ?>">
                        <small style="color:#8892b0; display:block; margin-top:4px;">Supported: JPG, PNG, WEBP (Max 5MB). Live preview updates immediately upon selection.</small>
                    </div>

                    <div class="form-group">
                        <label>Excerpt / Summary</label>
                        <textarea name="excerpt" id="inputExcerpt" rows="2" placeholder="Brief summary of the article..." oninput="onLiveUpdate()"><?php echo htmlspecialchars($formExcerpt); ?></textarea>
                    </div>
                </div>

                <!-- SECTION BUILDER PALETTE -->
                <div class="add-block-palette">
                    <div class="palette-label"><i class="fa-solid fa-plus-circle"></i> Add Content Block to Blog:</div>
                    <div class="palette-btn-grid">
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('text')"><i class="fa-solid fa-paragraph"></i> Text Story</button>
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('image')"><i class="fa-solid fa-image"></i> Photo Card</button>
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('gallery_2col')"><i class="fa-solid fa-images"></i> 2-Col Gallery</button>
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('quote')"><i class="fa-solid fa-quote-left"></i> Pull Quote</button>
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('features')"><i class="fa-solid fa-list-check"></i> Highlights</button>
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('callout')"><i class="fa-solid fa-lightbulb"></i> Pro Tip Box</button>
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('faq')"><i class="fa-solid fa-circle-question"></i> FAQ Accordion</button>
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('video')"><i class="fa-solid fa-video"></i> Video Tour</button>
                        <button type="button" class="btn-palette-add" onclick="addBuilderSection('cta')"><i class="fa-solid fa-bullhorn"></i> In-Article CTA</button>
                    </div>
                </div>

                <!-- LIST OF DYNAMIC SECTION ITEMS -->
                <div id="builderSectionsList"></div>

                <!-- SEO METADATA CARD -->
                <div class="builder-card" style="margin-top:20px;">
                    <div class="builder-card-title"><i class="fa-solid fa-magnifying-glass"></i> SEO & Search Meta</div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" id="inputMetaDesc" rows="2" placeholder="SEO description for search engines..."><?php echo htmlspecialchars($formMetaDesc); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="inputMetaKeys" value="<?php echo htmlspecialchars($formMetaKeys); ?>" placeholder="resort in dharamshala, destination wedding, dhauladhar heights" oninput="onTagsChange(this.value)">
                    </div>
                </div>

                <div style="display:flex; gap:12px; margin-top:24px;">
                    <button type="submit" class="btn-builder-save" style="padding:12px 28px; font-size:14px;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> <?php echo $isEdit ? 'Save Changes' : 'Publish Blog Post'; ?>
                    </button>
                    <a href="index.php" class="btn-builder-back" style="padding:12px 20px;">Cancel</a>
                </div>
            </form>
        </section>

        <!-- RIGHT PANE: REAL-TIME RESORT LIVE PREVIEW -->
        <section class="builder-preview-pane">
            <div class="preview-pane-info-bar">
                <div>
                    <i class="fa-solid fa-eye" style="color:#5DC5E3; margin-right:6px;"></i>
                    Resort Dhauladhar Live Render: <strong id="previewModeLabel">Desktop View</strong>
                </div>
                <div id="previewWordCountInfo">
                    0 words • 1 min read
                </div>
            </div>

            <!-- PREVIEW VIEWPORT (TOGGLED BETWEEN DESKTOP AND SMARTPHONE) -->
            <div id="previewViewportContainer" class="preview-canvas-desktop">
                
                <!-- Simulated Resort Site Header -->
                <div class="preview-site-header">
                    <div class="preview-site-logo">
                        <img src="<?php echo $baseUrl; ?>/images/dhr_logo_full_white_720.png" alt="Dhauladhar Heights Resort">
                    </div>
                    <div class="preview-site-nav">
                        <span>THE RESORT</span>
                        <span>ABOUT</span>
                        <span>ROOMS</span>
                        <span>WEDDINGS</span>
                        <span>BLOG</span>
                        <span>CONTACT</span>
                    </div>
                </div>

                <!-- MAIN ARTICLE & SIDEBAR LAYOUT (MATCHES FRONT-END BLOG-DETAIL.PHP) -->
                <main class="blog-detail-wrapper">
                    
                    <!-- LEFT / MAIN ARTICLE -->
                    <article class="blog-detail-article">
                        
                        <!-- Post Header -->
                        <header class="blog-detail-post-header">
                            <span class="blog-detail-category" id="liveCategoryBadge">Resort & Travel</span>
                            <h2 id="livePostTitle">Dream Destination Weddings in Dharamshala at Dhauladhar Heights</h2>
                            <div class="blog-detail-meta">
                                <span><i class="fa-regular fa-calendar"></i> <span id="livePostDate"><?php echo $publishDateFormatted; ?></span></span>
                                <span><i class="fa-regular fa-clock"></i> <span id="liveReadTime">1 min read</span></span>
                                <span><i class="fa-solid fa-location-dot"></i> Dharamshala, Himachal Pradesh</span>
                            </div>
                        </header>

                        <!-- Featured Image -->
                        <div class="blog-featured-image-wrap">
                            <img src="<?php echo !empty($formFeaturedImgUrl) ? htmlspecialchars($formFeaturedImgUrl) : ($baseUrl . '/images/default-blog.jpg'); ?>" 
                                 alt="Featured Blog Cover" 
                                 class="blog-featured-image" 
                                 id="liveFeaturedImage"
                                 onerror="this.onerror=null; this.src='<?php echo $baseUrl; ?>/images/default-blog.jpg';">
                        </div>

                        <!-- Share Row -->
                        <div class="blog-share-row">
                            <span class="blog-back-link"><i class="fa-solid fa-arrow-left"></i> Back to Blogs</span>
                            <div class="blog-social-icons">
                                <a><i class="fab fa-facebook-f"></i></a>
                                <a><i class="fab fa-instagram"></i></a>
                                <a><i class="fab fa-whatsapp"></i></a>
                                <a><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>

                        <!-- Rendered Sections Content Container -->
                        <div class="blog-detail-content" id="liveSectionsContainer">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <!-- Live Tags Row -->
                        <div class="blog-tags" id="liveTagsRow" style="padding: 0 36px 20px; display:none;">
                            <strong>Tags:</strong>
                            <span id="liveTagsList"></span>
                        </div>

                        <!-- In-Article Consultation CTA Banner -->
                        <div class="blog-detail-cta" id="liveDetailCta">
                            <div>
                                <span>Planning a mountain getaway or event?</span>
                                <h2>Let Dhauladhar Heights Resort host your stay, banquet, wedding and dining experience.</h2>
                            </div>
                            <span class="cta-btn-primary"><i class="fa-solid fa-calendar-check"></i> Book Consultation</span>
                        </div>
                    </article>

                    <!-- KD TENT HOUSE STYLE SIDEBAR -->
                    <aside class="sidebar blog-detail-sidebar">
                        
                        <!-- Search Widget -->
                        <div class="widget search-box">
                            <h2>Search</h2>
                            <input type="text" id="liveSidebarSearchInput" placeholder="Search blog topics..." onkeyup="filterLiveSidebarPosts(this.value)">
                        </div>

                        <!-- Recent Posts Widget -->
                        <div class="widget">
                            <h2>Recent Posts</h2>
                            <ul class="detail-recent-posts" id="liveSidebarRecentPosts">
                                <?php if (!empty($recentBlogs)): ?>
                                    <?php foreach ($recentBlogs as $rb): ?>
                                        <li><a href="#"><?php echo htmlspecialchars($rb['title']); ?></a></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><a href="#">Top Hidden Gems to Visit in Dharamshala in 2026</a></li>
                                    <li><a href="#">Why Staying Amidst Tea Gardens is the Ultimate Mountain Retreat</a></li>
                                    <li><a href="#">Planning a Dream Himalayan Destination Wedding</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Accommodations & Experiences Widget -->
                        <div class="widget">
                            <h2>Accommodations & Experiences</h2>
                            <ul>
                                <li><a href="#">Executive Room</a></li>
                                <li><a href="#">Executive Suite</a></li>
                                <li><a href="#">Presidential Suite</a></li>
                                <li><a href="#">Twin Bedded Room</a></li>
                                <li><a href="#">Deluxe Room</a></li>
                                <li><a href="#">Destination Weddings & Banquets</a></li>
                                <li><a href="#">Tea Garden Mountain View</a></li>
                                <li><a href="#">Private Dining & Events</a></li>
                            </ul>
                        </div>

                        <!-- Quick Contact Widget -->
                        <div class="widget blog-contact-widget">
                            <h2>Quick Contact</h2>
                            <p><i class="fa-solid fa-phone"></i> +91 70188-41900</p>
                            <p><i class="fa-solid fa-envelope"></i> reservation@dhauladharheightsresort.com</p>
                            <span class="send-enquiry-btn">Send Enquiry</span>
                        </div>
                    </aside>
                </main>

                <!-- RELATED STORIES SECTION (KD TENT HOUSE LAYOUT) -->
                <section class="related-blog-section">
                    <div class="common-header">
                        <h2>Related <span>Stories</span></h2>
                        <p>More destination guides, travel tips and luxury retreat ideas in Dharamshala.</p>
                    </div>
                    <div class="related-blog-grid">
                        <article class="blog-card">
                            <div class="img-container">
                                <img src="<?php echo $baseUrl; ?>/images/DSC09810.jpg" alt="Dharamshala Travel Guide">
                            </div>
                            <div class="card-content">
                                <span class="category">TRAVEL GUIDE</span>
                                <h3 class="post-title"><a>Top 10 Hidden Gems to Visit in Dharamshala in 2026</a></h3>
                                <div class="post-meta">
                                    <span class="date"><i class="fa-regular fa-calendar"></i> April 21, 2026</span>
                                </div>
                            </div>
                        </article>
                        <article class="blog-card">
                            <div class="img-container">
                                <img src="<?php echo $baseUrl; ?>/images/DSC00496-HDR-Enhanced-NR-Edit.jpg" alt="Tea Garden Stay">
                            </div>
                            <div class="card-content">
                                <span class="category">EXPERIENCES</span>
                                <h3 class="post-title"><a>Why Staying Amidst Tea Gardens is the Ultimate Mountain Retreat</a></h3>
                                <div class="post-meta">
                                    <span class="date"><i class="fa-regular fa-calendar"></i> May 12, 2026</span>
                                </div>
                            </div>
                        </article>
                        <article class="blog-card">
                            <div class="img-container">
                                <img src="<?php echo $baseUrl; ?>/images/hs.jpg" alt="Destination Wedding">
                            </div>
                            <div class="card-content">
                                <span class="category">WEDDINGS</span>
                                <h3 class="post-title"><a>Planning a Dream Himalayan Destination Wedding at Dhauladhar</a></h3>
                                <div class="post-meta">
                                    <span class="date"><i class="fa-regular fa-calendar"></i> June 08, 2026</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </section>
    </main>

    <!-- CLIENT ENGINE: REAL-TIME LIVE PREVIEW & BUILDER SCRIPT -->
    <script>
        const siteBaseUrl = <?php echo json_encode($baseUrl); ?>;
        let sectionCounter = 0;
        let sectionsData = [];
        let currentDeviceMode = 'desktop';

        // Pre-existing sections if editing
        const initialSections = <?php echo json_encode(!empty($sections) ? $sections : []); ?>;

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Toggle Preview Device (Desktop vs Tablet vs Mobile Smartphone View)
        function switchPreviewDevice(mode) {
            currentDeviceMode = mode;
            const container = document.getElementById('previewViewportContainer');
            const btnDesktop = document.getElementById('btnDesktopView');
            const btnTablet = document.getElementById('btnTabletView');
            const btnMobile = document.getElementById('btnMobileView');
            const modeLabel = document.getElementById('previewModeLabel');

            [btnDesktop, btnTablet, btnMobile].forEach(b => { if (b) b.classList.remove('active'); });

            if (mode === 'mobile') {
                container.className = 'preview-canvas-mobile';
                if (btnMobile) btnMobile.classList.add('active');
                if (modeLabel) modeLabel.textContent = 'Mobile Smartphone View (390px)';
            } else if (mode === 'tablet') {
                container.className = 'preview-canvas-tablet';
                if (btnTablet) btnTablet.classList.add('active');
                if (modeLabel) modeLabel.textContent = 'Tablet View (768px)';
            } else {
                container.className = 'preview-canvas-desktop';
                if (btnDesktop) btnDesktop.classList.add('active');
                if (modeLabel) modeLabel.textContent = 'Desktop View';
            }
        }

        // Layout mode switcher (Split / Editor Only / Preview Only)
        function switchLayoutMode(mode) {
            const workspace = document.getElementById('builderWorkspace');
            const btns = document.querySelectorAll('.btn-layout-mode');
            btns.forEach(b => b.classList.remove('active'));

            const activeBtn = document.getElementById('btnMode' + mode.charAt(0).toUpperCase() + mode.slice(1));
            if (activeBtn) activeBtn.classList.add('active');

            workspace.classList.remove('mode-editor-only', 'mode-preview-only', 'mode-split');
            if (mode === 'editor') {
                workspace.classList.add('mode-editor-only');
            } else if (mode === 'preview') {
                workspace.classList.add('mode-preview-only');
                onLiveUpdate();
            } else {
                workspace.classList.add('mode-split');
            }
        }

        // Mobile Tab Switcher for tablets and phones
        function switchMobileTab(tab) {
            const workspace = document.getElementById('builderWorkspace');
            const btnEd = document.getElementById('tabBtnEditor');
            const btnPrev = document.getElementById('tabBtnPreview');

            if (tab === 'preview') {
                workspace.classList.remove('tab-show-editor');
                workspace.classList.add('tab-show-preview');
                if (btnPrev) btnPrev.classList.add('active');
                if (btnEd) btnEd.classList.remove('active');
                onLiveUpdate();
            } else {
                workspace.classList.remove('tab-show-preview');
                workspace.classList.add('tab-show-editor');
                if (btnEd) btnEd.classList.add('active');
                if (btnPrev) btnPrev.classList.remove('active');
            }
        }

        // Title changes: update preview + slugify
        function onTitleChange(val) {
            const previewTitle = document.getElementById('livePostTitle');
            previewTitle.textContent = val.trim() || 'Untitled Mountain Blog Post';

            // Auto-slug if slug is empty or was auto-generated
            const slugInput = document.getElementById('inputSlug');
            if (!slugInput.dataset.manualEdited) {
                slugInput.value = val.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
            onLiveUpdate();
        }

        document.getElementById('inputSlug').addEventListener('input', function() {
            this.dataset.manualEdited = 'true';
        });

        function onCategoryChange(val) {
            const customInput = document.getElementById('inputCategoryCustom');
            if (val === 'other') {
                customInput.style.display = 'block';
            } else {
                customInput.style.display = 'none';
            }
            onLiveUpdate();
        }

        function onTagsChange(val) {
            const row = document.getElementById('liveTagsRow');
            const list = document.getElementById('liveTagsList');
            const tags = val.split(',').map(t => t.trim()).filter(t => t.length > 0);
            if (tags.length > 0) {
                row.style.display = 'block';
                list.innerHTML = tags.map(t => `<span style="display:inline-block;background:rgba(93,197,227,0.12);color:#1D285C;padding:2px 8px;border-radius:4px;margin-right:6px;font-size:12px;font-weight:600;">#${escapeHtml(t)}</span>`).join('');
            } else {
                row.style.display = 'none';
                list.innerHTML = '';
            }
        }

        // Live Image preview for Featured Cover Upload
        function handleFeaturedImageUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('liveFeaturedImage').src = e.target.result;
                    document.getElementById('inputFeaturedImgUrl').value = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Format toolbar helper for textareas
        function applyMiniFormat(btn, prefix, suffix, placeholder) {
            const container = btn.closest('.builder-sec-body');
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
            onLiveUpdate();
        }

        // Add Dynamic Section Item
        function addBuilderSection(type = 'text', data = {}) {
            const container = document.getElementById('builderSectionsList');
            const secId = 'bsec_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const idx = sectionCounter++;

            let badge = '';
            let body = '';

            switch (type) {
                case 'text':
                    badge = '<i class="fa-solid fa-paragraph"></i> Text Story';
                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="text">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Section Heading (Optional)</label>
                                <input type="text" name="sections[${idx}][heading]" class="sec-field-heading" placeholder="e.g. Panoramic Himalayan Architecture" value="${escapeHtml(data.heading || data.title || '')}" oninput="onLiveUpdate()">
                            </div>
                            <div class="form-group">
                                <label>Heading Level</label>
                                <select name="sections[${idx}][level]" class="sec-field-level" onchange="onLiveUpdate()">
                                    <option value="h2" ${(data.level === 'h2' || !data.level) ? 'selected' : ''}>H2 - Major Title</option>
                                    <option value="h3" ${data.level === 'h3' ? 'selected' : ''}>H3 - Subsection</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Content Body</label>
                            <div class="mini-fmt-toolbar">
                                <button type="button" class="mini-fmt-btn" onclick="applyMiniFormat(this, '**', '**', 'bold')"><b>B</b></button>
                                <button type="button" class="mini-fmt-btn" onclick="applyMiniFormat(this, '*', '*', 'italic')"><i>I</i></button>
                                <button type="button" class="mini-fmt-btn" onclick="applyMiniFormat(this, '- ', '', 'Bullet item')">• List</button>
                                <button type="button" class="mini-fmt-btn" onclick="applyMiniFormat(this, '## ', '', 'Subtitle')">H2</button>
                                <button type="button" class="mini-fmt-btn" onclick="applyMiniFormat(this, '[Link Text](', ')', 'https://')">🔗 Link</button>
                            </div>
                            <textarea name="sections[${idx}][content]" class="sec-field-content" rows="4" placeholder="Write paragraphs freely. Paragraph breaks, bullets, and styling appear live on the right..." oninput="onLiveUpdate()">${escapeHtml(data.content || data.body || '')}</textarea>
                        </div>
                    `;
                    break;

                case 'image':
                    badge = '<i class="fa-solid fa-image"></i> Photo Card';
                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="image">
                        <div class="form-group">
                            <label>Image Source (URL or File)</label>
                            <input type="text" name="sections[${idx}][image_url]" class="sec-field-img-url" placeholder="https://images.unsplash.com/... or ./images/..." value="${escapeHtml(data.image_url || data.image || '')}" oninput="onLiveUpdate()">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Caption (Optional)</label>
                                <input type="text" name="sections[${idx}][caption]" class="sec-field-img-caption" placeholder="Scenic lawn and tea gardens..." value="${escapeHtml(data.caption || '')}" oninput="onLiveUpdate()">
                            </div>
                            <div class="form-group">
                                <label>Alt Text</label>
                                <input type="text" name="sections[${idx}][alt]" class="sec-field-img-alt" placeholder="Dhauladhar luxury retreat..." value="${escapeHtml(data.alt || '')}" oninput="onLiveUpdate()">
                            </div>
                        </div>
                    `;
                    break;

                case 'gallery_2col':
                    badge = '<i class="fa-solid fa-images"></i> 2-Col Gallery';
                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="gallery_2col">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Left Image URL</label>
                                <input type="text" name="sections[${idx}][image_1]" class="sec-field-gal-img1" placeholder="./images/DSC00496.jpg" value="${escapeHtml(data.image_1 || (data.images ? data.images[0] : '') || '')}" oninput="onLiveUpdate()">
                            </div>
                            <div class="form-group">
                                <label>Right Image URL</label>
                                <input type="text" name="sections[${idx}][image_2]" class="sec-field-gal-img2" placeholder="./images/hs.jpg" value="${escapeHtml(data.image_2 || (data.images ? data.images[1] : '') || '')}" oninput="onLiveUpdate()">
                            </div>
                        </div>
                    `;
                    break;

                case 'quote':
                    badge = '<i class="fa-solid fa-quote-left"></i> Pull Quote';
                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="quote">
                        <div class="form-group">
                            <label>Quote Statement</label>
                            <textarea name="sections[${idx}][quote]" class="sec-field-quote" rows="2" placeholder="Under the shadow of the Dhauladhar peaks, every wedding celebration feels timeless..." oninput="onLiveUpdate()">${escapeHtml(data.quote || '')}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Attribution / Author</label>
                            <input type="text" name="sections[${idx}][author]" class="sec-field-quote-author" placeholder="Himalayan Hospitality Journal" value="${escapeHtml(data.author || '')}" oninput="onLiveUpdate()">
                        </div>
                    `;
                    break;

                case 'features':
                    badge = '<i class="fa-solid fa-list-check"></i> Highlights Checklist';
                    const itemsArr = Array.isArray(data.items) ? data.items : (data.items ? data.items.split('\n') : ['Panoramic Mountain Banquets', 'Custom Culinary Menus', '5-Star Luxury Suites']);
                    let itemsHtml = itemsArr.map((it, iIdx) => `
                        <div class="dynamic-item-row">
                            <input type="text" name="sections[${idx}][items][]" class="sec-field-feature-item" value="${escapeHtml(it)}" placeholder="Highlight feature..." oninput="onLiveUpdate()">
                            <button type="button" class="btn-item-del" onclick="removeDynamicRow(this)"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    `).join('');

                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="features">
                        <div class="form-group">
                            <label>Section Title</label>
                            <input type="text" name="sections[${idx}][title]" class="sec-field-features-title" placeholder="Why Celebrate at Dhauladhar Heights" value="${escapeHtml(data.title || data.heading || 'Key Highlights')}" oninput="onLiveUpdate()">
                        </div>
                        <div class="form-group">
                            <label>Checklist Items</label>
                            <div class="features-rows-wrap">${itemsHtml}</div>
                            <button type="button" class="btn-item-add" onclick="addFeatureItemRow(this, ${idx})"><i class="fa-solid fa-plus"></i> Add Highlight Item</button>
                        </div>
                    `;
                    break;

                case 'callout':
                    badge = '<i class="fa-solid fa-lightbulb"></i> Pro Tip / Callout';
                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="callout">
                        <div class="form-group">
                            <label>Callout Title</label>
                            <input type="text" name="sections[${idx}][title]" class="sec-field-callout-title" placeholder="Resort Concierge Tip" value="${escapeHtml(data.title || 'Pro Tip')}" oninput="onLiveUpdate()">
                        </div>
                        <div class="form-group">
                            <label>Callout Message</label>
                            <textarea name="sections[${idx}][content]" class="sec-field-callout-content" rows="2" placeholder="Book your sunset photography slot along the upper tea garden trail..." oninput="onLiveUpdate()">${escapeHtml(data.content || data.body || '')}</textarea>
                        </div>
                    `;
                    break;

                case 'faq':
                    badge = '<i class="fa-solid fa-circle-question"></i> FAQ Item';
                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="faq">
                        <div class="form-group">
                            <label>Question</label>
                            <input type="text" name="sections[${idx}][question]" class="sec-field-faq-q" placeholder="Can the resort accommodate large destination wedding guests?" value="${escapeHtml(data.question || '')}" oninput="onLiveUpdate()">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="sections[${idx}][answer]" class="sec-field-faq-a" rows="3" placeholder="Yes, Dhauladhar Heights features executive suites, luxury presidential suites, and grand banquet lawns..." oninput="onLiveUpdate()">${escapeHtml(data.answer || '')}</textarea>
                        </div>
                    `;
                    break;

                case 'video':
                    badge = '<i class="fa-solid fa-video"></i> Video Tour';
                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="video">
                        <div class="form-group">
                            <label>Video Title</label>
                            <input type="text" name="sections[${idx}][title]" class="sec-field-video-title" placeholder="Take a Virtual Tour of Dhauladhar Heights" value="${escapeHtml(data.title || 'Resort Video Tour')}" oninput="onLiveUpdate()">
                        </div>
                        <div class="form-group">
                            <label>YouTube Embed URL or Video Link</label>
                            <input type="text" name="sections[${idx}][video_url]" class="sec-field-video-url" placeholder="https://www.youtube.com/embed/..." value="${escapeHtml(data.video_url || '')}" oninput="onLiveUpdate()">
                        </div>
                    `;
                    break;

                case 'cta':
                    badge = '<i class="fa-solid fa-bullhorn"></i> In-Article CTA';
                    body = `
                        <input type="hidden" name="sections[${idx}][type]" value="cta">
                        <div class="form-group">
                            <label>CTA Heading</label>
                            <input type="text" name="sections[${idx}][title]" class="sec-field-cta-title" placeholder="Plan Your Mountain Stay or Celebration" value="${escapeHtml(data.title || 'Plan Your Event or Mountain Stay')}" oninput="onLiveUpdate()">
                        </div>
                        <div class="form-group">
                            <label>CTA Subtitle</label>
                            <input type="text" name="sections[${idx}][subtitle]" class="sec-field-cta-sub" placeholder="Experience panoramic Himalayan luxury, banquet halls, and 5-star hospitality in Dharamshala." value="${escapeHtml(data.subtitle || '')}" oninput="onLiveUpdate()">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Button Text</label>
                                <input type="text" name="sections[${idx}][btn_text]" class="sec-field-cta-btn" placeholder="Book Consultation" value="${escapeHtml(data.btn_text || 'Book Consultation')}" oninput="onLiveUpdate()">
                            </div>
                            <div class="form-group">
                                <label>Button Link</label>
                                <input type="text" name="sections[${idx}][btn_link]" class="sec-field-cta-link" placeholder="contact.html" value="${escapeHtml(data.btn_link || 'contact.html')}" oninput="onLiveUpdate()">
                            </div>
                        </div>
                    `;
                    break;
            }

            const secDiv = document.createElement('div');
            secDiv.className = 'builder-section-item';
            secDiv.id = secId;
            secDiv.dataset.secType = type;

            secDiv.innerHTML = `
                <div class="builder-section-header" onclick="toggleSecCollapse('${secId}')">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="builder-sec-type-badge">${badge}</span>
                        <span class="builder-sec-title-summary">${escapeHtml(data.heading || data.title || type.toUpperCase())}</span>
                    </div>
                    <div class="builder-sec-controls" onclick="event.stopPropagation()">
                        <button type="button" class="btn-sec-act" title="Move Up" onclick="moveSec('${secId}', -1)"><i class="fa-solid fa-arrow-up"></i></button>
                        <button type="button" class="btn-sec-act" title="Move Down" onclick="moveSec('${secId}', 1)"><i class="fa-solid fa-arrow-down"></i></button>
                        <button type="button" class="btn-sec-act btn-delete" title="Delete Section" onclick="deleteSec('${secId}')"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
                <div class="builder-sec-body">
                    ${body}
                </div>
            `;

            container.appendChild(secDiv);
            onLiveUpdate();
        }

        function toggleSecCollapse(id) {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('collapsed');
        }

        function moveSec(id, dir) {
            const el = document.getElementById(id);
            if (!el) return;
            if (dir === -1 && el.previousElementSibling) {
                el.parentNode.insertBefore(el, el.previousElementSibling);
            } else if (dir === 1 && el.nextElementSibling) {
                el.parentNode.insertBefore(el.nextElementSibling, el);
            }
            onLiveUpdate();
        }

        function deleteSec(id) {
            const el = document.getElementById(id);
            if (el && confirm('Delete this section?')) {
                el.remove();
                onLiveUpdate();
            }
        }

        function removeDynamicRow(btn) {
            const row = btn.closest('.dynamic-item-row');
            if (row) {
                row.remove();
                onLiveUpdate();
            }
        }

        function addFeatureItemRow(btn, idx) {
            const wrap = btn.previousElementSibling;
            const div = document.createElement('div');
            div.className = 'dynamic-item-row';
            div.innerHTML = `
                <input type="text" name="sections[${idx}][items][]" class="sec-field-feature-item" placeholder="Highlight feature..." oninput="onLiveUpdate()">
                <button type="button" class="btn-item-del" onclick="removeDynamicRow(this)"><i class="fa-solid fa-trash"></i></button>
            `;
            wrap.appendChild(div);
        }

        // Live markdown-to-html converter
        function parseMiniMarkdown(text) {
            if (!text) return '';
            let html = escapeHtml(text);
            // Bold
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            // Italic
            html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
            // Headings
            html = html.replace(/^### (.*?)$/gm, '<h4 style="font-family:\'Cormorant Garamond\',serif;font-size:22px;color:#1D285C;margin:20px 0 10px;">$1</h4>');
            html = html.replace(/^## (.*?)$/gm, '<h3 class="blog-subheading" style="font-family:\'Cormorant Garamond\',serif;font-size:25px;color:#1D285C;margin:24px 0 12px;">$1</h3>');
            // Paragraph breaks
            const paras = html.split(/\n\n+/);
            return paras.map(p => {
                p = p.trim();
                if (p.startsWith('<h3') || p.startsWith('<h4')) return p;
                if (p.startsWith('- ')) {
                    const items = p.split('\n').filter(l => l.trim().startsWith('- ')).map(l => `<li>${l.substring(2).trim()}</li>`).join('');
                    return `<ul class="blog-styled-list" style="margin:16px 0;">${items}</ul>`;
                }
                return `<p class="blog-paragraph" style="line-height:1.85;font-size:16px;color:#404650;margin-bottom:20px;">${p.replace(/\n/g, '<br>')}</p>`;
            }).join('');
        }

        // LIVE PREVIEW SYNCHRONIZATION ENGINE
        function onLiveUpdate() {
            // Category
            const catSelect = document.getElementById('selectCategory');
            const customCat = document.getElementById('inputCategoryCustom');
            let catVal = catSelect.value;
            if (catVal === 'other' && customCat.value.trim()) {
                catVal = customCat.value.trim();
            }
            document.getElementById('liveCategoryBadge').textContent = catVal || 'Resort & Travel';

            // Title
            const titleVal = document.getElementById('inputTitle').value.trim();
            document.getElementById('livePostTitle').textContent = titleVal || 'Untitled Himalayan Post';

            // Calculate live word count & read time
            let totalWords = 0;
            const container = document.getElementById('liveSectionsContainer');
            let renderedHtml = '';

            const sectionElements = document.querySelectorAll('.builder-section-item');
            sectionElements.forEach((sec, sIdx) => {
                const type = sec.dataset.secType;

                switch (type) {
                    case 'text':
                        const heading = sec.querySelector('.sec-field-heading')?.value.trim() || '';
                        const level = sec.querySelector('.sec-field-level')?.value || 'h2';
                        const content = sec.querySelector('.sec-field-content')?.value || '';
                        
                        totalWords += content.split(/\s+/).filter(w => w.length > 0).length;

                        let headingTag = '';
                        if (heading) {
                            headingTag = level === 'h2' 
                                ? `<h2 class="blog-section-heading" style="font-family:'Cormorant Garamond',serif;font-size:28px;color:#1D285C;margin:32px 0 16px;border-bottom:2.5px solid #5DC5E3;padding-bottom:8px;display:inline-block;">${escapeHtml(heading)}</h2>`
                                : `<h3 class="blog-subheading" style="font-family:'Cormorant Garamond',serif;font-size:23px;color:#1D285C;margin:24px 0 12px;">${escapeHtml(heading)}</h3>`;
                        }

                        renderedHtml += `
                            <div class="blog-section-block blog-section-text" style="margin-bottom:28px;">
                                ${headingTag}
                                <div class="blog-text-flow">${parseMiniMarkdown(content)}</div>
                            </div>
                        `;
                        break;

                    case 'image':
                        const imgUrl = sec.querySelector('.sec-field-img-url')?.value.trim() || siteBaseUrl + '/images/default-blog.jpg';
                        const caption = sec.querySelector('.sec-field-img-caption')?.value.trim() || '';
                        const alt = sec.querySelector('.sec-field-img-alt')?.value.trim() || 'Resort Photo';

                        renderedHtml += `
                            <div class="blog-section-block blog-section-image-card blog-section-img-card" style="margin:28px 0;border-radius:12px;overflow:hidden;box-shadow:0 8px 25px rgba(29,40,92,0.08);">
                                <img src="${escapeHtml(imgUrl)}" alt="${escapeHtml(alt)}" style="width:100%;height:auto;aspect-ratio:16/9;max-height:480px;object-fit:cover;display:block;" onerror="this.src='${siteBaseUrl}/images/default-blog.jpg';">
                                ${caption ? `<div class="blog-img-caption" style="padding:10px 16px;background:#f8fafd;font-size:13.5px;color:#718096;display:flex;align-items:center;gap:8px;"><i class="fa-solid fa-camera" style="color:#5DC5E3;"></i> ${escapeHtml(caption)}</div>` : ''}
                            </div>
                        `;
                        break;

                    case 'gallery_2col':
                        const gImg1 = sec.querySelector('.sec-field-gal-img1')?.value.trim() || siteBaseUrl + '/images/DSC00496-HDR-Enhanced-NR-Edit.jpg';
                        const gImg2 = sec.querySelector('.sec-field-gal-img2')?.value.trim() || siteBaseUrl + '/images/hs.jpg';

                        renderedHtml += `
                            <div class="blog-section-block blog-section-gallery-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:28px 0;">
                                <div class="gallery-col-item" style="border-radius:10px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
                                    <img src="${escapeHtml(gImg1)}" style="width:100%;height:auto;aspect-ratio:4/3;max-height:260px;object-fit:cover;display:block;" onerror="this.src='${siteBaseUrl}/images/default-blog.jpg';">
                                </div>
                                <div class="gallery-col-item" style="border-radius:10px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
                                    <img src="${escapeHtml(gImg2)}" style="width:100%;height:auto;aspect-ratio:4/3;max-height:260px;object-fit:cover;display:block;" onerror="this.src='${siteBaseUrl}/images/default-blog.jpg';">
                                </div>
                            </div>
                        `;
                        break;

                    case 'quote':
                        const quoteTxt = sec.querySelector('.sec-field-quote')?.value.trim() || '';
                        const quoteAuth = sec.querySelector('.sec-field-quote-author')?.value.trim() || '';
                        if (quoteTxt) {
                            renderedHtml += `
                                <div class="blog-section-block blog-section-quote" style="margin:28px 0;padding:24px 30px;background:linear-gradient(135deg,rgba(93,197,227,0.08) 0%,rgba(29,40,92,0.04) 100%);border-left:4px solid #5DC5E3;border-radius:0 12px 12px 0;">
                                    <div class="quote-icon" style="font-size:24px;color:#5DC5E3;margin-bottom:8px;"><i class="fa-solid fa-quote-left"></i></div>
                                    <blockquote style="font-family:'Cormorant Garamond',serif;font-size:21px;font-style:italic;line-height:1.6;color:#1D285C;margin:0;">"${escapeHtml(quoteTxt)}"</blockquote>
                                    ${quoteAuth ? `<div class="quote-author" style="margin-top:10px;font-size:13px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:1px;">— ${escapeHtml(quoteAuth)}</div>` : ''}
                                </div>
                            `;
                        }
                        break;

                    case 'features':
                        const featTitle = sec.querySelector('.sec-field-features-title')?.value.trim() || 'Key Highlights';
                        const featItems = Array.from(sec.querySelectorAll('.sec-field-feature-item')).map(i => i.value.trim()).filter(v => v.length > 0);

                        renderedHtml += `
                            <div class="blog-section-block blog-section-features" style="margin:28px 0;padding:24px 28px;background:#f9fbfe;border:1px solid rgba(93,197,227,0.3);border-radius:12px;">
                                <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;color:#1D285C;margin:0 0 16px;display:flex;align-items:center;gap:10px;font-weight:600;">
                                    <i class="fa-solid fa-star" style="color:#5DC5E3;font-size:18px;"></i> ${escapeHtml(featTitle)}
                                </h3>
                                <ul class="feature-checklist" style="list-style:none;padding:0;margin:0;display:grid;grid-template-columns:1fr 1fr;gap:10px 16px;">
                                    ${featItems.map(it => `<li style="display:flex;align-items:center;gap:10px;font-size:14.5px;color:#2d3748;font-weight:500;"><i class="fa-solid fa-circle-check" style="color:#5DC5E3;"></i> ${escapeHtml(it)}</li>`).join('')}
                                </ul>
                            </div>
                        `;
                        break;

                    case 'callout':
                        const calloutTitle = sec.querySelector('.sec-field-callout-title')?.value.trim() || 'Pro Tip';
                        const calloutContent = sec.querySelector('.sec-field-callout-content')?.value.trim() || '';

                        renderedHtml += `
                            <div class="blog-section-block blog-section-callout" style="margin:28px 0;padding:20px 24px;background:rgba(93,197,227,0.08);border-left:4px solid #5DC5E3;border-radius:0 10px 10px 0;display:flex;gap:16px;align-items:flex-start;">
                                <div class="callout-icon" style="width:38px;height:38px;border-radius:50%;background:#1D285C;color:#5DC5E3;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
                                    <i class="fa-solid fa-lightbulb"></i>
                                </div>
                                <div>
                                    <div class="callout-title" style="font-size:16.5px;font-weight:700;color:#1D285C;margin:0 0 4px;">${escapeHtml(calloutTitle)}</div>
                                    <div class="callout-body" style="font-size:14.5px;line-height:1.65;color:#404650;">${escapeHtml(calloutContent)}</div>
                                </div>
                            </div>
                        `;
                        break;

                    case 'faq':
                        const faqQ = sec.querySelector('.sec-field-faq-q')?.value.trim() || 'Frequently Asked Question';
                        const faqA = sec.querySelector('.sec-field-faq-a')?.value.trim() || '';

                        renderedHtml += `
                            <div class="blog-section-block blog-section-faq" style="margin:20px 0;">
                                <div class="faq-accordion-item" style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#ffffff;">
                                    <button type="button" class="faq-accordion-header" onclick="this.parentElement.classList.toggle('active')" style="width:100%;padding:16px 20px;background:none;border:none;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-size:15px;font-weight:600;color:#1D285C;text-align:left;">
                                        <span><i class="fa-solid fa-circle-question" style="color:#5DC5E3;margin-right:8px;"></i> ${escapeHtml(faqQ)}</span>
                                        <i class="fa-solid fa-chevron-down" style="color:#5DC5E3;"></i>
                                    </button>
                                    <div class="faq-accordion-body" style="padding:0 20px 16px;color:#4a5568;font-size:14.5px;line-height:1.7;">${escapeHtml(faqA)}</div>
                                </div>
                            </div>
                        `;
                        break;

                    case 'video':
                        const vidTitle = sec.querySelector('.sec-field-video-title')?.value.trim() || 'Video Tour';
                        const vidUrl = sec.querySelector('.sec-field-video-url')?.value.trim() || '';

                        renderedHtml += `
                            <div class="blog-section-block blog-section-video" style="margin:28px 0;">
                                <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;color:#1D285C;margin:0 0 14px;display:flex;align-items:center;gap:10px;">
                                    <i class="fa-solid fa-video" style="color:#5DC5E3;"></i> ${escapeHtml(vidTitle)}
                                </h3>
                                <div class="video-embed-wrap" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px;background:#000;">
                                    <iframe src="${escapeHtml(vidUrl)}" style="position:absolute;inset:0;width:100%;height:100%;border:none;" allowfullscreen></iframe>
                                </div>
                            </div>
                        `;
                        break;

                    case 'cta':
                        const ctaTitle = sec.querySelector('.sec-field-cta-title')?.value.trim() || 'Plan Your Event or Mountain Stay';
                        const ctaSub = sec.querySelector('.sec-field-cta-sub')?.value.trim() || 'Experience panoramic Himalayan luxury, banquet halls, and 5-star hospitality in Dharamshala.';
                        const ctaBtn = sec.querySelector('.sec-field-cta-btn')?.value.trim() || 'Book Consultation';

                        renderedHtml += `
                            <div class="blog-section-block blog-detail-cta" style="margin:28px 0;padding:26px 30px;border-radius:12px;background:linear-gradient(135deg,#1D285C 0%,#0B162C 100%);display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
                                <div>
                                    <span style="color:#5DC5E3;font-size:12px;text-transform:uppercase;letter-spacing:1px;font-weight:700;display:block;">RESERVATION & BANQUET BOOKING</span>
                                    <h2 style="color:#ffffff;font-size:22px;margin:6px 0 0;font-family:'Cormorant Garamond',serif;font-weight:600;">${escapeHtml(ctaTitle)}</h2>
                                    <p style="color:#c4d6ea;font-size:13.5px;margin:4px 0 0;">${escapeHtml(ctaSub)}</p>
                                </div>
                                <span class="cta-btn-primary" style="background:#5DC5E3;color:#0B162C;font-weight:700;padding:10px 20px;border-radius:6px;font-size:13px;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;"><i class="fa-solid fa-calendar-check"></i> ${escapeHtml(ctaBtn)}</span>
                            </div>
                        `;
                        break;
                }
            });

            if (!renderedHtml) {
                renderedHtml = `
                    <div style="padding:40px 20px;text-align:center;color:#8892b0;">
                        <i class="fa-solid fa-layer-group" style="font-size:36px;margin-bottom:12px;display:block;opacity:0.4;"></i>
                        <p style="margin:0;font-size:15px;">No sections added yet. Click any block button on the left to add story text, images, pull quotes, or checklists!</p>
                    </div>
                `;
            }

            container.innerHTML = renderedHtml;

            // Update read time
            const calculatedRead = Math.max(1, Math.ceil(totalWords / 200)) + ' min read';
            document.getElementById('liveReadTime').textContent = calculatedRead;
            document.getElementById('previewWordCountInfo').textContent = `${totalWords} words • ${calculatedRead}`;
        }

        // Filter recent posts in preview sidebar
        function filterLiveSidebarPosts(query) {
            var q = query.toLowerCase();
            var list = document.getElementById('liveSidebarRecentPosts');
            if (!list) return;
            var items = list.getElementsByTagName('li');
            for (var i = 0; i < items.length; i++) {
                var txt = items[i].textContent || items[i].innerText;
                items[i].style.display = txt.toLowerCase().indexOf(q) > -1 ? '' : 'none';
            }
        }

        // Quick Start Himalayan Template Loader
        function loadSampleTemplate() {
            if (confirm('Load sample Himalayan Resort story template? This will add structured sections to your builder.')) {
                if (!document.getElementById('inputTitle').value) {
                    document.getElementById('inputTitle').value = 'A Magical Himalayan Escape: Why Dhauladhar Heights Resort is Dharamshala\'s Hidden Gem';
                    onTitleChange(document.getElementById('inputTitle').value);
                }
                
                addBuilderSection('text', {
                    heading: 'A Sanctuary Above the Clouds',
                    level: 'h2',
                    content: 'Perched along the serene slopes of the Kangra Valley, Hotel Dhauladhar Heights Resort offers travelers a peaceful refuge where panoramic snow-capped peaks meet pristine lush pine forests.\n\nWhether you are arriving for a leisurely vacation, a grand destination wedding, or a serene corporate workation, the crisp mountain breeze and uninterrupted horizon provide an instant sense of tranquility.'
                });

                addBuilderSection('image', {
                    image_url: siteBaseUrl + '/images/DSC00496-HDR-Enhanced-NR-Edit.jpg',
                    caption: 'Panoramic balcony view of the tea gardens and sunset horizon',
                    alt: 'Dhauladhar Heights mountain view'
                });

                addBuilderSection('quote', {
                    quote: 'To step onto the private sun terrace at dawn is to witness the snow-dusted Dhauladhar ridge turn to gold.',
                    author: 'Himachal Luxury Travel Guide'
                });

                addBuilderSection('features', {
                    title: 'Signature Mountain Experiences',
                    items: [
                        'Executive & Presidential Suites with private balconies',
                        'Grand banquet hall and outdoor lawn for 500+ guests',
                        'Signature Himachali culinary delicacies and live barbecue',
                        'Dedicated wedding planning and corporate retreat team'
                    ]
                });

                addBuilderSection('callout', {
                    title: 'Concierge Tip for Travelers',
                    content: 'Evenings in Dharamshala turn cool even during summer months. Request an outdoor fire pit setup on our banquet deck for stargazing with hot spiced cider.'
                });

                addBuilderSection('faq', {
                    question: 'How far is the resort from Dharamshala airport and McLeod Ganj?',
                    answer: 'The resort is conveniently located just 25 minutes from Kangra Airport (Gaggal) and a brief scenic 15-minute drive from the cultural center of McLeod Ganj.'
                });
            }
        }

        // Submit form helper
        function submitBuilderForm() {
            const form = document.getElementById('builderForm');
            if (form.reportValidity()) {
                form.submit();
            }
        }

        // Initialize editor with existing sections or sample
        window.addEventListener('DOMContentLoaded', function() {
            if (initialSections && initialSections.length > 0) {
                initialSections.forEach(s => {
                    addBuilderSection(s.type || 'text', s);
                });
            } else {
                // Add initial clean text section
                addBuilderSection('text', {
                    heading: 'Introduction & Mountain Overview',
                    level: 'h2',
                    content: 'Welcome to Dhauladhar Heights Resort. Write your opening story paragraphs here...'
                });
            }
            onLiveUpdate();
            onTagsChange(document.getElementById('inputMetaKeys').value);
        });
    </script>
</body>
</html>
