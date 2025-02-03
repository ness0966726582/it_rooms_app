import os
import psycopg2
from dotenv import load_dotenv

# 載入 .env 環境變數
load_dotenv()

# 讀取環境變數
DB_HOST = os.getenv("N_POSTGRES_SERVER")
DB_PORT = os.getenv("N_POSTGRES_PORT")
DB_USER = os.getenv("N_POSTGRES_USER")
DB_PASSWORD = os.getenv("N_POSTGRES_PASSWORD")
DB_NAME = os.getenv("N_POSTGRES_DB")

# 建立資料庫連線
conn = psycopg2.connect(
    host=DB_HOST,
    port=DB_PORT,
    user=DB_USER,
    password=DB_PASSWORD,
    dbname=DB_NAME
)
cur = conn.cursor()

# 建立 it_rooms 資料表
cur.execute("""
     CREATE TABLE IF NOT EXISTS it_rooms (
        id SERIAL PRIMARY KEY,
        point_id VARCHAR(50) UNIQUE NOT NULL,  -- 機房的唯一識別碼
        name VARCHAR(100) NOT NULL,  -- 機房名稱
        photo_path VARCHAR(255),  -- 儲存圖片的檔案路徑
        coordinate VARCHAR(100),  -- 機房座標
        
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 記錄機房新增時間
        update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
""")

conn.commit()
cur.close()
conn.close()

print("✅ 資料表 it_rooms 確保已建立！")
