<?php
// Vị trí: config/config.php

// --- CẤU HÌNH DATABASE ---
// Thay đổi các giá trị này cho phù hợp với môi trường của bạn
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // User của bạn, thường là 'root'
define('DB_PASS', '');     // Mật khẩu của bạn
define('DB_NAME', 'nln_nlcs'); // Tên CSDL bạn đã cung cấp

// --- CẤU HÌNH ĐƯỜNG DẪN ---
// URL gốc của dự án. Rất quan trọng cho việc tạo link sau này.
define('BASE_URL', 'http://localhost/NLN_NLCS');