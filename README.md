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

เปิด Rancher Desktop ให้พร้อมใช้งาน คัดลอก `.env.example` เป็น `.env` แล้วกำหนดค่าที่ห้ามเว้นว่าง:

```text
APP_KEY=base64:คีย์สุ่มขนาด32ไบต์
DOCKER_DB_PASSWORD=รหัสผ่านฐานข้อมูลที่คาดเดายาก
DOCKER_DB_ROOT_PASSWORD=รหัสผ่าน root ที่แตกต่างจากรหัสผ่านด้านบน
```

สร้าง `APP_KEY` ได้ด้วยคำสั่งต่อไปนี้ แล้วคัดลอกผลลัพธ์ลง `.env`:

```powershell
docker run --rm php:8.3-cli-alpine php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

จากนั้นรัน:

```powershell
docker compose up -d --build
```

เข้าเว็บ:

```text
http://localhost:8000
```

พอร์ตของแอปและฐานข้อมูลจะ bind เฉพาะ `127.0.0.1` โดยค่าเริ่มต้น จึงไม่เปิดให้เครื่องอื่นในเครือข่ายเข้าถึง

### สร้างข้อมูลตัวอย่างครั้งแรก

ระบบจะไม่สร้างบัญชีตัวอย่างอัตโนมัติ หากต้องการใช้ข้อมูลตัวอย่างบนเครื่อง local ให้กำหนดค่าต่อไปนี้ใน `.env` ก่อนเปิด container ครั้งแรก:

```text
AUTO_SEED_DEMO_DATA=true
DEMO_USER_PASSWORD=รหัสผ่านทดสอบที่ยาวอย่างน้อย12ตัวอักษร
```

หลังสร้างข้อมูลแล้วให้เปลี่ยน `AUTO_SEED_DEMO_DATA=false` บัญชีตัวอย่างทั้งหมดจะใช้รหัสผ่านจาก `DEMO_USER_PASSWORD`

หมายเหตุ: ฐานข้อมูลเก็บอยู่ใน Docker volume `broiler-db-data` ข้อมูลจะไม่หายเมื่อ `docker compose up -d --build` หรือ rebuild app container ตราบใดที่ไม่ลบ volume

หากเป็นฐานข้อมูลเดิม การเปลี่ยนค่าใน `.env` เพียงอย่างเดียวจะไม่เปลี่ยนรหัสผ่านที่ MariaDB บันทึกไว้ ต้องหมุนรหัสผ่านผู้ใช้ฐานข้อมูลใน MariaDB ก่อน แล้วจึงแก้ `.env` ให้ตรงกัน

## บัญชีทดสอบ

- Super Admin: `admin@example.com`
- Farm Manager: `nongthanon@example.com`, `kanlueang@example.com`, `nongbon@example.com`, `khoksanuan@example.com`, `banbat@example.com`, `srisuk@example.com`, `narin@example.com`
- รหัสผ่านมาจากค่า `DEMO_USER_PASSWORD` และไม่ควรใช้บัญชีเหล่านี้ใน production

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
- เมื่อปิดรุ่น ระบบจะล็อกการแก้ไขข้อมูลย้อนหลัง และต้องให้ Super Admin ปลดล็อกก่อนแก้ไข
