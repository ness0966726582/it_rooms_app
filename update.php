<?php
require 'config.php'; // 連接資料庫

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $point_id = $_POST['point_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $coordinate = $_POST['coordinate'] ?? null;

    if (!$id || !$point_id || !$name) {
        die("❌ 錯誤：缺少機房 ID、編號或名稱");
    }

    // 查詢舊的圖片路徑
    $stmt = $pdo->prepare("SELECT photo_path FROM it_rooms WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    $old_photo_path = $row['photo_path'] ?? 'static/no-image.png';
    $new_photo_path = $old_photo_path;

    // 如果有上傳新圖片
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed_exts)) {
            die("❌ 只允許上傳 jpg, png, gif 格式的圖片");
        }

        $file_name = uniqid() . "." . $file_ext;
        $new_photo_path = $upload_dir . $file_name;

        if (!move_uploaded_file($file_tmp, $new_photo_path)) {
            die("❌ 圖片上傳失敗");
        }

        // 刪除舊的圖片（確保不是預設圖片）
        if ($old_photo_path !== 'static/no-image.png' && file_exists($old_photo_path)) {
            unlink($old_photo_path);
        }
    }

    // 更新機房資訊
    try {
        $stmt = $pdo->prepare("UPDATE it_rooms SET point_id = ?, name = ?, photo_path = ?, coordinate = ?, update_at = NOW() WHERE id = ?");
        $stmt->execute([$point_id, $name, $new_photo_path, $coordinate, $id]);

        echo "<script>alert('✅ 機房更新成功！'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        die("❌ 更新失敗：" . $e->getMessage());
    }
}
?>
