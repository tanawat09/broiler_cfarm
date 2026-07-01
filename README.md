# ระบบบันทึกการเลี้ยงไก่เนื้อ

เว็บแอปสำหรับบันทึกข้อมูลการเลี้ยงไก่เนื้อรายวัน แยกตามฟาร์ม รุ่นการเลี้ยง และเล้า รองรับการบันทึกอุณหภูมิ ความชื้น สูญเสียรายวัน มิเตอร์น้ำ รับอาหาร จับไก่ขาย ราคามาสเตอร์ และปิดรุ่นแบบตรวจยอด

## เทคโนโลยี

- Laravel 12
- PHP 8.2+
- MariaDB
- Laravel Breeze
- Blade
- Tailwind CSS
- Eloquent ORM
- Docker Compose / Rancher Desktop

## รันด้วย Rancher Desktop + Docker Compose

เปิด Rancher Desktop ให้พร้อมใช้งาน แล้วรัน:

```powershell
docker compose up -d --build
```

เข้าเว็บ:

```text
http://localhost:8000
```

บัญชีหลัก:

```text
admin@example.com
password
```

หมายเหตุ: ฐานข้อมูลเก็บอยู่ใน Docker volume `broiler-db-data` ข้อมูลจะไม่หายเมื่อ `docker compose up -d --build` หรือ rebuild app container ตราบใดที่ไม่ลบ volume

## บัญชีทดสอบ

- Super Admin: `admin@example.com` / `password`
- หนองถนน: `nongthanon@example.com` / `password`
- ก้านเหลือง: `kanlueang@example.com` / `password`
- หนองบอน: `nongbon@example.com` / `password`
- โคกสนวน: `khoksanuan@example.com` / `password`
- บ้านบาตร: `banbat@example.com` / `password`
- ศรีสุข: `srisuk@example.com` / `password`
- นรินทร์: `narin@example.com` / `password`

## ฟาร์มเริ่มต้น

- หนองถนน 20 เล้า
- ก้านเหลือง 18 เล้า
- หนองบอน 14 เล้า
- โคกสนวน 12 เล้า
- บ้านบาตร 16 เล้า
- ศรีสุข 18 เล้า
- นรินทร์ 20 เล้า

## ฟีเจอร์ที่ทำแล้ว

- Login / Logout ด้วย Laravel Breeze
- Dashboard
- Sidebar ภาษาไทย
- Role: Super Admin และ Farm Manager
- จัดการผู้ใช้: Super Admin เพิ่ม/แก้ไข/ลบผู้ใช้ และผูก Farm Manager กับฟาร์มเดียว
- จำกัดข้อมูลตามฟาร์ม: Farm Manager เห็นเฉพาะฟาร์มตัวเอง
- จัดการฟาร์มและจำนวนเล้าตามฟาร์ม
- จัดการเล้า
- เปิดรุ่นการเลี้ยงและกรอกข้อมูลลงไก่รายเล้า
- บันทึกอุณหภูมิ ความชื้น ตาย คัดทิ้งรายวัน
- บันทึกมิเตอร์น้ำรายวัน และคำนวณน้ำใช้จากเลขมิเตอร์สะสม
- บันทึกรับอาหาร แยกเล้า
- ใบหน้าเล้าแบบตาราง
- บันทึกจับไก่ขาย แยกเล้า หลายเที่ยว หลายวัน
- มาสเตอร์ราคาลูกไก่ แยกเพศและเกรด พร้อมวันที่เริ่มใช้ราคา
- มาสเตอร์ราคาขายไก่ บาท/กก. แบบราคากลาง ใช้ร่วมกันทุกฟาร์ม
- ปิดรุ่นแบบตรวจยอด โดยต้องตรงตามสูตร:

```text
ไก่เข้า = ตายสะสม + คัดทิ้งสะสม + จับขายรวม
```

## หน้าสำคัญ

- Dashboard: `/dashboard`
- ผู้ใช้: `/users`
- ฟาร์ม: `/farms`
- รุ่นการเลี้ยง: `/flocks`
- บันทึกประจำวัน: `/daily-records`
- รับอาหาร: `/feed-receipts`
- มิเตอร์น้ำ: `/water-meters`
- จับไก่ขาย: `/sale-records`
- ราคาขายกลาง: `/sale-price-masters`
- ราคาลูกไก่: `/chick-price-masters`
- ใบหน้าเล้า: `/summary`
- ปิดรุ่นตัวอย่าง: `/flocks/6/close`

## คำสั่งดูแลระบบ

รัน migration:

```powershell
docker exec broiler-app php artisan migrate --force
```

รัน seeder:

```powershell
docker exec broiler-app php artisan db:seed --force
```

ดูสถานะ container:

```powershell
docker ps
```

ดูสถานะ migration:

```powershell
docker exec broiler-app php artisan migrate:status
```

## สิ่งที่ยังไม่ได้ทำ

- Export Excel
- Export PDF
- กราฟ
- API แยก
- Mobile app
- Line notification
- ข้อมูลโรงเชือด
- ข้อมูลลูกค้า
- ระบบปิดรุ่นยังไม่ล็อกการแก้ไขย้อนหลัง ตาม requirement ปัจจุบัน
