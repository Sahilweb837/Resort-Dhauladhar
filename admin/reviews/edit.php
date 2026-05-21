<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

$review = getReviewById($id);
if (!$review) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'reviewer_name' => trim($_POST['reviewer_name'] ?? ''),
        'rating' => intval($_POST['rating'] ?? 5),
        'review_text' => trim($_POST['review_text'] ?? ''),
        'guest_type' => trim($_POST['guest_type'] ?? ''),
        'status' => intval($_POST['status'] ?? 1),
        'review_date' => $_POST['review_date'] ?? date('Y-m-d'),
        'reviewer_image' => $review['reviewer_image'] // Keep existing image by default
    ];
    
    // Handle image upload
    if (isset($_FILES['reviewer_image']) && $_FILES['reviewer_image']['error'] == UPLOAD_ERR_OK) {
        $uploadResult = uploadImage($_FILES['reviewer_image'], 'uploads/reviews/');
        if ($uploadResult) {
            // Delete old image if exists
            if (!empty($review['reviewer_image'])) {
                deleteImage($review['reviewer_image']);
            }
            $data['reviewer_image'] = $uploadResult;
        } else {
            $error = "Image upload failed. Please try again.";
        }
    }
    
    // Remove image if requested
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        if (!empty($review['reviewer_image'])) {
            deleteImage($review['reviewer_image']);
        }
        $data['reviewer_image'] = '';
    }
    
    if (empty($error)) {
        if (empty($data['reviewer_name'])) {
            $error = "Reviewer name is required.";
        } elseif (empty($data['review_text'])) {
            $error = "Review text is required.";
        } else {
            if (updateReview($id, $data)) {
                header('Location: index.php?msg=updated');
                exit();
            } else {
                $error = "Failed to update review.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Review - Resort Admin Panel</title>
    <!-- Montserrat & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php include_once __DIR__ . '/../includes/admin_styles.php'; ?>
    <style>
        .reviewer-preview-img {
            max-width: 150px;
            margin-top: 10px;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 2px solid var(--border);
        }
        .current-image-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .current-image-wrapper img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--border);
        }
        .current-image-wrapper label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin: 0;
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
                <h1><i class="fas fa-edit"></i> Edit Review</h1>
                <div class="header-actions">
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> <span class="required">Reviewer Name</span></label>
                        <input type="text" name="reviewer_name" required value="<?php echo htmlspecialchars($review['reviewer_name']); ?>" placeholder="Enter reviewer's name">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Review Date</label>
                            <input type="date" name="review_date" value="<?php echo htmlspecialchars($review['review_date']); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-suitcase"></i> Guest Type</label>
                            <input type="text" name="guest_type" value="<?php echo htmlspecialchars($review['guest_type']); ?>" placeholder="e.g. Business | Family | Solo">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-star"></i> Rating (1 to 5)</label>
                            <select name="rating" required>
                                <option value="5" <?php echo $review['rating'] == 5 ? 'selected' : ''; ?>>5 Stars</option>
                                <option value="4" <?php echo $review['rating'] == 4 ? 'selected' : ''; ?>>4 Stars</option>
                                <option value="3" <?php echo $review['rating'] == 3 ? 'selected' : ''; ?>>3 Stars</option>
                                <option value="2" <?php echo $review['rating'] == 2 ? 'selected' : ''; ?>>2 Stars</option>
                                <option value="1" <?php echo $review['rating'] == 1 ? 'selected' : ''; ?>>1 Star</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-toggle-on"></i> Status</label>
                            <select name="status">
                                <option value="1" <?php echo $review['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $review['status'] == 0 ? 'selected' : ''; ?>>Hidden</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-comment-dots"></i> <span class="required">Review Text</span></label>
                        <textarea name="review_text" required placeholder="Type the review content here..." rows="5"><?php echo htmlspecialchars($review['review_text']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-image"></i> Reviewer Image</label>
                        <?php if(!empty($review['reviewer_image'])): ?>
                            <div class="current-image-wrapper">
                                <img src="<?php echo getReviewerImageUrl($review['reviewer_image']); ?>" alt="Current Image" onerror="this.src='../../images/default-blog.jpg'">
                                <label>
                                    <input type="checkbox" name="remove_image" value="1"> <span>Remove current image</span>
                                </label>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="reviewer_image" accept="image/*" id="reviewer_image" style="margin-top: 10px;">
                        <small style="color:var(--text-muted);display:block;margin-top:4px;">Leave blank to keep existing image</small>
                        <div id="image-preview"></div>
                    </div>
                    
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Review</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Reviewer image preview
        document.getElementById('reviewer_image')?.addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('reviewer-preview-img');
                    preview.appendChild(img);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>
