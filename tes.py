import mysql.connector

# Koneksi ke database
db = mysql.connector.connect(
    host="interchange.proxy.rlwy.net",
    user="root",
    password="HJOavoCtODmNevZqfrMWbLJTEfgQsOAN",
    database="railway"
)

# DB_CONNECTION=mysql
# DB_HOST=interchange.proxy.rlwy.net
# DB_PORT=26110
# DB_DATABASE=railway
# DB_USERNAME=root
# DB_PASSWORD=HJOavoCtODmNevZqfrMWbLJTEfgQsOAN

cursor = db.cursor()

# Mendapatkan semua tabel
cursor.execute("SHOW TABLES")
tables = cursor.fetchall()

# Menghapus setiap tabel
for table in tables:
    cursor.execute(f"DROP TABLE {table[0]}")

db.commit()
cursor.close()
db.close()
