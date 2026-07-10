# คู่มือสำหรับ Agent

เอกสารนี้เป็นบริบทและแนวทางสำหรับเอเจนต์ที่ทำงานกับโปรเจคนี้

## ภาพรวมโปรเจค

โปรเจคนี้คือเว็บแอปภาษาไทยสำหรับบันทึกและติดตามการเลี้ยงไก่เนื้อ แยกตามฟาร์ม รุ่นการเลี้ยง และเล้า ฟีเจอร์หลักประกอบด้วย:

- ผู้ใช้และสิทธิ์ `Super Admin` / `Farm Manager`
- ฟาร์ม เล้า และรุ่นการเลี้ยง (flock)
- บันทึกรายวัน อุณหภูมิ ความชื้น ตาย และคัดทิ้ง
- มิเตอร์น้ำ รับอาหาร น้ำหนัก จับขาย และข้อมูลโรงเชือด
- ยามาสเตอร์ ยาลูกไก่ อาหาร และทีมจับไก่
- รายงานสรุป ใบหน้าเล้า รายงานการสูญเสีย รายงานผลผลิต และ export PDF/Excel
- การปิดรุ่นโดยตรวจสอบยอดไก่เข้าและยอดปลายทาง

## เทคโนโลยี

- Laravel 12, PHP 8.2+ (Docker ใช้ PHP 8.3)
- MariaDB 11.4 / MySQL ผ่าน Eloquent ORM
- Blade, Laravel Breeze, Tailwind CSS, Alpine.js, Vite
- PHPUnit 11 ผ่าน Laravel test runner
- `mPDF` สำหรับ PDF และ `PhpSpreadsheet` สำหรับ Excel

## โครงสร้างที่ควรรู้

- `app/Http/Controllers/` — controller ของแต่ละหน้าหรือกระบวนการ
- `app/Http/Requests/` — validation และ authorization ของ request
- `app/Models/` — Eloquent models และความสัมพันธ์
- `app/Services/` — business logic ที่ใช้ร่วมกัน เช่น `PoultryCalculationService`
- `app/Support/` — helper และ logic สำหรับจำกัดข้อมูลตามฟาร์ม
- `database/migrations/` — schema และการเปลี่ยนแปลงฐานข้อมูลตามลำดับเวลา
- `database/seeders/` — ข้อมูลเริ่มต้นและข้อมูล master
- `resources/views/` — Blade templates แยกตามฟีเจอร์
- `routes/web.php` — web routes และ route shortcut ไปยัง active flock
- `tests/Feature/`, `tests/Unit/` — integration/HTTP tests และ unit tests

## การเริ่มใช้งาน

### Docker Compose (วิธีหลัก)

ต้องเปิด Docker หรือ Rancher Desktop ก่อน จาก root ของโปรเจค ให้คัดลอก `.env.example` เป็น `.env` และกำหนด `APP_KEY`, `DOCKER_DB_PASSWORD` และ `DOCKER_DB_ROOT_PASSWORD` เป็นค่าสุ่มที่คาดเดายากก่อน จากนั้นรัน:

```bash
docker compose up -d --build
```

แอปอยู่ที่ `http://localhost:8000` และฐานข้อมูล MariaDB เปิดที่ port `3319` เฉพาะ `127.0.0.1` บนเครื่อง host

คำสั่งที่ใช้บ่อย:

```bash
docker compose ps
docker compose logs -f app
docker exec broiler-app php artisan migrate --force
docker exec broiler-app php artisan db:seed --force
docker exec broiler-app php artisan migrate:status
docker compose down
```

ห้ามใช้ `docker compose down -v` เว้นแต่ผู้ใช้ต้องการลบข้อมูล เพราะข้อมูลฐานข้อมูลอยู่ใน volume `broiler-db-data`

### รันบนเครื่องโดยตรง

ต้องมี PHP, Composer, Node.js และฐานข้อมูลที่ตรงกับ `.env`:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

สำหรับ development แบบ hot reload ใช้:

```bash
composer run dev
```

หรือแยก process เป็น `php artisan serve` และ `npm run dev`

## การทดสอบและตรวจคุณภาพ

รันชุดทดสอบทั้งหมดด้วย:

```bash
composer test
```

Docker image สำหรับ production ไม่ติดตั้ง dev dependency จึงไม่มี test runner ให้รัน test ใน development environment ที่ติดตั้ง Composer dependency ครบแทน

คำสั่งเพิ่มเติม:

```bash
php artisan test --filter=PoultryCalculationServiceTest
./vendor/bin/pint --test
```

เมื่อแก้ business logic หรือ validation ให้เพิ่ม/ปรับ test ที่เกี่ยวข้องเสมอ โดยเฉพาะการคำนวณยอดไก่ การจำกัดข้อมูลตามฟาร์ม และการปิดรุ่น

## แนวทางการพัฒนา

1. อ่าน route, controller, request, model, migration และ view ที่เกี่ยวข้องก่อนแก้ไข
2. ทำให้การตรวจสิทธิ์และการจำกัดข้อมูลตามฟาร์มอยู่ใน flow เดียวกันกับฟีเจอร์เดิม อย่าอาศัยเพียงการซ่อนเมนูใน Blade
3. ใช้ Form Request สำหรับ validation ที่มีอยู่แล้ว และใช้ Eloquent relationships แทน query ที่ทำให้ logic ซ้ำ
4. เก็บ business logic ที่ซับซ้อนหรือใช้ซ้ำไว้ใน service/support class ไม่ใส่ไว้ใน Blade
5. เมื่อเปลี่ยน schema ให้สร้าง migration ใหม่ ห้ามแก้ migration ที่อาจถูกใช้งานไปแล้ว เว้นแต่ตรวจสอบแล้วว่าเป็น migration ใหม่ที่ยังไม่ถูก deploy
6. เมื่อเพิ่ม field ให้ตรวจให้ครบทั้ง migration, model `$fillable`/casts, request validation, controller และ view
7. ใช้ข้อความและ label ภาษาไทยตามรูปแบบของระบบเดิม และรักษา layout/component ที่มีอยู่ก่อนสร้างของใหม่
8. อย่าแก้ไขไฟล์หรือ revert การเปลี่ยนแปลงที่ไม่เกี่ยวข้องกับงาน เพราะ working tree อาจมีงานของผู้ใช้อยู่ก่อนแล้ว

## กฎสำคัญของโดเมน

- `Farm Manager` ต้องเห็นและแก้ไขได้เฉพาะข้อมูลของฟาร์มที่ผูกไว้ ส่วน `Super Admin` จึงจัดการข้อมูลข้ามฟาร์มได้
- ข้อมูลการทำงานส่วนใหญ่ต้องอยู่ภายใต้ `flock` และต้องตรวจว่า flock เป็นของฟาร์มที่ผู้ใช้เข้าถึงได้ก่อนดำเนินการ
- route shortcut เช่น `/daily-records`, `/summary`, `/water-meters` จะเลือก active flock ของผู้ใช้ หากไม่มีให้ redirect ไปหน้า flocks พร้อมข้อความแจ้งเตือน
- การปิดรุ่นต้องตรวจยอดให้สอดคล้องกับสูตร:

  ```text
  ไก่เข้า = ตายสะสม + คัดทิ้งสะสม + จับขายรวม
  ```

- การคำนวณยอด น้ำหนัก ราคา และผลผลิตควรใช้ logic ใน `app/Services/PoultryCalculationService.php` หรือ service ที่เหมาะสม ไม่คำนวณซ้ำแบบ ad hoc ใน view
- ตัวเลขทางการเงินและน้ำหนักต้องระวัง precision, rounding และค่า null ให้สอดคล้องกับ logic เดิม
- วันที่ของระบบเป็นข้อมูลภาษาไทย แต่ควรรักษารูปแบบวันที่ที่ model/database และ helper เดิมใช้อยู่
- ข้อมูล master ที่ใช้ร่วมกันหลายฟาร์มต้องไม่ถูกกรองแบบ farm-local โดยไม่มีเหตุผลทางธุรกิจ

## แนวทางการแก้ไข UI

- ใช้ Blade components และ class ของ Tailwind ที่มีอยู่แล้วให้มากที่สุด
- รักษา responsive layout สำหรับหน้าจอ desktop และ mobile
- ระวังตารางข้อมูลที่มีหลายคอลัมน์ โดยเฉพาะหน้ารายวัน ใบหน้าเล้า และรายงานผลผลิต
- ตรวจข้อความ validation, flash message, empty state และ error state ให้ครบ

## ก่อนส่งมอบงาน

ตรวจอย่างน้อยตามขอบเขตที่แก้ไข:

- `php artisan test` หรือ test เฉพาะส่วนที่เกี่ยวข้อง
- `./vendor/bin/pint --test` หากแก้ PHP
- `npm run build` หากแก้ `resources/`, `resources/js/`, CSS หรือ config ของ Vite/Tailwind
- `php artisan route:list` หากแก้ route
- ตรวจ migration และ seed ในสภาพแวดล้อม Docker หากแก้ฐานข้อมูล
- สรุปไฟล์ที่แก้ คำสั่งที่รัน และข้อจำกัด/สิ่งที่ยังไม่ได้ตรวจให้ผู้ใช้ทราบ

## ข้อมูลสำหรับการเข้าสู่ระบบทดสอบ

ข้อมูลตัวอย่างที่ระบุใน README ใช้สำหรับ local development เท่านั้น ห้ามใช้บัญชีเหล่านี้ใน production:

- Super Admin: `admin@example.com`
- Farm Manager ตัวอย่าง: `nongthanon@example.com`, `kanlueang@example.com`, `nongbon@example.com` และบัญชีฟาร์มอื่นใน `README.md`
- ระบบไม่ seed บัญชีเหล่านี้อัตโนมัติ ต้องเปิด `AUTO_SEED_DEMO_DATA` และกำหนด `DEMO_USER_PASSWORD` ที่ยาวอย่างน้อย 12 ตัวอักษรเอง
- ฐานข้อมูลเก่าที่เคย seed ด้วยรหัสผ่านเริ่มต้นต้องเปลี่ยนรหัสผ่านของทุกบัญชีทันที

## ข้อควรระวัง

- ไฟล์ `STD.xlsx`, ไฟล์ `.txt` และสคริปต์ใน `scratch/` เป็นข้อมูล/เครื่องมือประกอบงานเฉพาะ ไม่ควรแก้หรือลบหากผู้ใช้ไม่ได้ร้องขอ
- อย่า commit secret จาก `.env` หรือข้อมูล production
- ก่อนรันคำสั่ง import, seed, migrate แบบ destructive หรือเปลี่ยนข้อมูลจำนวนมาก ต้องแจ้งผลกระทบให้ชัดเจน
- หาก requirement ขัดกับกฎยอดหรือสิทธิ์ของระบบ ให้หยุดและรายงานความขัดแย้งก่อนเลือกพฤติกรรมเอง
