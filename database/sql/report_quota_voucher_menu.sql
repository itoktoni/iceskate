-- Menu report: Quota Voucher (Laporan Administrasi Pemakaian Voucher)
-- Jalankan di database server (mis. database production).

-- 1. Master menu
INSERT INTO system_menu
    (system_menu_code, system_menu_name, system_menu_url, system_menu_controller,
     system_menu_action, system_menu_type, system_menu_sort,
     system_menu_description, system_menu_enable, system_menu_can_delete)
SELECT 'report_quota_voucher', 'Quota Voucher', 'report_quota_voucher',
       'App\\Http\\Controllers\\ReportQuotaVoucherController',
       'report_quota_voucher.getCreate', 1, NULL, NULL, NULL, 1
WHERE NOT EXISTS (
    SELECT 1 FROM system_menu WHERE system_menu_code = 'report_quota_voucher'
);

-- 2. Relasi menu ke group Laporan
INSERT INTO system_group_connection_menu (system_group_code, system_menu_code)
SELECT 'laporan', 'report_quota_voucher'
WHERE NOT EXISTS (
    SELECT 1 FROM system_group_connection_menu
    WHERE system_group_code = 'laporan'
      AND system_menu_code = 'report_quota_voucher'
);

-- 3. Verifikasi
SELECT * FROM system_menu WHERE system_menu_code = 'report_quota_voucher';
SELECT * FROM system_group_connection_menu WHERE system_menu_code = 'report_quota_voucher';
