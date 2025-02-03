<?php
// 讀取 `.env` 環境變數
$dotenvPath = __DIR__ . '/.env';

if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // 跳過註解行
        }
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// 取得資料庫環境變數
$host = getenv('N_POSTGRES_SERVER');
$port = getenv('N_POSTGRES_PORT');
$dbname = getenv('N_POSTGRES_DB');
$user = getenv('N_POSTGRES_USER');
$password = getenv('N_POSTGRES_PASSWORD');

// 檢查環境變數是否成功載入
if (!$host || !$port || !$dbname || !$user || !$password) {
    die("<p style='color: red;'>❌ 錯誤: 無法載入 .env 變數，請檢查 .env 檔案是否存在。</p>");
}

// 連接資料庫
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<p style='color: red;'>❌ 資料庫連接失敗：" . $e->getMessage() . "</p>");
}
?>
