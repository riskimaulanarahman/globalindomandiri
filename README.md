## Panduan Penggunaan Sistem

Dokumen ini menjelaskan cara menggunakan sistem Operasional & Billing R&R Globalindo Mandiri dari sisi pengguna (tanpa detail teknis).

### 1. Masuk & Navigasi

-   Masuk ke aplikasi dengan akun Anda.
-   Gunakan menu di sisi kiri untuk berpindah modul: Customers, Locations, Rates, Quotations, Shipments, Invoices, Payments, Reports, Users.
-   Di setiap halaman tersedia tombol “Bantuan” yang berisi instruksi, deskripsi kolom, dan contoh pengisian.

### 2. Data Master

#### Customers

-   Tambah Customer: isi Code (unik), Name, PIC, Phone, Email (unik), Payment Term (hari), dan Notes bila perlu.
-   Edit/Hapus dari daftar Customers. Hapus membutuhkan konfirmasi.

#### Locations

-   Tambah Location: isi City dan Country (Province opsional).
-   Hindari duplikasi kombinasi City + Province + Country.

#### Rates

-   Tambah Rate: pilih Origin, Destination, isi Service Type (mis. REG/EXP), Price (tarif per kg), Lead Time, dan aktifkan “Active”.
-   Filter di daftar berdasarkan origin/destination/service/active.
-   Import/Export CSV (format kolom: origin_id,destination_id,service_type,price,lead_time,is_active).

### 3. Penawaran (Quotations)

-   Buat Quotation: atur Date, Valid Until (masa berlaku), Customer, Route (Origin → Destination), Service, Lead Time, Currency/Tax/Discount, Terms/Notes.
-   Tambah Item:
    -   Manual: isi Description, Qty, UOM, Unit Price.
    -   Add from Rate: pilih rate sesuai rute untuk mengisi harga otomatis.
-   Status:
    -   Draft → Sent → Accepted/Rejected/Expired → Converted (menjadi Shipment).
-   Print: buka halaman print dan gunakan print browser untuk simpan/cetak.

### 4. Pengiriman (Shipments)

-   Buat Shipment:
    -   Resi otomatis jika dikosongkan (format: RGM-{BRANCH}-{000001}).
    -   Pilih Customer, Origin, Destination, Service.
    -   Isi Weight Actual & Volume; sistem menggunakan nilai terbesar sebagai chargeable weight.
    -   Pilih Rate (opsional) agar Base Fare dihitung otomatis = rate.price × chargeable.
    -   Lengkapi data Sender & Receiver (name, address, PIC, phone) serta biaya lain (Packing, Insurance, Discount, PPN, PPh23, Other).
-   Status: Draft → Booked → In Transit → Delivered/Cancelled.
-   Print Resi/AWB: tombol “Print Resi” menampilkan halaman A4 landscape siap cetak.
-   Catatan: Shipment yang sudah masuk invoice tidak dapat dihapus.

### 5. Penagihan (Invoices)

-   Buat Invoice: atur Date, Due Date/Top, Customer, Terms & Remarks.
-   Tambah Item:
    -   Manual Line: Description, Qty, Amount.
    -   Dari Shipment: pilih resi untuk mengisi item otomatis.
-   Status mengikuti pembayaran: Draft → Sent → Partially Paid → Paid/Overdue.
-   Print: gunakan halaman print untuk arsip atau cetak.

### 6. Pembayaran (Payments)

-   Tambah Payment: pilih Invoice, isi Paid Amount (≤ outstanding), Paid Date, Method, dan Reference (opsional).
-   Mengubah/Menghapus Payment akan memperbarui status invoice dan outstanding secara otomatis.

### 7. Laporan (Reports)

-   Ringkasan metrik sederhana (misal status pengiriman, aging). Gunakan sebagai indikator cepat operasional dan AR.

### 8. Manajemen Pengguna (Users)

-   Tambah/Edit pengguna. Password konfirmasi wajib saat membuat user; kosongkan saat edit jika tidak berubah.
-   Tidak dapat menghapus akun diri sendiri.

### 9. Pencarian, Filter & Bantuan

-   Pencarian/Filter tersedia di daftar setiap modul untuk mempercepat temuan data.
-   Semua pilihan (select) mendukung pencarian cepat (ketik untuk mencari).
-   Tombol Bantuan berisi instruksi, deskripsi kolom, dan contoh pengisian—baca sebelum mengisi data baru.

### 10. Penomoran Otomatis

-   Resi: dibuat otomatis saat Shipment disimpan tanpa nomor resi.
-   Quotation: nomor dibuat otomatis saat pertama kali disimpan.

### 11. Tips Cetak

-   Untuk hasil terbaik, gunakan skala 100% dan margin normal di dialog Print browser.
-   AWB/Resi dirancang dalam orientasi A4 landscape.

### 12. Kebijakan Penghapusan

-   Beberapa data terlindungi dari penghapusan ketika sudah memiliki relasi penting (contoh: Shipment yang telah ditagihkan).
-   Gunakan Edit alih‑alih Hapus bila data sudah terpakai dalam proses lain.

---

Jika ada kebutuhan alur tambahan (misal approval internal, lampiran dokumen, atau template cetak khusus), silakan hubungi admin sistem untuk diaktifkan.
