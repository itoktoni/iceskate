# Migrasi Pembayaran Cashi → iPaymu v2 Redirect (Pendekatan A)

Tanggal: 2026-10-14
Status: draft — menunggu review user
Keputusan: pendekatan A (Redirect 1:1 pengganti Cashi, logika inline di controller seperti pola Cashi sekarang, tanpa service class baru).

## 1. Tujuan

Mengganti gateway pembayaran Cashi dengan iPaymu v2 Redirect payment.
Kode Cashi dihapus total. Fallback legacy Xendit (IP-whitelist di `PublicController@webhook`) ikut dihapus.

## 2. Konteks kode saat ini

- `app/Http/Controllers/PublicController.php`: `involke()` (POST `{CASHI_BASE_URL}/api/create-order`,
  header `x-api-key`, body `{amount, order_id, kode_channel}` → simpan `payment_code = orderId`,
  `payment_url = checkout_url`, redirect ke `checkout_url`), `checkStatus()` (GET `/api/check-status/{orderId}`,
  lunas jika `status == SETTLED`), `webhook()` (validasi `X-Gateway-Signature = HMAC-SHA256(payload, CASHI_WEBHOOK)`,
  event `PAYMENT_SETTLED` → `markPaid()`), `markPaid()` (cocokkan `payment_code` dulu lalu `payment_id`, idempoten).
- `app/Http/Controllers/PaymentController.php`: `getCheck()` (cek admin, pola sama dengan `checkStatus`),
  label `payment_method` ber embel-embel `(Cashi)`.
- `routes/api.php`: `POST /api/webhook/xendit` dan `POST /api/webhook/cashi` → `PublicController@webhook`.
- `.env.example`: `CASHI_BASE_URL`, `CASHI_KEY`, `CASHI_WEBHOOK`, `CASHI_CHANNEL=QRIS_CUSTOM`.
- Kolom `payment` yang dipakai ulang (tanpa migrasi): `payment_code` (simpan SessionID iPaymu),
  `payment_url` (checkout Url), `payment_method` (channel, mis. `va/bca`).

## 3. Referensi iPaymu

- Postman collection user: `https://documenter.getpostman.com/view/40296808/2sB3WtseBT?version=latest`
- Docs resmi: `https://docs.ipaymu.com/en/docs` (signature, redirect-payment, callback, check-transaction).
- Kredensial: user punya VA + API Key untuk Sandbox maupun Production (mode via `.env`).

## 4. Desain perubahan

### 4.1 Config & env

- Hapus: `CASHI_BASE_URL`, `CASHI_KEY`, `CASHI_WEBHOOK`, `CASHI_CHANNEL` (dari `.env.example` + semua pemakaian kode).
- Tambah di `.env.example`:
  - `IPAYMU_VA=`
  - `IPAYMU_API_KEY=`
  - `IPAYMU_BASE_URL=https://sandbox.ipaymu.com` (produksi: `https://my.ipaymu.com`).
- Route: hapus `webhook` (xendit) dan `webhook.cashi`; tambah
  `Route::post('/webhook/ipaymu', [PublicController::class, 'webhook'])->name('webhook.ipaymu');`
- Syarat produksi iPaymu (di luar kode): server ber-IP statis dan domain terdaftar di dashboard iPaymu.

### 4.2 Create payment (`involke()` ditulis ulang, tetap inline di `PublicController`)

- Signature per request (jangan diubah urutannya):
  1. `$jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES)`
  2. `$bodyHash = strtolower(hash('sha256', $jsonBody))`
  3. `$stringToSign = 'POST:' . $va . ':' . $bodyHash . ':' . $apiKey`
  4. `$signature = hash_hmac('sha256', $stringToSign, $apiKey)`, header `timestamp = date('YmdHis')`.
- Header: `Accept: application/json`, `Content-Type: application/json`, `va`, `signature`, `timestamp`.
- Endpoint: `POST {IPAYMU_BASE_URL}/api/v2/payment`.
- Body:
  - `product = [keterangan iuran / "Tagihan <periode>"]`, `qty = ["1"]`, `price = [(int) amount]`,
    `description = [teks yang sama dengan product]`.
  - `referenceId = payment_id code` (unik per tagihan, dipakai callback untuk matching).
  - `buyerName/buyerEmail/buyerPhone` dari user login (jika ada).
  - `returnUrl = route('payment')` (absolut), `cancelUrl = route('payment')`,
    `notifyUrl = route('webhook.ipaymu')` (absolut, ikut `APP_URL`).
- Sukses (`Status == 200`): simpan `payment_code = Data.SessionID`, `payment_url = Data.Url`, redirect user ke `Url`.
- Gagal: log `status + body` seperti pola Cashi sekarang, kembalikan `''` agar controller menampilkan
  "Gagal membuat pembayaran, silakan coba lagi."
- Batas minimal amount ikut aturan iPaymu (batas Cashi 2.000 dihapus, diganti validasi respons iPaymu).

### 4.3 Webhook (`webhook()` ditulis ulang, hanya path iPaymu)

- Baca header `X-Signature`; kosong → 401 (tidak ada fallback Xendit lagi).
- Ambil body (dukung `application/json` dan `x-www-form-urlencoded`): `$request->all()`,
  buang field `signature` bila ada.
- Normalisasi tipe sebelum hash (wajib, kalau tidak hash selalu mismatch):
  `trx_id, status_code, transaction_status_code, paid_off` → int;
  `is_escrow` (`"0"/"1"/"true"`) → bool; `additional_info` hilang → `[]`, string `"[]"` → `[]`;
  sisanya string. `payment_no/va` tetap string (jaga leading zero).
- Sort key A-Z case-sensitive (ala `ksort`), `json_encode`, escape `/` → `\/`,
  `hash_hmac('sha256', $json, $va)` dengan secret = `IPAYMU_VA`, bandingkan via `hash_equals`.
- Invalid → 400/401 + log. Valid → log payload, cek `status == "berhasil" && (int) status_code == 1`
  → `markPaid(reference_id / referenceId, via/channel)`.
- `markPaid()`: cocokkan `payment_id` (reference) dulu, fallback `payment_code == sid`;
  idempoten (sudah paid → return true); update `payment_paid=1`, `payment_done=now`,
  `payment_method=via/channel`; selalu balas HTTP 200 untuk callback valid agar iPaymu berhenti retry.
  Status lain (`pending/expired`) → log + 200 tanpa update.

### 4.4 Cek status manual (`checkStatus()` user + `getCheck()` admin)

- iPaymu `POST /api/v2/transaction` butuh `transactionId` (trx_id iPaymu), yang baru ada setelah callback.
  Maka tombol cek disederhanakan:
  - Sudah `payment_paid` → "sudah lunas".
  - Belum → tampilkan status + link `payment_url` (buka ulang halaman bayar iPaymu, masih valid sampai expired).
  - Opsional bila `trx_id` sudah tersimpan: coba `POST /api/v2/transaction {transactionId}` dengan
    signature yang sama; `Status/Data.Status == 1` atau `PaidStatus == paid` → `markPaid()`.
- Label `(Cashi)` di `PaymentController` diganti `(iPaymu)`. Pesan error "Gagal cek ke Cashi" diganti.

### 4.5 Data flow

1. User buat tagihan → `invoice()` buat `Payment` → `involke()` POST iPaymu → redirect ke `Data.Url`.
2. User bayar di halaman iPaymu → iPaymu POST callback ke `notifyUrl` → validasi signature → `markPaid()`.
3. iPaymu redirect user ke `returnUrl` (`/payment`) → halaman lihat status lokal (lunas bila webhook sudah masuk).
4. Tombol "cek" hanya baca DB + tawarkan buka ulang `payment_url`.

### 4.6 Error handling & logging

- Pertahankan pola log Cashi: `Log::error` saat create gagal/exception + `order_id/reference`,
  `Log::warning` signature invalid / status diabaikan, `Log::info` payload callback valid.
- Jangan log `IPAYMU_API_KEY` atau full body berisi data pribadi melebihi kebutuhan debug.
- Webhook selalu kembalikan 200 untuk request valid (cegah retry ganda); idempotency via cek `payment_paid`.

### 4.7 Testing

1. Set `.env` sandbox (`IPAYMU_BASE_URL=https://sandbox.ipaymu.com`, VA + Key sandbox), `php artisan config:clear`.
2. Buat tagihan → pastikan redirect ke `sandbox.ipaymu.com/payment/<SessionID>`, `payment_code/payment_url` tersimpan.
3. Bayar di sandbox → callback masuk (cek `storage/logs/laravel.log` "webhook payload") → `payment_paid=1`.
4. Kirim ulang callback yang sama → tetap 200, tidak double-posting.
5. Callback dengan signature salah → 400/401, tidak update DB.
6. Tombol cek saat pending → tampil link bayar ulang; setelah paid → "lunas".
7. Ulangi poin 2–3 dengan kredensial produksi di lingkungan ber-IP statis + domain terdaftar.

## 5. Non-goals (tidak dikerjakan)

- Direct Payment / channel picker di web sendiri.
- Service class terpisah (itu pendekatan C, ditolak — user pilih A).
- Migrasi database baru.
- Perubahan alur tagihan (`generate:payment`, `send:payment`, `InvoiceService`) selain URL bayar yang kini dari iPaymu.
- Pendaftaran IP/domain ke dashboard iPaymu (tugas user di luar kode).

## 6. Self-review spek

- [x] Tanpa placeholder/TBD; semua env, route, field DB eksplisit.
- [x] Konsisten: reference = `payment_id`, SessionID → `payment_code`, tidak ada sisa referensi Cashi/Xendit.
- [x] Scope tunggal: satu gateway redirect, tanpa migrasi DB.
- [x] Ambigu "cek status" diselesaikan eksplisit di 4.4 (keterbatasan `transactionId` didokumentasikan).
