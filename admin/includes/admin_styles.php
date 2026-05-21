<?php
// Core CSS variables and premium dark Navy/Cyan styling for the resort admin dashboard
?>
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
        --surface: rgba(15, 26, 46, 0.6);
        --surface-light: rgba(22, 35, 64, 0.7);
        --text: #cdd6f4;
        --text-muted: #8892b0;
        --border: rgba(255,255,255,0.06);
        --sidebar-w: 270px;
        --radius: 14px;
        --shadow: 0 8px 32px rgba(0,0,0,0.3);
        --glass-bg: rgba(10, 19, 34, 0.75);
        --glass-border: rgba(255, 255, 255, 0.05);
    }

    body {
        font-family: 'Inter', -apple-system, sans-serif;
        background: var(--darkest);
        background-attachment: fixed;
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

    .sidebar-brand img {
        height: 50px;
        margin-bottom: 8px;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
    }

    .sidebar-nav {
        flex: 1;
        padding: 20px 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        color: var(--text-muted);
        text-decoration: none;
        border-radius: 10px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.25s ease;
        gap: 12px;
    }

    .sidebar-nav a i {
        font-size: 16px;
        width: 20px;
        text-align: center;
    }

    .sidebar-nav a:hover {
        background: rgba(255, 255, 255, 0.03);
        color: #fff;
    }

    .sidebar-nav a.active {
        background: rgba(93, 197, 227, 0.1);
        color: var(--primary-light);
        font-weight: 600;
        box-shadow: inset 0 0 12px rgba(93, 197, 227, 0.05);
        border-left: 3px solid var(--primary-light);
    }

    /* ========== LAYOUT & MAIN CONTENT ========== */
    .admin-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .main-content {
        flex: 1;
        margin-left: var(--sidebar-w);
        padding: 40px;
        transition: margin-left .3s ease;
        min-width: 0; /* Prevents overflow */
    }

    /* ========== TOPBAR / HEADER ========== */
    .header {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        padding: 24px 32px;
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow);
    }

    .header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header h1 i {
        color: var(--primary-light);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 14px;
    }

    .user-info i {
        color: var(--primary-light);
    }

    /* ========== BUTTONS ========== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, #253375 100%);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 4px 15px rgba(29, 40, 92, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(93, 197, 227, 0.2);
        background: linear-gradient(135deg, #253375 0%, var(--primary) 100%);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text);
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    .btn-danger {
        background: rgba(225, 112, 85, 0.15);
        color: #ff7675;
        border: 1px solid rgba(225, 112, 85, 0.3);
    }

    .btn-danger:hover {
        background: #e17055;
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-success {
        background: rgba(0, 184, 148, 0.15);
        color: #55efc4;
        border: 1px solid rgba(0, 184, 148, 0.3);
    }

    .btn-success:hover {
        background: #00b894;
        color: #fff;
        transform: translateY(-2px);
    }

    /* ========== TABLES ========== */
    .table-container {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    th, td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
    }

    th {
        font-weight: 600;
        color: #fff;
        background: rgba(255,255,255,0.02);
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover td {
        background: rgba(255, 255, 255, 0.01);
    }

    /* ========== BADGES ========== */
    .status-badge {
        display: inline-flex;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-published, .status-1 {
        background: rgba(0, 184, 148, 0.1);
        color: #2ecc71;
        border: 1px solid rgba(0, 184, 148, 0.2);
    }

    .status-draft, .status-0 {
        background: rgba(253, 203, 110, 0.1);
        color: #f1c40f;
        border: 1px solid rgba(253, 203, 110, 0.2);
    }

    /* ========== ALERTS ========== */
    .alert {
        background: rgba(0, 184, 148, 0.12);
        color: #55efc4;
        border: 1px solid rgba(0, 184, 148, 0.2);
        padding: 16px 24px;
        border-radius: var(--radius);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-danger {
        background: rgba(225, 112, 85, 0.12);
        color: #ff7675;
        border: 1px solid rgba(225, 112, 85, 0.2);
    }

    /* ========== FORMS ========== */
    .form-container {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        padding: 40px;
        box-shadow: var(--shadow);
    }

    .form-group {
        margin-bottom: 24px;
    }

    label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        font-size: 14px;
        color: #fff;
    }

    label i {
        color: var(--primary-light);
        margin-right: 8px;
    }

    input[type="text"], input[type="file"], input[type="number"], select, textarea {
        width: 100%;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 16px;
        color: #fff;
        font-family: inherit;
        font-size: 14px;
        transition: all 0.25s ease;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: var(--primary-light);
        background: rgba(255, 255, 255, 0.05);
        box-shadow: 0 0 0 3px rgba(93, 197, 227, 0.1);
    }

    textarea {
        resize: vertical;
    }

    .required::after {
        content: '*';
        color: var(--danger);
        margin-left: 4px;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 1024px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .main-content { margin-left: 0; }
        .topbar, .content { padding-left: 20px; padding-right: 20px; }
    }
</style>
