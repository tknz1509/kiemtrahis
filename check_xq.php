<!DOCTYPE html>
<html>
<head>
    <title>Kiểm tra XQ</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .back-btn {
            margin: 20px 0;
            display: inline-block;
        }
        .highlight {
            background-color: #90ee90; /* Xanh lá nhạt cho mã BN trùng */
        }
        .noi-soi {
            background-color: #87CEEB; /* Xanh dương cho Nội soi */
        }
        .sieu-am-doppler-tim {
            background-color: #FFFF99; /* Vàng cho Siêu âm Doppler tim */
        }
        .sieu-am-o-bung {
            background-color: #D2B48C; /* Nâu cho Siêu âm ổ bụng, Soi cổ tử cung, Siêu âm tuyến giáp */
        }
        .default-service {
            background-color: #FFA07A; /* Cam cho các dịch vụ khác */
        }
        .error {
            background-color: #FF0000; /* Đỏ cho trạng thái vi phạm */
            color: white;
        }
        #check-xq-btn {
            margin: 10px 0;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        #check-xq-btn:hover {
            background-color: #45a049;
        }
        #violation-list {
            margin-top: 10px;
            color: #FF0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kiểm tra XQ</h2>
        <a href="index.php" class="back-btn">Quay lại upload</a>
        <a href="list.php" class="back-btn">Quay lại kiểm tra thông thường</a>
        
        <?php
        // Bắt đầu session
        session_start();

        // Lấy danh sách file từ cache
        $files = isset($_SESSION['uploaded_files_cache']) ? $_SESSION['uploaded_files_cache'] : [];
        
        if(count($files) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Tên file</th><th>Thời gian upload</th><th>Số bản ghi</th><th>Thao tác</th></tr>";
            foreach($files as $file_key => $file) {
                echo "<tr>";
                echo "<td>{$file_key}</td>";
                echo "<td>{$file['file_name']}</td>";
                echo "<td>{$file['upload_time']}</td>";
                echo "<td>{$file['total_records']}</td>";
                echo "<td><a href='?view={$file_key}'>Xem chi tiết</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Chưa có file nào được upload.</p>";
        }
        
        if(isset($_GET['view'])) {
            $file_key = $_GET['view'];
            // Lấy dữ liệu excel liên quan đến file từ cache
            $records = [];
            if (isset($_SESSION['excel_data_cache'])) {
                foreach($_SESSION['excel_data_cache'] as $data_key => $data_array) {
                    foreach($data_array as $record) {
                        if ($record['file_cache_key'] === $file_key) {
                            $records[] = $record;
                        }
                    }
                }
            }
            
            if(count($records) > 0) {
                echo "<h3>Chi tiết nội dung file</h3>";
                echo "<button id='check-xq-btn'>Kiểm tra XQ</button>";
                echo "<div id='violation-list'></div>";
                echo "<table id='data-table'>";
                echo "<tr><th>STT</th><th>Trạng thái</th><th>Mã BN</th><th>Tên bệnh nhân</th><th>Ngày vào viện</th><th>Ngày ra viện</th><th>Ngày y lệnh</th><th>Ngày TH y lệnh</th><th>Ngày kết quả</th><th>Tên dịch vụ</th><th>Đơn giá</th><th>Bác sĩ chỉ định</th><th>Người thực hiện</th><th>Người đọc kết quả</th><th>DS CCHN</th><th>Mã khoa</th></tr>";
                foreach($records as $record) {
                    echo "<tr data-ma-bn='{$record['ma_bn']}' data-ten-dich-vu='{$record['ten_dich_vu']}' data-ngay-th-y-lenh='{$record['ngay_th_y_lenh']}' data-ngay-ket-qua='{$record['ngay_ket_qua']}'>";
                    echo "<td>{$record['stt']}</td>";
                    echo "<td class='trang-thai'>{$record['trang_thai']}</td>";
                    echo "<td>{$record['ma_bn']}</td>";
                    echo "<td>{$record['ten_benh_nhan']}</td>";
                    echo "<td>{$record['ngay_vao_vien']}</td>";
                    echo "<td>{$record['ngay_ra_vien']}</td>";
                    echo "<td>{$record['ngay_y_lenh']}</td>";
                    echo "<td>{$record['ngay_th_y_lenh']}</td>";
                    echo "<td>{$record['ngay_ket_qua']}</td>";
                    echo "<td>{$record['ten_dich_vu']}</td>";
                    echo "<td>{$record['don_gia']}</td>";
                    echo "<td>{$record['bac_si_chi_dinh']}</td>";
                    echo "<td>{$record['nguoi_thuc_hien']}</td>";
                    echo "<td>{$record['nguoi_doc_ket_qua']}</td>";
                    echo "<td>{$record['ds_cchn']}</td>";
                    echo "<td>{$record['ma_khoa']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        ?>
    </div>

    <script>
        // Hàm tô màu theo tên dịch vụ
        function applyColorCoding(rows) {
            for (let i = 1; i < rows.length; i++) {
                const tenDichVu = rows[i].getAttribute('data-ten-dich-vu').toLowerCase();
                rows[i].classList.remove('noi-soi', 'sieu-am-doppler-tim', 'sieu-am-o-bung', 'default-service', 'highlight', 'error');

                if (tenDichVu.includes('nội soi')) {
                    rows[i].classList.add('noi-soi');
                } else if (tenDichVu.includes('siêu âm doppler tim')) {
                    rows[i].classList.add('sieu-am-doppler-tim');
                } else if (tenDichVu.includes('siêu âm ổ bụng') || tenDichVu.includes('soi cổ tử cung') || tenDichVu.includes('siêu âm tuyến giáp')) {
                    rows[i].classList.add('sieu-am-o-bung');
                } else {
                    rows[i].classList.add('default-service');
                }
            }
        }

        // Logic cho nút "Kiểm tra XQ"
        document.getElementById('check-xq-btn').addEventListener('click', function() {
            const table = document.getElementById('data-table');
            const rows = table.getElementsByTagName('tr');
            let records = [];
            let violations = [];

            // Thu thập dữ liệu
            for (let i = 1; i < rows.length; i++) {
                const ngayThYLenh = new Date(rows[i].getAttribute('data-ngay-th-y-lenh'));
                const tenDichVu = rows[i].getAttribute('data-ten-dich-vu').toLowerCase();
                const tenBenhNhan = rows[i].querySelector('td:nth-child(4)').innerText;

                records.push({
                    row: rows[i],
                    ngayThYLenh: ngayThYLenh,
                    tenDichVu: tenDichVu,
                    tenBenhNhan: tenBenhNhan,
                    index: i - 1
                });
            }

            // Tô màu theo tên dịch vụ
            applyColorCoding(rows);

            // Sắp xếp theo thời gian NGÀY TH Y LỆNH để kiểm tra khoảng cách
            records.sort((a, b) => a.ngayThYLenh - b.ngayThYLenh);

            // Kiểm tra khoảng cách thời gian với ca trước và ca sau
            for (let i = 0; i < records.length; i++) {
                const currentTime = records[i].ngayThYLenh;
                let hasViolation = false;

                // Kiểm tra với ca trước
                if (i > 0) {
                    const prevTime = records[i - 1].ngayThYLenh;
                    const diffMinutesPrev = (currentTime - prevTime) / (1000 * 60);
                    if (diffMinutesPrev < 3) {
                        records[i].row.querySelector('.trang-thai').classList.add('error');
                        violations.push(`${records[i].tenBenhNhan} - ${records[i].tenDichVu}: Khoảng cách với ca trước không đủ (${diffMinutesPrev.toFixed(1)} phút < 3 phút)`);
                        hasViolation = true;
                    }
                }

                // Kiểm tra với ca sau
                if (i < records.length - 1) {
                    const nextTime = records[i + 1].ngayThYLenh;
                    const diffMinutesNext = (nextTime - currentTime) / (1000 * 60);
                    if (diffMinutesNext < 3) {
                        records[i].row.querySelector('.trang-thai').classList.add('error');
                        if (!hasViolation) {
                            violations.push(`${records[i].tenBenhNhan} - ${records[i].tenDichVu}: Khoảng cách với ca sau không đủ (${diffMinutesNext.toFixed(1)} phút < 3 phút)`);
                        }
                    }
                }
            }

            // Hiển thị kết quả
            document.getElementById('violation-list').innerHTML = violations.length > 0 
                ? "Các ca vi phạm:<br>" + violations.join('<br>') 
                : "Không có ca vi phạm.";
        });
    </script>
</body>
</html>