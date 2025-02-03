<?php
require 'config.php'; // 連接資料庫

// 讀取機房資料
try {
    $stmt = $pdo->query("SELECT * FROM it_rooms ORDER BY update_at DESC");
    $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    die("❌ 資料庫查詢失敗：" . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>機房管理系統</title>
    <script>
        // 開啟新增機房視窗
        function openAddRoomModal() {
            document.getElementById("addRoomForm").reset();
            document.getElementById("addModal").style.display = "block";
        }

        // 開啟編輯機房視窗
        function openEditRoomModal(id, pointId, name, photoPath, coordinate) {
            document.getElementById("edit-id").value = id;
            document.getElementById("edit-point-id").value = pointId;
            document.getElementById("edit-name").value = name;
            document.getElementById("edit-photo-preview").src = photoPath;
            document.getElementById("edit-coordinate").value = coordinate;
            document.getElementById("editModal").style.display = "block";
        }

        // 開啟刪除確認視窗
        function openDeleteModal(id) {
            document.getElementById("delete-id").value = id;
            document.getElementById("deleteModal").style.display = "block";
        }

        // 關閉視窗
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // 確定刪除機房
        function deleteRoom() {
            document.getElementById("deleteForm").submit();
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }

        // 新增機房後刷新頁面
        function addRoom() {
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    </script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        /* Modal 視窗樣式 */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>機房管理系統</h1>

<button onclick="openAddRoomModal()">➕ 新增機房</button>

<table>
    <tr>
        <th>ID</th>
        <th>機房編號</th>
        <th>名稱</th>
        <th>圖片</th>
        <th>座標</th>
        <th>更新時間</th>
        <th>操作</th>
    </tr>
    <?php foreach ($rooms as $room): ?>
    <tr>
        <td><?= htmlspecialchars($room['id']) ?></td>
        <td><?= htmlspecialchars($room['point_id']) ?></td>
        <td><?= htmlspecialchars($room['name']) ?></td>
        <td>
            <img src="<?= htmlspecialchars($room['photo_path'] ?: 'static/no-image.png') ?>" alt="機房圖片">
        </td>
        <td><?= htmlspecialchars($room['coordinate']) ?></td>
        <td><?= htmlspecialchars($room['update_at']) ?></td>
        <td>
            <button onclick="openEditRoomModal(
                '<?= htmlspecialchars($room['id']) ?>',
                '<?= htmlspecialchars($room['point_id']) ?>',
                '<?= htmlspecialchars($room['name']) ?>',
                '<?= htmlspecialchars($room['photo_path']) ?>',
                '<?= htmlspecialchars($room['coordinate']) ?>'
            )">✏️ 編輯</button>
            <button onclick="openDeleteModal('<?= htmlspecialchars($room['id']) ?>')">🗑️ 刪除</button>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- 新增機房 Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h2>新增機房</h2>
        <form id="addRoomForm" action="upload.php" method="POST" enctype="multipart/form-data" onsubmit="addRoom()">
            <label>機房編號:</label>
            <input type="text" name="point_id" required><br>
            <label>名稱:</label>
            <input type="text" name="name" required><br>
            <label>座標:</label>
            <input type="text" name="coordinate"><br>
            <label>上傳圖片:</label>
            <input type="file" name="photo"><br>
            <button type="submit">✅ 新增</button>
        </form>
    </div>
</div>

<!-- 編輯機房 Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h2>編輯機房</h2>
        <form action="update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit-id">
            <label>機房編號:</label>
            <input type="text" name="point_id" id="edit-point-id" required><br>
            <label>名稱:</label>
            <input type="text" name="name" id="edit-name" required><br>
            <label>座標:</label>
            <input type="text" name="coordinate" id="edit-coordinate"><br>
            <label>上傳新圖片:</label>
            <input type="file" name="photo"><br>
            <img id="edit-photo-preview" src="" width="100"><br>
            <button type="submit">✅ 更新</button>
        </form>
    </div>
</div>

<!-- 刪除機房 Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        <h2>確定刪除機房？</h2>
        <form id="deleteForm" action="delete.php" method="POST">
            <input type="hidden" name="id" id="delete-id">
            <button type="submit">🗑️ 確定刪除</button>
        </form>
    </div>
</div>

</body>
</html>
