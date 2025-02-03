<?php
require 'config.php'; // 連接資料庫

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $point_id = $_POST['point_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $coordinate = $_POST['coordinate'] ?? null;

    if (!$point_id || !$name) {
        die("❌ 錯誤：缺少機房編號 (point_id) 或名稱 (name)");
    }

    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $photo_path = 'static/no-image.png'; // 預設圖片

    // 處理圖片上傳
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed_exts)) {
            die("❌ 只允許上傳 jpg, png, gif 格式的圖片");
        }

        $file_name = uniqid() . "." . $file_ext;
        $photo_path = $upload_dir . $file_name;

        if (!move_uploaded_file($file_tmp, $photo_path)) {
            die("❌ 圖片上傳失敗");
        }
    }

    // 插入機房資訊到資料庫
    try {
        $stmt = $pdo->prepare("INSERT INTO it_rooms (point_id, name, photo_path, coordinate, created_at, update_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$point_id, $name, $photo_path, $coordinate]);

        echo "<script>alert('✅ 機房新增成功！'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        die("❌ 新增失敗：" . $e->getMessage());
    }
}
?>
