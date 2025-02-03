<?php
require 'config.php'; // 連接資料庫

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        die("❌ 錯誤：缺少機房 ID");
    }

    // 取得機房的圖片路徑
    $stmt = $pdo->prepare("SELECT photo_path FROM it_rooms WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    $photo_path = $row['photo_path'] ?? null;

    // 刪除機房資料
    $stmt_delete = $pdo->prepare("DELETE FROM it_rooms WHERE id = ?");
    $result_delete = $stmt_delete->execute([$id]);

    if ($result_delete) {
        // 刪除圖片（確保不是預設圖片）
        if ($photo_path && file_exists($photo_path) && $photo_path !== 'static/no-image.png') {
            unlink($photo_path);
        }

        echo "<script>alert('✅ 機房刪除成功！'); window.location.href='index.php';</script>";
    } else {
        die("❌ 機房刪除失敗");
    }
}
?>
