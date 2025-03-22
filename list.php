<!DOCTYPE html>
<html>
<head>
    <title>Danh sách file Excel đã upload</title>
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
        #check-btn {
            margin: 10px 0;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        #check-btn:hover {
            background-color: #45a049;
        }
        #duplicate-count {
            margin-top: 10px;
            font-weight: bold;
        }
        #violation-list {
            margin-top: 10px;
            color: #FF0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Danh sách file Excel đã upload</h2>
        <a href="index.php" class="back-btn">Quay lại upload</a>
        <a href="check_xq.php" class="back-btn">Kiểm tra XQ</a>
        
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
                echo "<button id='check-btn'>Kiểm tra</button>";
                echo "<div id='duplicate-count'></div>";
                echo "<div id='violation-list'></div>";
                echo "<table id='data-table'>";
                echo "<tr><th>STT</th><th>Trạng thái</th><th>Mã BN</th><th>Tên bệnh nhân</th><th>Ngày vào viện</th><th>Ngày ra viện</th><th>Ngày y lệnh</th><th>Ngày TH y lệnh</th><th>Ngày kết quả</th><th>Tên dịch vụ</th><th>Đơn giá</th><th>Bác sĩ chỉ định</th><th>Người thực hiện</th><th>Người đọc kết quả</th><th>DS CCHN</th><th>Mã khoa</th></tr>";
                foreach($records as $record) {
                    // Đảm bảo các giá trị null được xử lý
                    $ngay_th_y_lenh = $record['ngay_th_y_lenh'] ?? '';
                    $ngay_ket_qua = $record['ngay_ket_qua'] ?? '';
                    $ten_dich_vu = $record['ten_dich_vu'] ?? '';
                    $ma_bn = $record['ma_bn'] ?? '';
                    $trang_thai = $record['trang_thai'] ?? '';
                    $ten_benh_nhan = $record['ten_benh_nhan'] ?? '';
                    $ngay_vao_vien = $record['ngay_vao_vien'] ?? '';
                    $ngay_ra_vien = $record['ngay_ra_vien'] ?? '';
                    $ngay_y_lenh = $record['ngay_y_lenh'] ?? '';
                    $don_gia = $record['don_gia'] ?? '';
                    $bac_si_chi_dinh = $record['bac_si_chi_dinh'] ?? '';
                    $nguoi_thuc_hien = $record['nguoi_thuc_hien'] ?? '';
                    $nguoi_doc_ket_qua = $record['nguoi_doc_ket_qua'] ?? '';
                    $ds_cchn = $record['ds_cchn'] ?? '';
                    $ma_khoa = $record['ma_khoa'] ?? '';
                    $stt = $record['stt'] ?? '';

                    echo "<tr data-ma-bn='{$ma_bn}' data-ten-dich-vu='{$ten_dich_vu}' data-ngay-th-y-lenh='{$ngay_th_y_lenh}' data-ngay-ket-qua='{$ngay_ket_qua}'>";
                    echo "<td>{$stt}</td>";
                    echo "<td class='trang-thai'>{$trang_thai}</td>";
                    echo "<td>{$ma_bn}</td>";
                    echo "<td>{$ten_benh_nhan}</td>";
                    echo "<td>{$ngay_vao_vien}</td>";
                    echo "<td>{$ngay_ra_vien}</td>";
                    echo "<td>{$ngay_y_lenh}</td>";
                    echo "<td>{$ngay_th_y_lenh}</td>";
                    echo "<td>{$ngay_ket_qua}</td>";
                    echo "<td>{$ten_dich_vu}</td>";
                    echo "<td>{$don_gia}</td>";
                    echo "<td>{$bac_si_chi_dinh}</td>";
                    echo "<td>{$nguoi_thuc_hien}</td>";
                    echo "<td>{$nguoi_doc_ket_qua}</td>"; // Sửa từ $ngay_doc_ket_qua thành $nguoi_doc_ket_qua
                    echo "<td>{$ds_cchn}</td>";
                    echo "<td>{$ma_khoa}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Không tìm thấy dữ liệu cho file này.</p>";
            }
        }
        ?>
    </div>

    <script>
        document.getElementById('check-btn').addEventListener('click', function() {
            const table = document.getElementById('data-table');
            const rows = table.getElementsByTagName('tr');
            const maBnCount = {};
            const maBnRecords = {};
            let violations = [];

            // Thu thập dữ liệu từ bảng
            for (let i = 1; i < rows.length; i++) {
                const maBn = rows[i].getAttribute('data-ma-bn') || '';
                const ngayThYLenh = rows[i].getAttribute('data-ngay-th-y-lenh') ? new Date(rows[i].getAttribute('data-ngay-th-y-lenh')) : null;
                const ngayKetQua = rows[i].getAttribute('data-ngay-ket-qua') ? new Date(rows[i].getAttribute('data-ngay-ket-qua')) : null;
                const tenDichVu = (rows[i].getAttribute('data-ten-dich-vu') || '').toLowerCase();
                const tenBenhNhan = rows[i].querySelector('td:nth-child(4)').innerText || '';

                // Chỉ thêm bản ghi nếu có dữ liệu hợp lệ
                if (maBn && ngayThYLenh && ngayKetQua) {
                    maBnCount[maBn] = (maBnCount[maBn] || 0) + 1;
                    if (!maBnRecords[maBn]) {
                        maBnRecords[maBn] = [];
                    }
                    maBnRecords[maBn].push({
                        row: rows[i],
                        ngayThYLenh: ngayThYLenh,
                        ngayKetQua: ngayKetQua,
                        tenDichVu: tenDichVu,
                        tenBenhNhan: tenBenhNhan,
                        index: i - 1
                    });
                }
            }

            // Tô màu
            for (let i = 1; i < rows.length; i++) {
                const maBn = rows[i].getAttribute('data-ma-bn') || '';
                const tenDichVu = (rows[i].getAttribute('data-ten-dich-vu') || '').toLowerCase();
                rows[i].classList.remove('noi-soi', 'sieu-am-doppler-tim', 'sieu-am-o-bung', 'default-service', 'highlight', 'error');

                if (maBnCount[maBn] > 1) {
                    rows[i].classList.add('highlight');
                }

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

            // Kiểm tra vi phạm
            for (const maBn in maBnRecords) {
                if (maBnCount[maBn] > 1) {
                    const records = maBnRecords[maBn];

                    // Kiểm tra khoảng cách thời gian
                    for (let j = 0; j < records.length; j++) {
                        const currentTime = records[j].ngayThYLenh;
                        let nextTime = null;
                        let nextRecord = null;

                        for (let k = 0; k < records.length; k++) {
                            if (k !== j) {
                                const otherTime = records[k].ngayThYLenh;
                                if (otherTime > currentTime) {
                                    if (!nextTime || otherTime < nextTime) {
                                        nextTime = otherTime;
                                        nextRecord = records[k];
                                    }
                                }
                            }
                        }

                        if (nextTime && nextRecord) {
                            const diffMinutes = (nextTime - currentTime) / (1000 * 60);
                            let minGap = 3;
                            if (records[j].tenDichVu.includes('nội soi')) {
                                minGap = 16;
                            } else if (records[j].tenDichVu.includes('siêu âm doppler tim')) {
                                minGap = 8;
                            } else if (records[j].tenDichVu.includes('siêu âm ổ bụng') || records[j].tenDichVu.includes('soi cổ tử cung') || records[j].tenDichVu.includes('siêu âm tuyến giáp')) {
                                minGap = 5;
                            }

                            if (diffMinutes < minGap) {
                                records[j].row.querySelector('.trang-thai').classList.add('error');
                                violations.push(`${records[j].tenBenhNhan} - ${records[j].tenDichVu}: Khoảng cách không đủ (${diffMinutes.toFixed(1)} phút < ${minGap} phút)`);
                            }
                        }
                    }

                    // Kiểm tra chồng lấn thời gian
                    for (let j = 0; j < records.length; j++) {
                        const isSpecialService = records[j].tenDichVu.includes('nội soi') || 
                                                records[j].tenDichVu.includes('siêu âm doppler tim') || 
                                                records[j].tenDichVu.includes('siêu âm ổ bụng') || 
                                                records[j].tenDichVu.includes('soi cổ tử cung') || 
                                                records[j].tenDichVu.includes('siêu âm tuyến giáp');
                        
                        if (isSpecialService) {
                            const startTime = records[j].ngayThYLenh;
                            const endTime = records[j].ngayKetQua;

                            for (let k = 0; k < records.length; k++) {
                                if (k !== j) {
                                    const otherStart = records[k].ngayThYLenh;
                                    const isOtherCam = !records[k].tenDichVu.includes('nội soi') && 
                                                       !records[k].tenDichVu.includes('siêu âm doppler tim') && 
                                                       !records[k].tenDichVu.includes('siêu âm ổ bụng') && 
                                                       !records[k].tenDichVu.includes('soi cổ tử cung') && 
                                                       !records[k].tenDichVu.includes('siêu âm tuyến giáp');

                                    if (isOtherCam && otherStart > startTime && otherStart < endTime) {
                                        records[k].row.querySelector('.trang-thai').classList.add('error');
                                        violations.push(`${records[k].tenBenhNhan} - ${records[k].tenDichVu}: Lấn vào thời gian của ${records[j].tenDichVu}`);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Đếm số mã BN trùng
            let duplicateMaBnCount = 0;
            for (const maBn in maBnCount) {
                if (maBnCount[maBn] > 1) {
                    duplicateMaBnCount++;
                }
            }

            // Hiển thị kết quả
            document.getElementById('duplicate-count').innerText = `Tổng số mã BN trùng: ${duplicateMaBnCount}`;
            document.getElementById('violation-list').innerHTML = violations.length > 0 
                ? "Các ca vi phạm:<br>" + violations.join('<br>') 
                : "Không có ca vi phạm.";
        });
    </script>
</body>
</html>