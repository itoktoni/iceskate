# Migrasi Cashi → iPaymu Redirect Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ganti gateway Cashi dengan iPaymu v2 Redirect, logika inline di controller (pendekatan A), Cashi + fallback Xendit dihapus total.

**Architecture:** Perubahan 1:1 mengikuti pola Cashi yang ada — `PublicController@involke()` menjadi client `POST {IPAYMU_BASE_URL}/api/v2/payment` (header `va/signature/timestamp`), `webhook()` menjadi validator `X-Signature` iPaymu, `markPaid()` cocokkan `reference_id → payment_id` (fallback `sid → payment_code`). Tanpa file baru kecuali test; tanpa migrasi DB (`payment_code` = SessionID, `payment_url` = checkout Url, `payment_method` = channel).

**Tech Stack:** Laravel (Http facade, Log), PHP 8.1+, PHPUnit 9 (tests/Unit pola `PHPUnit\Framework\TestCase` plain seperti `MenuTreeBuilderTest`; tests/Feature pola `Tests\TestCase` booting app seperti `Feature\ExampleTest`).

## Global Constraints

- Hapus total semua referensi Cashi (`CASHI_*`, `cashi`, `Cashi`) dan fallback Xendit (IP-whitelist) dari kode aplikasi.
- Jangan log `IPAYMU_API_KEY` atau secret dalam bentuk apa pun.
- Webhook untuk callback valid SELALU balas HTTP 200 (mencegah retry ganda iPaymu); invalid → 400/401 tanpa update DB.
- `markPaid()` idempoten: payment yang sudah `payment_paid=1` → return true tanpa update.
- Urutan pembuatan signature JANGAN diubah: `sha256(json) → "POST:VA:bodyHash:APIKEY" → hmac_sha256(..., APIKEY)`.
- Satu task = satu commit; jalankan test yang relevan sebelum commit.

---

## File map

- Modify: `.env.example` (tukar 4 baris `CASHI_*` → 3 baris `IPAYMU_*`).
- Modify: `routes/api.php:46-47` (hapus 2 route webhook lama → 1 route `webhook.ipaymu`).
- Modify: `app/Http/Controllers/PublicController.php` — `involke()` (~line 432-482), `webhook()` (~line 484-563), `markPaid()` (~line 565-597), `checkStatus()` (~line 365-399). Tambah 2 private method murni: `ipaymuSignature()` dan `isValidIpaymuCallback()`.
- Modify: `app/Http/Controllers/PaymentController.php` — `getCheck()` (~line 289-327), label line 143 `(Cashi)` → `(iPaymu)`, hapus import `Http` yang tidak terpakai.
- Create: `tests/Unit/IpaymuSignatureTest.php`, `tests/Unit/IpaymuCallbackTest.php`, `tests/Unit/NoCashiRemnantTest.php`, `tests/Feature/IpaymuRouteTest.php`.
- Manual (tidak di-commit): tambah `IPAYMU_VA`, `IPAYMU_API_KEY`, `IPAYMU_BASE_URL` ke `.env` lokal lalu `php artisan config:clear`.

---

### Task 1: Tukar env CASHI → IPAYMU

**Files:**
- Modify: `.env.example:86-89`
- Test: `tests/Unit/NoCashiRemnantTest.php` (baru — dipakai lagi di Task 6)

**Interfaces:**
- Consumes: tidak ada.
- Produces: nama env `IPAYMU_VA`, `IPAYMU_API_KEY`, `IPAYMU_BASE_URL` yang dipakai Task 3-4 via `env()`.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class NoCashiRemnantTest extends TestCase
{
    public function test_env_example_uses_ipaymu_keys(): void
    {
        $content = file_get_contents(__DIR__ . '/../../.env.example');

        $this->assertStringContainsString('IPAYMU_VA=', $content);
        $this->assertStringContainsString('IPAYMU_API_KEY=', $content);
        $this->assertStringContainsString('IPAYMU_BASE_URL=https://sandbox.ipaymu.com', $content);
        $this->assertStringNotContainsString('CASHI_', $content);
    }

    public function test_controllers_have_no_cashi_or_xendit_references(): void
    {
        foreach ([
            __DIR__ . '/../../app/Http/Controllers/PublicController.php',
            __DIR__ . '/../../app/Http/Controllers/PaymentController.php',
            __DIR__ . '/../../routes/api.php',
        ] as $file) {
            $content = file_get_contents($file);
            $this->assertStringNotContainsStringIgnoringCase('cashi', $content, "Cashi remnant in $file");
            $this->assertStringNotContainsStringIgnoringCase('xendit', $content, "Xendit remnant in $file");
        }
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor\bin\phpunit --filter NoCashiRemnantTest`
Expected: FAIL (`IPAYMU_VA=` not found; Cashi remnants found).

- [ ] **Step 3: Edit `.env.example` — ganti blok baris 86-89**

```env
IPAYMU_VA=
IPAYMU_API_KEY=
IPAYMU_BASE_URL=https://sandbox.ipaymu.com
```

Hapus 4 baris lama (`CASHI_BASE_URL`, `CASHI_KEY`, `CASHI_WEBHOOK`, `CASHI_CHANNEL`). Langkah ini SAJA yang dikerjakan di Task 1 (controller + route masih Cashi → test method kedua tetap merah, itu ekspektasi dan akan hijau di Task 6).

- [ ] **Step 4: Run test, catat status**

Run: `vendor\bin\phpunit --filter NoCashiRemnantTest::test_env_example_uses_ipaymu_keys`
Expected: PASS untuk method env; method controller-remnant masih FAIL (diterima sampai Task 6).

- [ ] **Step 5: Commit**

```bash
git add .env.example tests/Unit/NoCashiRemnantTest.php
git commit -m "chore: swap CASHI env keys to IPAYMU in .env.example"
```

---

### Task 2: Route webhook ipaymu

**Files:**
- Modify: `routes/api.php:46-47`
- Test: `tests/Feature/IpaymuRouteTest.php` (baru)

**Interfaces:**
- Consumes: tidak ada.
- Produces: route name `webhook.ipaymu` (`POST /api/webhook/ipaymu`) yang dipakai Task 3 sebagai `notifyUrl` dan Task 4 sebagai endpoint callback.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class IpaymuRouteTest extends TestCase
{
    public function test_ipaymu_webhook_route_registered(): void
    {
        $this->assertTrue(Route::has('webhook.ipaymu'));
        $this->assertFalse(Route::has('webhook.cashi'));

        $route = collect(Route::getRoutes())->firstWhere('action.as', 'webhook.ipaymu');
        $this->assertContains('POST', $route->methods);
        $this->assertSame('api/webhook/ipaymu', $route->uri);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor\bin\phpunit --filter IpaymuRouteTest`
Expected: FAIL (`webhook.ipaymu` belum ada; jika app gagal boot karena `.env` lokal bermasalah, fallback verifikasi manual `php artisan route:list --name=webhook` harus TIDAK menampilkan `webhook.ipaymu`).

- [ ] **Step 3: Edit `routes/api.php` — ganti 2 baris route**

```php
Route::post('/webhook/ipaymu', [PublicController::class, 'webhook'])->name('webhook.ipaymu');
```

Hapus baris `webhook` (xendit) dan `webhook.cashi`. `PublicController` sudah di-import di file itu (baris 9) — jangan tambah import.

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor\bin\phpunit --filter IpaymuRouteTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add routes/api.php tests/Feature/IpaymuRouteTest.php
git commit -m "feat: register POST /api/webhook/ipaymu, drop cashi+xendit webhooks"
```

---

### Task 3: `involke()` → iPaymu create redirect

**Files:**
- Modify: `app/Http/Controllers/PublicController.php` (`involke()` ~line 432-482, tambah private `ipaymuSignature()` di dekatnya)
- Test: `tests/Unit/IpaymuSignatureTest.php` (baru; via reflection, tanpa DB tanpa boot Laravel)

**Interfaces:**
- Consumes: env `IPAYMU_VA`, `IPAYMU_API_KEY`, `IPAYMU_BASE_URL` (Task 1); route `webhook.ipaymu` (Task 2) untuk `notifyUrl`.
- Produces: `PublicController::ipaymuSignature(array $body, string $method = 'POST'): array{json: string, signature: string, timestamp: string}` (private; Task 4 memakai pola signature yang sama hanya untuk dokumentasi, tidak memanggil method ini).

- [ ] **Step 1: Write the failing test** (vektor tetap yang dihitung dari algoritma resmi; va `1179000899`, key `TESTKEY123`)

```php
<?php

namespace Tests\Unit;

use App\Http\Controllers\PublicController;
use PHPUnit\Framework\TestCase;

class IpaymuSignatureTest extends TestCase
{
    private function callSignature(array $body): array
    {
        $method = new \ReflectionMethod(PublicController::class, 'ipaymuSignature');
        $method->setAccessible(true);

        return $method->invoke(null, $body);
    }

    public function test_signature_matches_official_vector(): void
    {
        $body = [
            'product' => ['T-Shirt'],
            'qty' => ['1'],
            'price' => ['100000'],
            'description' => ['Blue Size L'],
            'returnUrl' => 'https://example.com/thank-you',
            'notifyUrl' => 'https://example.com/callback',
            'cancelUrl' => 'https://example.com/cancel',
            'referenceId' => 'INV-002',
        ];

        putenv('IPAYMU_VA=1179000899');
        $_ENV['IPAYMU_VA'] = '1179000899';
        putenv('IPAYMU_API_KEY=TESTKEY123');
        $_ENV['IPAYMU_API_KEY'] = 'TESTKEY123';

        $result = $this->callSignature($body);

        $this->assertSame(
            '{"product":["T-Shirt"],"qty":["1"],"price":["100000"],"description":["Blue Size L"],'
            . '"returnUrl":"https://example.com/thank-you","notifyUrl":"https://example.com/callback",'
            . '"cancelUrl":"https://example.com/cancel","referenceId":"INV-002"}',
            $result['json']
        );
        $this->assertSame('010838ef8c75da854f055c9b9e2226a8e16e881533ddf973b7da44e8db13a517', $result['signature']);
        $this->assertMatchesRegularExpression('/^\d{14}$/', $result['timestamp']);
    }

    public function test_signature_changes_when_body_changes(): void
    {
        putenv('IPAYMU_VA=1179000899');
        $_ENV['IPAYMU_VA'] = '1179000899';
        putenv('IPAYMU_API_KEY=TESTKEY123');
        $_ENV['IPAYMU_API_KEY'] = 'TESTKEY123';

        $a = $this->callSignature(['referenceId' => 'A']);
        $b = $this->callSignature(['referenceId' => 'B']);

        $this->assertNotSame($a['signature'], $b['signature']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor\bin\phpunit --filter IpaymuSignatureTest`
Expected: FAIL (`ipaymuSignature()` belum ada → ReflectionException).

- [ ] **Step 3: Implementasi — tambah private helper + tulis ulang `involke()`**

Helper (letakkan tepat di atas `involke()`):

```php
private static function ipaymuSignature(array $body): array
{
    $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);
    $bodyHash = strtolower(hash('sha256', $jsonBody));
    $va = env('IPAYMU_VA', '');
    $apiKey = env('IPAYMU_API_KEY', '');
    $stringToSign = 'POST:' . $va . ':' . $bodyHash . ':' . $apiKey;

    return [
        'json' => $jsonBody,
        'signature' => hash_hmac('sha256', $stringToSign, $apiKey),
        'timestamp' => date('YmdHis'),
    ];
}
```

`involke()` baru (ganti seluruh body method lama; signature method tetap `private function involke($payment, $code, $total, $description = null)`):

```php
private function involke($payment, $code, $total, $description = null)
{
    $url = '';
    $amount = (int) $total;

    if ($amount <= 0) {
        Log::warning('iPaymu: invalid amount', ['order_id' => $code, 'amount' => $total]);
        return $url;
    }

    $baseUrl = rtrim(env('IPAYMU_BASE_URL', 'https://sandbox.ipaymu.com'), '/');
    $va = env('IPAYMU_VA', '');

    if (empty($va) || empty(env('IPAYMU_API_KEY', ''))) {
        Log::error('iPaymu: IPAYMU_VA / IPAYMU_API_KEY missing in .env');
        return $url;
    }

    $user = auth()->user();
    $label = $description ?: ('Tagihan ' . $code);
    $body = [
        'product' => [$label],
        'qty' => ['1'],
        'price' => [$amount],
        'description' => [$label],
        'returnUrl' => route('payment'),
        'notifyUrl' => route('webhook.ipaymu'),
        'cancelUrl' => route('payment'),
        'referenceId' => $code,
    ];
    if ($user) {
        $body['buyerName'] = $user->name ?? '';
        $body['buyerEmail'] = $user->email ?? '';
        $body['buyerPhone'] = $user->phone ?? '';
    }

    try {
        $signed = self::ipaymuSignature($body);
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'va' => $va,
            'signature' => $signed['signature'],
            'timestamp' => $signed['timestamp'],
        ])->timeout(30)->withBody($signed['json'], 'application/json')
            ->post($baseUrl . '/api/v2/payment');

        $data = $response->json();

        if (! $response->successful() || ($data['Status'] ?? null) !== 200 || empty($data['Data']['Url'])) {
            Log::error('iPaymu create-redirect failed', [
                'order_id' => $code,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return $url;
        }

        $url = $data['Data']['Url'];
        $payment->payment_code = $data['Data']['SessionID'] ?? $code;
        $payment->payment_url = $url;
        $payment->save();
    } catch (\Throwable $e) {
        Log::error('iPaymu create-redirect exception: ' . $e->getMessage(), ['order_id' => $code]);
    }

    return $url;
}
```

Catatan: `Http` dan `Log` sudah di-import di `PublicController` (baris 18-19) — jangan tambah import. Pesan/log lama ber kata "Cashi" di method ini ikut terganti oleh kode di atas. Blok `if ($amount < 2000)` khas Cashi DIHAPUS (diganti `$amount <= 0`; batas iPaymu mengikuti respons API).

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor\bin\phpunit --filter "IpaymuSignatureTest|NoCashiRemnantTest::test_env_example_uses_ipaymu_keys"`
Expected: PASS. Lalu `php -l app\Http\Controllers\PublicController.php` → `No syntax errors detected`.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/PublicController.php tests/Unit/IpaymuSignatureTest.php
git commit -m "feat: create iPaymu v2 redirect payment in involke"
```

---

### Task 4: `webhook()` → validasi iPaymu + `markPaid()` via reference

**Files:**
- Modify: `app/Http/Controllers/PublicController.php` (`webhook()` ~line 484-563, `markPaid()` ~line 565-597, tambah private `isValidIpaymuCallback()`)
- Test: `tests/Unit/IpaymuCallbackTest.php` (baru; via reflection, tanpa DB)

**Interfaces:**
- Consumes: env `IPAYMU_VA` sebagai secret validasi (Task 1); `payment_code`/`payment_id`/`payment_method` semantics (tak berubah).
- Produces: `PublicController::isValidIpaymuCallback(array $data, string $receivedSignature, string $secret): bool` (private); `webhook()` yang hanya paham iPaymu; `markPaid(string $paymentId, ?string $method)` yang cocokkan `payment_id` dulu lalu `payment_code == sid`.

- [ ] **Step 1: Write the failing test** (vektor tetap; secret = VA `1179000899`, ekspektasi signature `7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2`)

```php
<?php

namespace Tests\Unit;

use App\Http\Controllers\PublicController;
use PHPUnit\Framework\TestCase;

class IpaymuCallbackTest extends TestCase
{
    private function callValidate(array $data, string $signature, string $secret): bool
    {
        $method = new \ReflectionMethod(PublicController::class, 'isValidIpaymuCallback');
        $method->setAccessible(true);

        return $method->invoke(null, $data, $signature, $secret);
    }

    private function payload(): array
    {
        return [
            'amount' => '100000',
            'reference_id' => 'ORDER123456',
            'status' => 'berhasil',
            'status_code' => '1',
            'trx_id' => '12345678',
            'paid_off' => '98500',
            'transaction_status_code' => '1',
            'is_escrow' => '0',
        ];
    }

    public function test_valid_form_encoded_payload_passes(): void
    {
        $this->assertTrue($this->callValidate(
            $this->payload(),
            '7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2',
            '1179000899'
        ));
    }

    public function test_tampered_amount_fails(): void
    {
        $payload = $this->payload();
        $payload['amount'] = '99999';

        $this->assertFalse($this->callValidate(
            $payload,
            '7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2',
            '1179000899'
        ));
    }

    public function test_wrong_secret_fails(): void
    {
        $this->assertFalse($this->callValidate(
            $this->payload(),
            '7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2',
            'WRONGVA'
        ));
    }

    public function test_missing_additional_info_defaults_to_empty_array(): void
    {
        $payload = $this->payload();
        $payload['additional_info'] = '[]';

        $this->assertTrue($this->callValidate(
            $payload,
            '7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2',
            '1179000899'
        ));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor\bin\phpunit --filter IpaymuCallbackTest`
Expected: FAIL (`isValidIpaymuCallback()` belum ada → ReflectionException).

- [ ] **Step 3: Implementasi — tambah validator + tulis ulang `webhook()` dan `markPaid()`**

Validator (letakkan tepat di atas `webhook()`):

```php
private static function isValidIpaymuCallback(array $data, string $receivedSignature, string $secret): bool
{
    unset($data['signature']);

    $normalized = [];
    foreach ($data as $key => $value) {
        if ($key === 'is_escrow') {
            $normalized[$key] = ($value === true || $value === '1' || $value === 1 || $value === 'true');
        } elseif (in_array($key, ['trx_id', 'status_code', 'transaction_status_code', 'paid_off'], true)) {
            $normalized[$key] = (int) $value;
        } elseif ($key === 'additional_info') {
            $normalized[$key] = ($value === '[]' || $value === '' || $value === null) ? [] : $value;
        } else {
            $normalized[$key] = (string) $value;
        }
    }
    if (! array_key_exists('additional_info', $normalized)) {
        $normalized['additional_info'] = [];
    }

    ksort($normalized, SORT_STRING);
    $json = str_replace('/', '\\/', json_encode($normalized, JSON_UNESCAPED_SLASHES));
    $calculated = hash_hmac('sha256', $json, $secret);

    return hash_equals($calculated, $receivedSignature);
}
```

`webhook()` baru (ganti seluruh body method lama termasuk fallback Xendit):

```php
public function webhook(Request $request)
{
    $receivedSignature = $request->header('X-Signature', '');
    $secret = env('IPAYMU_VA', '');

    if (empty($receivedSignature) || empty($secret)) {
        Log::warning('iPaymu webhook: missing signature or IPAYMU_VA', ['ip' => $request->ip()]);
        return response()->json(['message' => 'Invalid signature'], 401);
    }

    $data = $request->all();

    if (! self::isValidIpaymuCallback($data, $receivedSignature, $secret)) {
        Log::warning('iPaymu webhook: invalid signature', ['ip' => $request->ip()]);
        return response()->json(['message' => 'Invalid signature'], 401);
    }

    Log::info('iPaymu webhook payload', ['body' => $data]);

    $status = strtolower((string) ($data['status'] ?? ''));
    $statusCode = (int) ($data['status_code'] ?? 0);

    if ($status === 'berhasil' && $statusCode === 1) {
        $reference = trim((string) ($data['reference_id'] ?? $data['referenceId'] ?? ''));
        $sid = trim((string) ($data['sid'] ?? ''));
        $method = ($data['via'] ?? null) && ($data['channel'] ?? null)
            ? ($data['via'] . '/' . $data['channel'])
            : ($data['via'] ?? $data['channel'] ?? null);

        $ok = $this->markPaid($reference !== '' ? $reference : $sid, $method, $sid);

        return response()->json(['message' => $ok ? 'OK' : 'Order not found']);
    }

    Log::info('iPaymu webhook: non-success status ignored', [
        'status' => $data['status'] ?? null,
        'status_code' => $data['status_code'] ?? null,
    ]);

    return response()->json(['message' => 'OK']);
}
```

`markPaid()` baru (ganti seluruh body; signature jadi `($paymentId, $method = null, $sid = null)`):

```php
private function markPaid($paymentId, $method = null, $sid = null)
{
    $paymentId = trim((string) $paymentId);
    $sid = trim((string) ($sid ?? ''));

    $payment = $paymentId !== ''
        ? Payment::find($paymentId)
        : null;

    if (empty($payment) && $sid !== '') {
        $payment = Payment::where('payment_code', $sid)->first();
    }

    if (empty($payment)) {
        Log::warning('iPaymu markPaid: payment not found', ['order_id' => $paymentId, 'sid' => $sid]);
        return false;
    }

    if ($payment->payment_paid) {
        Log::info('iPaymu markPaid: already paid', ['order_id' => $payment->payment_id]);
        return true;
    }

    $payment->update([
        'payment_paid' => 1,
        'payment_done' => date('Y-m-d H:i:s'),
        'payment_method' => $method,
    ]);

    Log::info('iPaymu markPaid: payment_paid=1', ['order_id' => $payment->payment_id]);

    return true;
}
```

Semua string log "Cashi" menjadi "iPaymu". Seluruh blok `$allow` IP Xendit DIHAPUS.

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor\bin\phpunit --filter "IpaymuCallbackTest|IpaymuSignatureTest"`
Expected: PASS. Lalu `php -l app\Http\Controllers\PublicController.php`.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/PublicController.php tests/Unit/IpaymuCallbackTest.php
git commit -m "feat: validate iPaymu webhook signature, settle by reference id"
```

---

### Task 5: Sederhanakan cek status + label admin

**Files:**
- Modify: `app/Http/Controllers/PublicController.php` (`checkStatus()` ~line 365-399)
- Modify: `app/Http/Controllers/PaymentController.php` (`getCheck()` ~line 289-327, label line 143, hapus import `Http` line 16)
- Test: tidak ada test baru; verifikasi via `NoCashiRemnantTest` + `php -l` + langkah manual Task 6

**Interfaces:**
- Consumes: `payment_code`/`payment_url` semantics dari Task 3.
- Produces: tidak ada (ujung alur).

- [ ] **Step 1: Tulis ulang `checkStatus()` di `PublicController`**

```php
public function checkStatus($id)
{
    if (! auth()->check()) {
        return redirect('/');
    }

    $payment = Payment::where('payment_id', $id)
        ->where('payment_id_user', auth()->user()->id)
        ->firstOrFail();

    if ($payment->payment_paid) {
        return redirect()->route('payment')->with('success', 'Pembayaran sudah lunas.');
    }

    if (! empty($payment->payment_url)) {
        return redirect()->route('payment')->with(
            'error',
            'Pembayaran belum diterima. Silakan lanjutkan pembayaran: ' . $payment->payment_url
        );
    }

    return redirect()->route('payment')->with('error', 'Pembayaran belum diterima.');
}
```

Alasan (dari spek 4.4): `POST /api/v2/transaction` butuh `transactionId` iPaymu yang baru ada setelah callback, jadi cek server-to-server tidak bisa menggantikan Cashi `check-status`; satu-satunya sumber kebenaran pra-bayar adalah webhook + halaman bayar iPaymu.

- [ ] **Step 2: Tulis ulang `getCheck()` di `PaymentController` + label + import**

```php
public function getCheck($code)
{
    $payment = Payment::where('payment_id', strval($code))->first();

    if (empty($payment)) {
        return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
    }

    if ($payment->payment_paid) {
        return redirect()->back()->with('success', 'Pembayaran sudah lunas.');
    }

    if (! empty($payment->payment_url)) {
        return redirect()->back()->with(
            'error',
            'Belum dibayar. Link pembayaran: ' . $payment->payment_url
        );
    }

    return redirect()->back()->with('error', 'Belum dibayar.');
}
```

Label line 143: `' (Cashi)'` → `' (iPaymu)'`. Hapus baris 16 `use Illuminate\Support\Facades\Http;` (tidak lagi dipakai di file ini).

- [ ] **Step 3: Run verifikasi**

Run: `php -l app\Http\Controllers\PublicController.php; if ($?) { php -l app\Http\Controllers\PaymentController.php }; if ($?) { vendor\bin\phpunit --filter NoCashiRemnantTest }`
Expected: dua `No syntax errors detected`, test remnant method controller + route... (route sudah hijau sejak Task 2; controller hijau setelah task ini).

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/PublicController.php app/Http/Controllers/PaymentController.php
git commit -m "feat: simplify payment status check for iPaymu, drop Cashi check-status"
```

---

### Task 6: Sapu bersih + verifikasi akhir + uji sandbox manual

**Files:** tidak ada perubahan kode kecuali temuan sapu bersih; test: suite yang sudah ada.

**Interfaces:**
- Consumes: seluruh Task 1-5.
- Produces: migrasi selesai + terverifikasi.

- [ ] **Step 1: Sapu bersih repo-wide (bukti nol remnant)**

Run: `grep -rni "cashi\|xendit" app routes config database resources --include="*.php" | grep -v vendor`
Expected: TIDAK ada output. (Hit di `docs/superpowers/specs`, `.env` lokal, atau log lama boleh diabaikan — catat saja.)

- [ ] **Step 2: Full unit suite**

Run: `vendor\bin\phpunit --testsuite Unit`
Expected: semua PASS termasuk `IpaymuSignatureTest`, `IpaymuCallbackTest`, `NoCashiRemnantTest`, dan test lama (`MenuTreeBuilderTest`).

- [ ] **Step 3: Konfigurasi lokal + smoke boot**

Run: tambah `IPAYMU_VA`, `IPAYMU_API_KEY`, `IPAYMU_BASE_URL=https://sandbox.ipaymu.com` ke `.env` (JANGAN commit), lalu `php artisan config:clear; if ($?) { php artisan route:list --name=webhook }`
Expected: `webhook.ipaymu` terdaftar; tidak ada error boot.

- [ ] **Step 4: Uji end-to-end sandbox (checklist spek 4.7, manual di browser)**

1. Buat tagihan → redirect ke `sandbox.ipaymu.com/payment/<SessionID>`; `payment_code`/`payment_url` tersimpan.
2. Bayar di sandbox → `storage/logs/laravel.log` ada `iPaymu webhook payload` → `payment_paid=1`.
3. Kirim ulang callback sama → 200, tidak double-update (cek log `already paid`).
4. Callback signature salah (ubah 1 karakter `X-Signature` via curl/postman ke `/api/webhook/ipaymu`) → 401, DB tidak berubah.
5. Tombol cek saat pending → pesan + link bayar ulang; setelah paid → "lunas".

- [ ] **Step 5: Commit jika ada perbaikan dari sapu bersih, lalu commit plan**

```bash
git add -A
git commit -m "chore: finalize iPaymu migration, remove remnants"
git add docs/superpowers/plans/2026-10-14-ipaymu-migration.md
git commit -m "docs: implementation plan migrasi Cashi ke iPaymu (pendekatan A)"
```

---

## Self-review

1. **Spec coverage:** 4.1 → Task 1+2; 4.2 → Task 3 (field `product/qty/price/description/referenceId/buyer*/return-notify-cancelUrl`, simpan SessionID/Url); 4.3 → Task 4 (normalisasi tipe, sort, escape `/`, HMAC-VA, `hash_equals`, reference→payment_id + fallback sid→payment_code, idempoten, 200/401); 4.4 → Task 5 (keterbatasan `transactionId` didokumentasikan di kode via komentar alasan + perilaku fallback link); 4.5-4.6 → tercermin di Task 3-4 (redirect flow, pola log tanpa secret); 4.7 → Task 6 Step 4. Non-goals dihormati: tanpa service class, tanpa migrasi, tanpa channel picker.
2. **Placeholder scan:** semua step berisi kode/ekspektasi konkret; vektor signature riil (`010838…`, `7126c1…`) bukan TBD; tidak ada "handle edge cases" generik — edge (`additional_info` hilang, secret salah, body diubah, amount invalid, kredensial kosong) masing-masing punya test/langkah.
3. **Type consistency:** `ipaymuSignature(array): array{json,signature,timestamp}` dipanggil hanya di Task 3; `isValidIpaymuCallback(array,string,string): bool` hanya di Task 4; `markPaid($paymentId,$method=null,$sid=null)` hanya dipanggil dari `webhook()` Task 4; nama env/route identik di semua task.
