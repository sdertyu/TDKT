## Hệ thống quản lý Thi đua - Khen thưởng

-   Hướng dẫn cài đặt môi trường và công cụ cho dự án Laravel + Vuetify từ GitHub có thể phức tạp, tùy thuộc vào cấu trúc dự án.
-   Dường như cần PHP phiên bản 8.1 trở lên, Node.js phiên bản 14 trở lên, Git, cơ sở dữ liệu như MySQL hoặc PostgreSQL, và một trình soạn thảo mã.
-   Các bước bao gồm clone dự án, cài đặt dependencies, thiết lập cơ sở dữ liệu, và chạy server cho cả backend và frontend.

## Hướng Dẫn Cài Đặt

### Điều Kiện Tiên Quyết

Để bắt đầu, bạn cần đảm bảo có các công cụ sau:
-   PHP: Phiên bản 8.1 hoặc cao hơn, phù hợp với yêu cầu của Laravel 10. [Download](https://laragon.org/download/)
-   Node.js: Phiên bản 14 hoặc cao hơn, cần cho các dependencies frontend [Download](https://nodejs.org/en/download).
-   Git: Để clone dự án từ GitHub [Download](https://git-scm.com/downloads).
-   Cơ sở dữ liệu: MySQL (Laragon), nếu dự án sử dụng cơ sở dữ liệu.
-   Trình soạn thảo mã: Như Visual Studio Code hoặc IDE khác để viết và quản lý mã.
* <b>Lưu ý: </b> Truy cập file <mark>composer.json</mark> và <mark>package.json</mark> để kiểm tra phiên bản dependencies project đang chạy.

## Cấu hình cho project

1. Clone dự án từ GitHub:

```sh
  git clone https://github.com/sdertyu/TDKT.git
```

2. Mở dự án lên trong Visual Studio Code
3. Cài đặt dependencies backend:
- Cài composer:
```sh
  composer install
```
- Sao chép tệp <mark>.evn</mark>
```sh
  cp .env.example .env
```
- Mở file <mark>.evn</mark> và thiết lập kết nối cơ sở dữ liệu:
```sh
    DB_CONNECTION=mysql  # hoặc postgres
    DB_HOST=127.0.0.1
    DB_PORT=3306  # hoặc cổng của cơ sở dữ liệu
    DB_DATABASE=ten-co-so-du-lieu
    DB_USERNAME=ten-nguoi-dung
    DB_PASSWORD=mat-khau
```
- Tạo app key cho <mark>.evn</mark>
```sh
  php artisan key:generate
```
- Chạy migrations cơ sở dữ liệu:
```sh
  php artisan migrate
```
- Cài đặt dependencies frontend:
```sh
  npm install
```
4. Chạy thử ứng dụng
* Chạy môi trường frontend
```sh
  npm run dev
```
* Chạy môi trường backend
```sh
  php artisan serve
```
* Truy cập đường dẫn [http://127.0.0.1:8000]