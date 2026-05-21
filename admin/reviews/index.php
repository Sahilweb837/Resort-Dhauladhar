<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$reviews = getAllAdminReviews();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Resort Admin Panel</title>
    <!-- Montserrat & Inter Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php include_once __DIR__ . '/../includes/admin_styles.php'; ?>
    <style>
        .rev-img { 
            width: 44px; 
            height: 44px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 2px solid var(--border);
        }
        .rev-img-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
        }
        .status-badge { 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600;
            display: inline-block;
        }
        .status-1 { 
            background: rgba(46, 204, 113, 0.15); 
            color: #2ecc71; 
            border: 1px solid rgba(46, 204, 113, 0.3);
        }
        .status-0 { 
            background: rgba(241, 196, 15, 0.15); 
            color: #f1c40f; 
            border: 1px solid rgba(241, 196, 15, 0.3);
        }
        .rating-stars {
            color: #f1c40f;
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
                <h1><i class="fas fa-star"></i> Manage Reviews</h1>
                <div class="header-actions">
                    <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Review</a>
                </div>
            </div>
            
            <?php if ($msg == 'created'): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> Review added successfully!</div>
            <?php elseif ($msg == 'updated'): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> Review updated successfully!</div>
            <?php elseif ($msg == 'deleted'): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> Review deleted successfully!</div>
            <?php endif; ?>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reviews)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                                    <i class="far fa-star" style="font-size: 40px; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                                    No reviews found. Click "Add Review" to create one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reviews as $rev): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($rev['reviewer_image'])): ?>
                                        <img src="<?php echo getReviewerImageUrl($rev['reviewer_image']); ?>" class="rev-img" alt="Reviewer">
                                    <?php else: ?>
                                        <div class="rev-img-placeholder"><i class="fas fa-user"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><strong style="color: #fff;"><?php echo htmlspecialchars($rev['reviewer_name']); ?></strong></td>
                                <td>
                                    <div class="rating-stars">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <?php if($i <= $rev['rating']): ?>
                                                <i class="fas fa-star"></i>
                                            <?php else: ?>
                                                <i class="far fa-star" style="opacity: 0.3;"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($rev['review_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $rev['status']; ?>">
                                        <?php echo $rev['status'] == 1 ? 'Active' : 'Hidden'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="edit.php?id=<?php echo $rev['id']; ?>" class="action-btn edit-btn" title="Edit Review">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete.php?id=<?php echo $rev['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this review?')" title="Delete Review">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
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
