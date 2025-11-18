GIẢI THÍCH CẤU TRÚC

src
    models: chứa các file PHP (class) để tương tác với CSDL.

    views: chứa các file .php (hoặc template) để hiển thị giao diện người dùng.

    controllers: xử lý logic. 
                Nó sẽ nhận yêu cầu từ người dùng (thông qua public/index.php), 
                lấy dữ liệu từ models, và truyền dữ liệu đó cho views để hiển thị.
    



-------------------------------------------------------------------------------------------------------
B1: Cài Composer -- hoạt động

B2: Điền thông tin DB vào .env --- .env đọc được

B3: Tạo cấu hình đọc .env
/config/config.php ---- chạy ok

B4: Lớp kết nối Database (PDO, singleton)
/src/core/Database.php --- dùng file NLN_NLCS/test-db.php kết nối được MySQL

B5: BƯỚC TIẾP THEO: HIỂN THỊ DỮ LIỆU TỪ DATABASE LÊN TRANG WEB
------------------------------------
                GD: Backend
GD1:

Bước 1: Thiết lập Cấu hình và Autoloading
    A. Tạo file Cấu hình:
    Lưu thông tin kết nối CSDL và các cài đặt chung ở một nơi duy nhất.
    ---config.php

    B. Cấu hình Autoloading cho Composer
    Ra lệnh cho Composer tự động nạp các class trong thư mục src/ mà không cần dùng lệnh require thủ công.
    ---composer.json

Bước 2: Xây dựng các Lớp Lõi (Core Classes)
    A. Lớp Database
    Mục đích: Tạo một lớp duy nhất chịu trách nhiệm kết nối và truy vấn CSDL, giúp tái sử dụng và bảo mật (với PDO).
    ---Database.php trong src/core/

    B. Lớp Router
    Mục đích: Phân tích URL mà người dùng truy cập để quyết định Controller nào và phương thức nào sẽ xử lý yêu cầu đó.
    ---Tạo file Router.php trong src/core/.

Bước 3: Thiết lập Điểm vào và Route đầu tiên
Mục đích: Kết nối tất cả các thành phần lại với nhau tại index.php.
---public/index.php

GD2,3: Tạo Controller và View đầu tiên để trang chủ có thể hiển thị.

Bước 1: Tạo PagesController --src/controllers/

Bước 2: Tạo View cho Trang chủ 
---src/views/home.php

Bước 3: Tạo một Hàm view() tiện ích
---src/helpers.php

Bước 4: Tự động nạp file helpers.php
---composer.json
Thêm khóa "files" vào bên trong "autoload"

terminal: dump-autoload

            GD: Hoàn thiện Giao diện (Styling)frontend

Bước 1: Tạo file CSS với tone màu chủ đạo
---public/css/style.css

Bước 2: Tạo Layout Tái sử dụng (Header & Footer)
---src/views/partials/header.php
---src/views/partials/footer.php

Bước 3: Cập nhật home.php để sử dụng Layout



