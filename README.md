# GunplaHub

GunplaHub là website bán mô hình Gundam (Gunpla) và phụ kiện lắp ráp, xây dựng bằng Laravel cho đồ án học phần. Khách hàng có thể duyệt sản phẩm, tìm kiếm/lọc, thêm giỏ hàng, đặt hàng và theo dõi đơn; quản trị viên quản lý sản phẩm, danh mục, thương hiệu, voucher, đơn hàng và xem báo cáo.

## Tính năng chính

**Khách hàng**
- Giao diện tiếng Việt, hỗ trợ Light/Dark Mode.
- Tìm kiếm, lọc theo thương hiệu/cấp độ/series/khoảng giá và sắp xếp; trang chi tiết có gallery nhiều ảnh.
- Giỏ hàng, danh sách yêu thích, mã giảm giá.
- Đặt hàng với COD, VietQR và VNPay (chỉ ở môi trường sandbox).
- Theo dõi đơn hàng, hủy đơn, yêu cầu trả hàng kèm ảnh minh chứng, xem hóa đơn.
- Đăng ký, đăng nhập, quên/đặt lại mật khẩu, cập nhật hồ sơ, liên hệ cửa hàng.

**Quản trị**
- Dashboard, quản lý đơn hàng theo quy trình trạng thái (kèm vận đơn giả lập).
- Quản lý sản phẩm (nhiều ảnh, lịch sử giá), danh mục, thương hiệu, voucher.
- Quản lý khách hàng và danh sách đen, hộp thư liên hệ.
- Báo cáo doanh thu và tồn kho, xuất CSV, nhật ký hoạt động (audit log).

## Công nghệ

- PHP 8.3, Laravel 13
- MySQL (Laragon) cho môi trường chạy; SQLite in-memory cho test
- Bootstrap 5.3, Bootstrap Icons, Chart.js và Google Fonts nạp qua CDN, nên **cần có Internet** khi chạy
- PHPUnit

## Yêu cầu

- PHP >= 8.3, Composer
- MySQL (đi kèm Laragon)
- Node.js/npm chỉ cần nếu muốn dùng Vite; giao diện hiện tại không phụ thuộc vào bước build này

## Cài đặt trên Windows/Laragon

1. Đặt dự án trong `C:\laragon\www\GUNPLAHUB`, bật Apache/Nginx và MySQL trong Laragon.
2. Tạo database `gunplahub` (HeidiSQL hoặc `mysql -u root -e "CREATE DATABASE gunplahub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"`).
3. Mở terminal tại thư mục dự án:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
```

4. Truy cập `http://gunplahub.test` (hoặc `php artisan serve` rồi mở `http://127.0.0.1:8000`).

`.env.example` đã cấu hình sẵn MySQL của Laragon (`root`, mật khẩu rỗng). Chỉnh các biến `DB_*` nếu máy bạn khác.

### Tài khoản demo (chỉ dùng khi phát triển)

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Quản trị | admin@gunplahub.test | admin123 |
| Khách hàng | khach1@gunplahub.test | khach123 |
| Khách hàng bị blacklist | blacklist@gunplahub.test | khach123 |

Đổi hoặc xóa các tài khoản này trước khi đưa lên môi trường thật. Không đưa `.env`, khóa API hay thông tin thanh toán thật lên GitHub.

### Nếu database đã được seed bằng phiên bản cũ

Chạy `php artisan migrate` để áp dụng migration mới: gộp các danh mục bị trùng tên và mở rộng cột trạng thái thanh toán (thêm "Chờ hoàn tiền", "Đã hoàn tiền"). Dữ liệu sản phẩm và đơn hàng được giữ nguyên.

## Thanh toán và vận chuyển (sandbox)

VietQR, VNPay và đơn vị vận chuyển chỉ là mô phỏng (`PAYMENTS_SANDBOX`, `SHIPPING_SANDBOX` trong `.env`), không phát sinh giao dịch thật. Khi hủy hoặc hoàn trả đơn đã thanh toán online, hệ thống chỉ **ghi nhận** "Chờ hoàn tiền"/"Đã hoàn tiền"; việc chuyển tiền thực tế do cửa hàng thực hiện ngoài hệ thống.

## Kiểm tra và định dạng mã nguồn

```powershell
php artisan test
vendor\bin\pint --dirty
php artisan optimize:clear
```

Test chạy trên SQLite in-memory (cấu hình trong `phpunit.xml`), không ảnh hưởng database MySQL của bạn.

## Cấu trúc thư mục quan trọng

```text
app/
  Http/Controllers/       Controller cho khách hàng và quản trị
  Models/                 Model Eloquent
  Services/               Nghiệp vụ đặt hàng, vận chuyển
  Support/                Logic hỗ trợ dùng chung
database/
  migrations/             Cấu trúc cơ sở dữ liệu
  seeders/                Dữ liệu mẫu
public/
  images/                 Ảnh sản phẩm, ảnh minh chứng trả hàng (không commit)
  icons/, logos/          Icon SVG và logo thương hiệu
resources/views/          Giao diện Blade (layouts, shop, admin...)
routes/web.php            Định nghĩa route website
```

## Triển khai

1. Tạo `.env` riêng, đặt `APP_ENV=production`, `APP_DEBUG=false`.
2. Cấu hình database, mail, lưu trữ ảnh bền vững và cổng thanh toán thật (tắt sandbox).
3. Chạy:

```powershell
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

## License

MIT
