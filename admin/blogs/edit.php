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
        header('Location: index.php?msg=error');
        exit();
    }
} else {
    header('Location: index.php');
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid security token.";
    } else {
        $title = trim($_POST['title']);
        $excerpt = trim($_POST['excerpt'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $author = trim($_POST['author'] ?? ($_SESSION['admin_name'] ?? 'Admin'));
        $meta_description = trim($_POST['meta_description'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        $status = $_POST['status'] ?? 'published';
        
        // Handle custom category
        if ($category === 'other' && !empty($_POST['category_custom'])) {
            $category = trim($_POST['category_custom']);
        }
        
        // Handle sections with multiple images
        $updatedSections = [];
        if (isset($_POST['sections']) && is_array($_POST['sections'])) {
            foreach ($_POST['sections'] as $index => $section) {
                $section_title = trim($section['title'] ?? '');
                $section_content = trim($section['content'] ?? '');
                $section_images = [];
                
                // Get existing images
                if (!empty($section['existing_images'])) {
                    $section_images = json_decode($section['existing_images'], true) ?: [];
                }
                
                // Handle new image uploads for this section
                if (isset($_FILES['sections']['name'][$index]['images']) && is_array($_FILES['sections']['name'][$index]['images'])) {
                    $fileCount = count($_FILES['sections']['name'][$index]['images']);
                    
                    for ($i = 0; $i < $fileCount; $i++) {
                        if (isset($_FILES['sections']['error'][$index]['images'][$i]) && 
                            $_FILES['sections']['error'][$index]['images'][$i] == 0 && 
                            !empty($_FILES['sections']['tmp_name'][$index]['images'][$i])) {
                            
                            $file = [
                                'name' => $_FILES['sections']['name'][$index]['images'][$i],
                                'type' => $_FILES['sections']['type'][$index]['images'][$i],
                                'tmp_name' => $_FILES['sections']['tmp_name'][$index]['images'][$i],
                                'error' => $_FILES['sections']['error'][$index]['images'][$i],
                                'size' => $_FILES['sections']['size'][$index]['images'][$i]
                            ];
                            
                            if ($file['size'] > 5 * 1024 * 1024) {
                                $error = "Image size must be less than 5MB.";
                                break 2;
                            }
                            
                            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                            $file_type = mime_content_type($file['tmp_name']);
                            
                            if (!in_array($file_type, $allowed_types)) {
                                $error = "Only JPG, PNG, GIF, and WEBP images are allowed.";
                                break 2;
                            }
                            
                            $upload_result = uploadImage($file);
                            if ($upload_result) {
                                $section_images[] = $upload_result;
                            }
                        }
                    }
                }
                
                // Remove images if requested
                if (isset($_POST['remove_images_' . $index]) && is_array($_POST['remove_images_' . $index])) {
                    foreach ($_POST['remove_images_' . $index] as $removeImg) {
                        $key = array_search($removeImg, $section_images);
                        if ($key !== false) {
                            deleteImage($removeImg);
                            unset($section_images[$key]);
                        }
                    }
                    $section_images = array_values($section_images);
                }
                
                if (!empty($section_title) || !empty($section_content) || !empty($section_images)) {
                    $updatedSections[] = [
                        'title' => htmlspecialchars($section_title, ENT_QUOTES, 'UTF-8'),
                        'content' => $section_content,
                        'images' => $section_images,
                        'order' => $index
                    ];
                }
            }
        }
        
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
                            // Delete old image
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
                if (empty($excerpt)) {
                    // Create excerpt from first section content
                    $first_section = $updatedSections[0] ?? [];
                    $excerpt = substr(strip_tags($first_section['content'] ?? ''), 0, 200) . '...';
                }
                
                // Build full content HTML
                $fullContent = '';
                foreach ($updatedSections as $section) {
                    if (!empty($section['title'])) {
                        $fullContent .= "<h2 class='section-title'>" . $section['title'] . "</h2>\n";
                    }
                    $fullContent .= "<div class='section-content'>" . $section['content'] . "</div>\n";
                    if (!empty($section['images'])) {
                        $fullContent .= "<div class='section-gallery'>\n";
                        foreach ($section['images'] as $img) {
                            $fullContent .= "<div class='gallery-item'><img src='" . $img . "' alt='" . htmlspecialchars($section['title']) . "'></div>\n";
                        }
                        $fullContent .= "</div>\n";
                    }
                }
                
                $data = [
                    'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                    'slug' => createSlug($title),
                    'content' => $fullContent,
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
                    header('Location: index.php?msg=updated');
                    exit();
                } else {
                    $error = "Failed to update blog. Please try again.";
                }
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
    <!-- Montserrat & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php include_once __DIR__ . '/../includes/admin_styles.php'; ?>
    <style>
        .sections-container { margin-top: 15px; }
        .section-item {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 24px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .section-title-input {
            font-size: 16px;
            font-weight: 600;
            flex: 1;
            min-width: 200px;
        }
        .section-images-area {
            margin-top: 18px;
            padding: 18px;
            background: rgba(0,0,0,0.1);
            border-radius: 8px;
            border: 1px dashed var(--border);
        }
        .images-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 12px;
        }
        .image-preview-item {
            position: relative;
            display: inline-block;
        }
        .image-preview-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid var(--border);
        }
        .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
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
        .add-section-btn {
            width: 100%;
            background: rgba(93, 197, 227, 0.15);
            color: var(--primary-light);
            border: 1px dashed var(--primary-light);
            padding: 14px;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 10px;
            transition: all 0.25s ease;
        }
        .add-section-btn:hover {
            background: var(--primary-light);
            color: var(--darker);
            border-style: solid;
        }
        .add-image-btn {
            background: rgba(255,255,255,0.05);
            color: #fff;
            border: 1px solid var(--border);
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }
        .add-image-btn:hover {
            background: rgba(255,255,255,0.1);
        }
        .remove-section {
            background: rgba(225, 112, 85, 0.15);
            color: #ff7675;
            border: 1px solid rgba(225, 112, 85, 0.3);
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }
        .remove-section:hover {
            background: #e17055;
            color: #fff;
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
            border-radius: 6px;
            border: 2px solid var(--border);
        }
        .current-image label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin: 0;
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
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></strong></span>
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
                        <input type="text" name="title" required value="<?php echo htmlspecialchars($blog['title']); ?>" placeholder="Enter blog title" maxlength="200">
                        <div class="char-counter"><?php echo strlen($blog['title']); ?>/200 characters</div>
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
                    
                    <!-- Blog Sections -->
                    <div class="form-group">
                        <label><i class="fas fa-layer-group"></i> <span class="required">Blog Sections</span></label>
                        <div class="sections-container">
                            <div id="sections-list"></div>
                            <button type="button" class="add-section-btn" onclick="addSection()">
                                <i class="fas fa-plus"></i> Add New Section
                            </button>
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
        let sectionCount = 0;
        let tempImageUrls = {};
        
        // Load existing sections
        const existingSections = <?php echo json_encode($sections); ?>;
        
        function addSection(title = '', content = '', images = []) {
            const sectionsList = document.getElementById('sections-list');
            const sectionId = 'section_' + Date.now() + '_' + sectionCount;
            
            let imagesHtml = '';
            if (images.length > 0) {
                images.forEach((img, idx) => {
                    const cleanImg = img.replace(/^\/+/, '');
                    imagesHtml += `
                        <div class="image-preview-item" data-image="${escapeHtml(img)}">
                            <img src="${BASE_URL}/${escapeHtml(cleanImg)}" onerror="this.src='../images/default-blog.jpg'">
                            <button type="button" class="remove-image" onclick="removeExistingImage(this, ${sectionCount}, '${escapeHtml(img)}')">×</button>
                        </div>
                    `;
                });
            }
            
            const sectionHtml = `
                <div class="section-item" id="${sectionId}" data-section-index="${sectionCount}">
                    <div class="section-header">
                        <input type="text" class="section-title-input" name="sections[${sectionCount}][title]" 
                               placeholder="Section Title" value="${escapeHtml(title)}">
                        <button type="button" class="remove-section" onclick="removeSection('${sectionId}', ${sectionCount})">
                            <i class="fas fa-trash"></i> Remove Section
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label>Section Content</label>
                        <textarea name="sections[${sectionCount}][content]" class="section-content" rows="6" style="width:100%;">${escapeHtml(content)}</textarea>
                    </div>
                    
                    <div class="section-images-area">
                        <label><i class="fas fa-images"></i> Section Images</label>
                        <button type="button" class="add-image-btn" onclick="addImageToSection(${sectionCount})">
                            <i class="fas fa-plus"></i> Add Images (Multiple)
                        </button>
                        <input type="file" class="image-input-${sectionCount}" style="display: none;" multiple accept="image/*" 
                               onchange="previewImages(this, ${sectionCount})">
                        <div class="images-preview" id="images_preview_${sectionCount}">
                            ${imagesHtml}
                        </div>
                        <input type="hidden" name="sections[${sectionCount}][existing_images]" value='${JSON.stringify(images)}' id="existing_images_${sectionCount}">
                        <div class="file-inputs-container" id="file_inputs_${sectionCount}">
                            <input type="file" name="sections[${sectionCount}][images][]" class="image-input-${sectionCount}" style="display: none;" multiple accept="image/*" onchange="previewImages(this, ${sectionCount})">
                        </div>
                        <small style="color:var(--text-muted);display:block;margin-top:4px;">Max size: 5MB per image. Allowed: JPG, PNG, GIF, WEBP. You can select multiple images at once.</small>
                    </div>
                </div>
            `;
            
            sectionsList.insertAdjacentHTML('beforeend', sectionHtml);
            sectionCount++;
        }
        
        function addImageToSection(sectionIndex) {
            // Find the last file input that is empty, or create a new one
            const container = document.getElementById(`file_inputs_${sectionIndex}`);
            const inputs = container.querySelectorAll('input[type="file"]');
            let lastInput = inputs[inputs.length - 1];
            
            if (lastInput && lastInput.files.length > 0) {
                // Last input has files, create a new one
                lastInput = document.createElement('input');
                lastInput.type = 'file';
                lastInput.name = `sections[${sectionIndex}][images][]`;
                lastInput.className = `image-input-${sectionIndex}`;
                lastInput.style.display = 'none';
                lastInput.multiple = true;
                lastInput.accept = 'image/*';
                lastInput.onchange = function() { previewImages(this, sectionIndex); };
                container.appendChild(lastInput);
            }
            
            lastInput.click();
        }
        
        function previewImages(input, sectionIndex) {
            const previewDiv = document.getElementById(`images_preview_${sectionIndex}`);
            const existingImagesInput = document.getElementById(`existing_images_${sectionIndex}`);
            let existingImages = [];
            
            if (existingImagesInput.value) {
                existingImages = JSON.parse(existingImagesInput.value);
            }
            
            if (input.files) {
                for (let i = 0; i < input.files.length; i++) {
                    const file = input.files[i];
                    
                    if (file.size > 5 * 1024 * 1024) {
                        alert(`Image ${file.name} is too large. Max 5MB.`);
                        continue;
                    }
                    
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        alert(`Only JPG, PNG, GIF, and WEBP images are allowed.`);
                        continue;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'image-preview-item';
                        imgDiv.setAttribute('data-temp', 'true');
                        imgDiv.innerHTML = `
                            <img src="${e.target.result}">
                            <button type="button" class="remove-image" onclick="this.parentElement.remove(); alert('To remove new images fully, please refresh and re-select.')">×</button>
                        `;
                        previewDiv.appendChild(imgDiv);
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
        
        function removeExistingImage(btn, sectionIndex, imagePath) {
            if (confirm('Remove this image?')) {
                const imageDiv = btn.parentElement;
                imageDiv.remove();
                
                const existingImagesInput = document.getElementById(`existing_images_${sectionIndex}`);
                let existingImages = [];
                if (existingImagesInput.value) {
                    existingImages = JSON.parse(existingImagesInput.value);
                }
                
                const newImages = existingImages.filter(img => img !== imagePath);
                existingImagesInput.value = JSON.stringify(newImages);
                
                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = `remove_images_${sectionIndex}[]`;
                removeInput.value = imagePath;
                document.querySelector(`.section-item[data-section-index="${sectionIndex}"]`).appendChild(removeInput);
            }
        }
        
        // Removed unused removeNewImage function
        
        function removeSection(sectionId, sectionIndex) {
            if (confirm('Are you sure you want to remove this section?')) {
                document.getElementById(sectionId).remove();
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
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
                    img.classList.add('preview-image');
                    img.style.maxWidth = '200px';
                    img.style.marginTop = '10px';
                    img.style.borderRadius = '6px';
                    img.style.border = '2px solid var(--border)';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Character counters
        document.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(el => {
            const counter = el.parentElement.querySelector('.char-counter');
            if (counter) {
                const update = () => {
                    counter.textContent = `${el.value.length}/${el.getAttribute('maxlength')} characters`;
                    if (el.value.length > el.getAttribute('maxlength') * 0.9) {
                        counter.style.color = '#dc3545';
                    } else {
                        counter.style.color = 'var(--text-muted)';
                    }
                };
                el.addEventListener('input', update);
                update();
            }
        });
        
        // Category handling
        const categorySelect = document.getElementById('categorySelect');
        const categoryCustom = document.getElementById('categoryCustom');
        
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
            });
        }
        
        // Load existing sections
        if (existingSections && existingSections.length > 0) {
            existingSections.forEach(section => {
                addSection(section.title || '', section.content || '', section.images || []);
            });
        } else {
            addSection();
        }
        
        // Form submit handler
        document.getElementById('blogForm').addEventListener('submit', function(e) {
            const sections = document.querySelectorAll('.section-item');
            if (sections.length === 0) {
                e.preventDefault();
                alert('Please add at least one section to your blog.');
                return false;
            }
            
            // Handled natively by input[type="file"] now
            
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                submitBtn.disabled = true;
            }
        });
    </script>
</body>
</html>
