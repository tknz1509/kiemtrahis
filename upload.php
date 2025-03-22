<?php
require_once 'config.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Bắt đầu session để sử dụng cache
session_start();

// Khởi tạo mảng cache nếu chưa tồn tại
if (!isset($_SESSION['excel_data_cache'])) {
    $_SESSION['excel_data_cache'] = array();
}
if (!isset($_SESSION['uploaded_files_cache'])) {
    $_SESSION['uploaded_files_cache'] = array();
}

// Định nghĩa hàm convertDateTime (giữ nguyên)
function convertDateTime($value) {
    if (empty($value)) {
        return null;
    }
    $dateTime = DateTime::createFromFormat('d/m/Y H:i', $value);
    if ($dateTime === false) {
        error_log("Invalid datetime format: " . $value);
        return null;
    }
    return $dateTime->format('Y-m-d H:i:s');
}

if(isset($_POST['submit'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $file_name = $_FILES['excel_file']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if($file_ext != 'xls' && $file_ext != 'xlsx') {
        echo "Please upload only Excel files (.xls or .xlsx)";
        exit;
    }
    
    $new_file_name = uniqid() . '.' . $file_ext;
    move_uploaded_file($file, "uploads/" . $new_file_name);
    
    try {
        $spreadsheet = IOFactory::load("uploads/" . $new_file_name);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        
        // Tổng số bản ghi thực tế trong file (trừ header)
        $total_records = $highestRow - 1;
        
        // Giới hạn số hàng xử lý tối đa là 400
        $maxRowsToProcess = min($total_records, 400);
        $highestRowToProcess = min($highestRow, 401);
        
        // Lưu thông tin file vào uploaded_files_cache thay vì database
        $file_cache_key = uniqid('file_');
        $_SESSION['uploaded_files_cache'][$file_cache_key] = [
            'file_name' => $file_name,
            'total_records' => $total_records,
            'upload_time' => date('Y-m-d H:i:s')
        ];
        
        // Tạo key cho dữ liệu excel
        $data_cache_key = uniqid('data_');
        $excel_data = [];
        
        for($row = 2; $row <= $highestRowToProcess; $row++) {
            $stt = $worksheet->getCell('A' . $row)->getValue();
            $trang_thai = $worksheet->getCell('B' . $row)->getValue();
            $ma_bn = $worksheet->getCell('C' . $row)->getValue();
            $ten_benh_nhan = $worksheet->getCell('D' . $row)->getValue();
            
            // Lấy giá trị thời gian từ Excel và chuyển đổi đồng nhất
            $ngay_vao_vien = $worksheet->getCell('E' . $row)->getFormattedValue();
            $ngay_ra_vien = $worksheet->getCell('F' . $row)->getFormattedValue();
            $ngay_y_lenh = $worksheet->getCell('G' . $row)->getFormattedValue();
            $ngay_th_y_lenh = $worksheet->getCell('H' . $row)->getFormattedValue();
            $ngay_ket_qua = $worksheet->getCell('I' . $row)->getFormattedValue();
            
            // Chuyển đổi tất cả cột thời gian
            $ngay_vao_vien = convertDateTime($ngay_vao_vien);
            $ngay_ra_vien = convertDateTime($ngay_ra_vien);
            $ngay_y_lenh = convertDateTime($ngay_y_lenh);
            $ngay_th_y_lenh = convertDateTime($ngay_th_y_lenh);
            $ngay_ket_qua = convertDateTime($ngay_ket_qua);
            
            $ten_dich_vu = $worksheet->getCell('J' . $row)->getValue();
            $don_gia = $worksheet->getCell('K' . $row)->getValue();
            $bac_si_chi_dinh = $worksheet->getCell('L' . $row)->getValue();
            $nguoi_thuc_hien = $worksheet->getCell('M' . $row)->getValue();
            $nguoi_doc_ket_qua = $worksheet->getCell('N' . $row)->getValue();
            $ds_cchn = $worksheet->getCell('O' . $row)->getValue();
            $ma_khoa = $worksheet->getCell('P' . $row)->getValue();
            
            // Lưu dữ liệu vào mảng thay vì database
            $excel_data[] = [
                'stt' => $stt,
                'trang_thai' => $trang_thai,
                'ma_bn' => $ma_bn,
                'ten_benh_nhan' => $ten_benh_nhan,
                'ngay_vao_vien' => $ngay_vao_vien,
                'ngay_ra_vien' => $ngay_ra_vien,
                'ngay_y_lenh' => $ngay_y_lenh,
                'ngay_th_y_lenh' => $ngay_th_y_lenh,
                'ngay_ket_qua' => $ngay_ket_qua,
                'ten_dich_vu' => $ten_dich_vu,
                'don_gia' => $don_gia,
                'bac_si_chi_dinh' => $bac_si_chi_dinh,
                'nguoi_thuc_hien' => $nguoi_thuc_hien,
                'nguoi_doc_ket_qua' => $nguoi_doc_ket_qua,
                'ds_cchn' => $ds_cchn,
                'ma_khoa' => $ma_khoa,
                'file_cache_key' => $file_cache_key // Liên kết với file upload
            ];
        }
        
        // Lưu toàn bộ dữ liệu vào cache
        $_SESSION['excel_data_cache'][$data_cache_key] = $excel_data;
        
        // Xóa file tạm
        unlink("uploads/" . $new_file_name);
        header("Location: list.php");
        exit;
        
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Hàm hỗ trợ lấy dữ liệu từ cache (nếu cần sau này)
function getExcelDataFromCache($data_cache_key) {
    return isset($_SESSION['excel_data_cache'][$data_cache_key]) ? 
           $_SESSION['excel_data_cache'][$data_cache_key] : null;
}

function getUploadedFileInfo($file_cache_key) {
    return isset($_SESSION['uploaded_files_cache'][$file_cache_key]) ? 
           $_SESSION['uploaded_files_cache'][$file_cache_key] : null;
}
?>