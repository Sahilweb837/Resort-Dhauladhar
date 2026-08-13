<?php
require_once __DIR__ . '/includes/functions.php';
http_response_code(404);
$baseUrl = getBaseUrl();
include __DIR__ . '/includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page Not Found - Hotel Dhauladhar Heights Resort</title>
    <link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>/images/dhr_logo_icon.png">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .error-section {
            padding: 120px 20px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-box {
            max-width: 600px;
            background: white;
            padding: 50px 40px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        }
        .error-code {
            font-size: 100px;
            font-weight: 800;
            color: #000e3a;
            line-height: 1;
            margin-bottom: 20px;
            letter-spacing: -2px;
        }
        .error-title {
            font-size: 26px;
            font-weight: 700;
            color: #222;
            margin-bottom: 15px;
        }
        .error-desc {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .error-btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #000e3a;
            color: white;
        }
        .btn-primary:hover {
            background: #001a66;
            transform: translateY(-2px);
        }
        .btn-outline {
            border: 2px solid #000e3a;
            color: #000e3a;
        }
        .btn-outline:hover {
            background: #000e3a;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<section class="error-section">
    <div class="error-box" data-aos="zoom-in">
        <div class="error-code">404</div>
        <h1 class="error-title">Page or Blog Post Not Found</h1>
        <p class="error-desc">The page or blog post you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <div class="error-actions">
            <a href="<?php echo $baseUrl; ?>/" class="error-btn btn-primary"><i class="fas fa-home"></i> Return Home</a>
            <a href="<?php echo $baseUrl; ?>/blog" class="error-btn btn-outline"><i class="fas fa-blog"></i> Back to Blog</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
