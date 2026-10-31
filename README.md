# GunplaHub

GunplaHub là website bán mô hình Gundam và phụ kiện lắp ráp, được xây dựng bằng Laravel. Dự án hỗ trợ khách hàng duyệt sản phẩm, tìm kiếm/lọc, xem nhanh, giỏ hàng, đặt hàng và theo dõi đơn hàng; quản trị viên có thể quản lý sản phẩm, hình ảnh, danh mục, thương hiệu và đơn hàng.

## Tính năng chính

- Giao diện tiếng Việt với Light Mode và Dark Mode.
- Danh sách sản phẩm với tìm kiếm, lọc theo thương hiệu/cấp độ/khoảng giá và sắp xếp.
- Trang chi tiết sản phẩm với gallery nhiều ảnh.
- Quick view, thêm vào giỏ hàng và danh sách yêu thích.
- Đăng ký, đăng nhập, đặt hàng và kiểm tra thông tin giao hàng.
- Thanh toán COD, VietQR và VNPay ở môi trường sandbox.
- Quản lý đơn hàng, sản phẩm, thương hiệu, danh mục và ảnh sản phẩm trong trang quản trị.
- Icon SVG và logo thương hiệu được lưu trong `public/icons` và `public/logos`.

## Công nghệ

- PHP 8.3
- Laravel 13
- SQLite mặc định cho môi trường phát triển
- Bootstrap 5.3 và Tailwind CSS 4
- Vite
- PHPUnit

## Yêu cầu

- PHP >= 8.3
- Composer
- Node.js và npm
- SQLite hoặc MySQL

## Cài đặt trên Windows/Laragon

Mở PowerShell tại thư mục dự án:

```powershell
git clone https://github.com/caonhutduy99/GUNPLAHUB.git
cd GUNPLAHUB
composer run setup
```

Lệnh `composer run setup` sẽ cài dependency, tạo `.env`, tạo application key, chạy migration, cài package frontend và build assets.

Nếu muốn thực hiện từng bước:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

## Cấu hình môi trường

File `.env.example` sử dụng SQLite:

```env
DB_CONNECTION=sqlite
```

Đảm bảo file database tồn tại trước khi migrate:

```powershell
New-Item -ItemType File database\database.sqlite -Force
php artisan migrate
```

Khi dùng MySQL, cập nhật các biến `DB_*` trong `.env`. Không đưa `.env`, khóa API, thông tin thanh toán hoặc thông tin đăng nhập thật lên GitHub.

## Chạy dự án

### Chạy Laravel và Vite cùng lúc

```powershell
composer run dev
```

### Hoặc chạy riêng

Terminal 1:

```powershell
php artisan serve
```

Terminal 2:

```powershell
npm run dev
```

Với Laragon, có thể đặt repository trong thư mục `C:\laragon\www` và truy cập domain tương ứng, ví dụ `http://gunplahub.test`.

## Kiểm tra và định dạng mã nguồn

Chạy test:

```powershell
php artisan test
```

Định dạng PHP bằng Laravel Pint:

```powershell
vendor\bin\pint --dirty --format agent
```

Xóa cache ứng dụng khi cần:

```powershell
php artisan optimize:clear
```

## Cấu trúc thư mục quan trọng

```text
app/
  Http/Controllers/       Controller cho khách hàng và quản trị
  Models/                 Model Eloquent
  Support/                Logic hỗ trợ dùng chung
database/
  migrations/             Cấu trúc cơ sở dữ liệu
  seeders/                Dữ liệu mẫu
public/
  icons/                  Icon SVG
  logos/                  Logo thương hiệu
resources/views/
  layouts/                Layout và footer dùng chung
  shop/                   Danh sách và chi tiết sản phẩm
  admin/                  Giao diện quản trị
routes/web.php            Định nghĩa route website
```

## Triển khai

Trước khi triển khai production:

1. Tạo `.env` riêng cho môi trường production.
2. Đặt `APP_ENV=production` và `APP_DEBUG=false`.
3. Cấu hình database, mail, filesystem và dịch vụ thanh toán thật.
4. Dùng object storage persistent cho ảnh sản phẩm.
5. Chạy:

```powershell
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
```

Không commit file `.env` hoặc thông tin bí mật vào repository.

## Đóng góp

1. Tạo fork hoặc branch mới.
2. Thực hiện thay đổi và bổ sung test phù hợp.
3. Chạy test, Pint và kiểm tra `git diff --check`.
4. Tạo Pull Request với mô tả rõ ràng.

## License
