<?php
namespace App\Http\Controllers;

use App\Dao\Enums\Core\RoleType;
use App\Dao\Models\Core\User;
use App\Dao\Models\History;
use App\Dao\Models\Iuran;
use App\Dao\Models\Jadwal;
use App\Dao\Models\Payment;
use App\Dao\Models\Race;
use App\Dao\Models\Token;
use App\Models\Menu;
use App\Models\Page;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Plugins\Cms;
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PublicController extends Controller
{
    public function share($data)
    {
        $menu   = Menu::slug('top')->first();
        $jadwal = Jadwal::leftJoinRelationship('has_category')->get();

        $user = null;
        if (auth()->check()) {
            $user = User::with('has_category')->find(auth()->user()->id);
        }

        $performance = Race::select(['*', 'name'])
            ->leftJoinRelationship('has_jarak')
            ->leftJoinRelationship('has_user');

        if (auth()->check() && auth()->user()->role == RoleType::User) {
            $performance = $performance->where('race_user_id', auth()->user()->id);
        }

        $performance = $performance->get();

        $default = [
            'logo_url'            => Cms::logo_url(),
            'website_address'     => Cms::website_address(),
            'website_email'       => Cms::website_email(),
            'website_description' => Cms::website_description(),
            'website_phone'       => Cms::website_phone(),
            'performance'         => $performance,
            'menu'                => $menu,
            'jadwal'              => $jadwal,
            'user'                => $user,
        ];

        return array_merge($default, $data);
    }

    public function index()
    {
        $homepage = Page::slug('homepage')->first();
        $template = $homepage->acf->template;

        return view('public.homepage', $this->share([
            'template' => $template,
        ]));
    }

    public function performance()
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $page = Page::slug('performance')->first();
        $user = User::where('role', RoleType::User);

        if (auth()->user()->role == RoleType::User) {
            $user = $user->where('id', auth()->user()->id)->first();
        } else {
            $user = $user->get();
        }

        $template = $page->acf->template;

        return view('public.homepage', $this->share([
            'page'     => $page,
            'template' => $template,
            'user'     => $user,
        ]));
    }

    public function page($slug)
    {
        $page     = Page::slug($slug)->firstOrFail();
        $template = $page->acf->template;

        return view('public.homepage', $this->share([
            'page'     => $page,
            'template' => $template,
        ]));
    }

    public function userprofile()
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $page     = Page::slug('performance')->first();
        $template = $page->acf->template->first();

        return view('public.userprofile', $this->share([
            'page' => $page,
            'data' => $template,
        ]));
    }

    public function hadir($id)
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        try {
            $jadwal = Jadwal::findOrFail($id);
            $jadwal->has_absen()->attach(auth()->user()->id);
            return redirect()->back()->with('success', 'Kehadiran berhasil dicatat!');

        } catch (\Throwable $th) {

            if($th->getCode() == 23000)
            {
                return redirect()->back()->with('error', 'Kehadiran sudah dicatat sebelumnya!');
            }

            return redirect()->back()->with('error', 'Jadwal tidak ditemukan!');
        }

    }

    public function kehadiran()
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $page     = Page::slug('kehadiran')->first();
        $template = $page->acf->template;

        $jadwal = Jadwal::orderBy('jadwal_tanggal', 'desc')
            ->paginate(5);

        $kehadiran = Jadwal::whereHas('has_absen', function ($query) {
            $query->where('users.id', auth()->user()->id);
        })->get();

        $single = false;
        if (request()->has('id')) {
            $jadwal = Jadwal::find(request()->get('id'));

            if ($jadwal) {
                $single = $jadwal;
            }

            $user_id = auth()->user()->id;

            $token = Token::query()
                ->where('payment_id_user', $user_id)
                ->where('total', '>=', 1)
                ->whereYear('payment_tanggal', now()->format('Y'))
                ->whereMonth('payment_tanggal', now()->format('m'))
                ->first();

            $jumlah = Token::query()
                ->where('payment_id_user', $user_id)
                ->whereYear('payment_tanggal', now()->format('Y'))
                ->whereMonth('payment_tanggal', now()->format('m'))
                ->sum('total');

            $usage = Token::query()
                ->where('payment_id_user', $user_id)
                ->whereYear('payment_tanggal', now()->format('Y'))
                ->whereMonth('payment_tanggal', now()->format('m'))
                ->sum('used');

            $available = $jumlah;

            $total = $token->total ?? 0;

            $qr = null;
            $path = public_path() .'/qr/'.$user_id. '.png';

            $data = json_encode([
                'u' => $user_id,
                'j' => $jadwal->jadwal_id ?? null,
                'p' => $token->payment_id ?? null,
                't' => $token->total ?? 0
            ]);

            $encrypted = Crypt::encryptString($data);

            return view('public.detailkehadiran', $this->share([
                'page'     => $page,
                'template' => $template,
                'jadwal'   => $jadwal,
                'single'   => $single,
                'kehadiran'   => $kehadiran,
                'qr'   => $encrypted,
                'total'   => $available,
            ]));
        }

        return view('public.kehadiran', $this->share([
            'page'     => $page,
            'template' => $template,
            'jadwal'   => $jadwal,
            'kehadiran'   => $kehadiran,
            'single'   => $single,
        ]));
    }

    public function payment()
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $page     = Page::slug('payment')->first();
        $template = $page->acf->template;
        $iuran    = Iuran::where('iuran_tanggal', '>=', now()
                ->addMonth(-2)->format('Y-m-d'))
                ->orWhereIn('iuran_id', [1,2,3])
            ->orderBy('iuran_tanggal', 'asc')
            ->get()
        ;

        $day = (int) now()->format('d');

        // Sudah beli VOUCHER 5 (iuran_id 1) bulan ini?
        $boughtV5 = Payment::where('payment_id_user', auth()->user()->id)
            ->where('payment_paid', 1)
            ->where('payment_iuran', 1)
            ->whereYear('payment_tanggal', now()->format('Y'))
            ->whereMonth('payment_tanggal', now()->format('m'))
            ->exists();

        // Aturan voucher:
        // - tgl 1-10 : VOUCHER 5 (belum beli) / VOUCHER 1+ (sudah beli V5, boleh beli berulang)
        // - lewat tgl 10 : hanya VOUCHER 1
        $five = $boughtV5 && $day <= 10; // tampilkan VOUCHER 1+
        $late = $day > 10;               // tampilkan VOUCHER 1 saja

        return view('public.payment', $this->share([
            'page'     => $page,
            'template' => $template,
            'iuran'    => $iuran,
            'five'     => $five,
            'late'     => $late,
            'boughtV5' => $boughtV5,
        ]));
    }

    public function history()
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $page     = Page::slug('payment')->first();
        $template = $page->acf->template;
        $history = History::where('payment_id_user', auth()->user()->id)
        ->addSelect('*')
        ->get();

        return view('public.history', $this->share([
            'page'     => $page,
            'template' => $template,
            'history'   => $history,
        ]));
    }



    public function iuran()
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $total = request()->get('iuran') ? array_sum(request()->get('iuran')) : 0;

        if(request()->get('iuran') == null)
        {
            return redirect()->back()->with('error', 'Tidak ada iuran yang dipilih');
        }

        if($total < 2000)
        {
            return redirect()->back()->with('error', 'Minimal pembayaran Rp 2.000');
        }

        $code    = unic(10) . date('Ymd');
        $payment = Payment::create([
            'payment_id'      => $code,
            'payment_tanggal' => now()->format('Y-m-d'),
            'payment_id_user' => auth()->user()->id,
            'payment_value'   => $total,
        ]);

        foreach (request()->get('iuran') as $key => $value) {
            $payment->has_iuran()->attach($key, ['iuran_harga' => $value]);
        }

        $url = $this->involke($payment, $code, $total);

        if (empty($url)) {
            return redirect()->back()->with('error', 'Gagal membuat pembayaran, silakan coba lagi.');
        }

        return redirect()->to($url);
    }

    public function invoice($id)
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $iuran = Iuran::find($id, ['iuran_harga', 'iuran_voucher']);
        $harga = $iuran->iuran_harga;
        $token = $iuran->iuran_voucher;

        $code    = unic(10) . date('Ymd');
        $payment = Payment::create([
            'payment_id'      => $code,
            'payment_tanggal' => now()->format('Y-m-d'),
            'payment_id_user' => auth()->user()->id,
            'payment_value'   => $harga,
            'payment_iuran'   => $id,
            'payment_voucher'   => $token,
        ]);

        $url = url()->full();

        try {
            $url = $this->involke($payment, $code, $harga, $iuran->iuran_keterangan);
        } catch (\Throwable $th) {
            Log::error('Cashi involke exception: ' . $th->getMessage(), ['order_id' => $code]);
        }

        InvoiceService::generate($payment->payment_id);

        if (empty($url) || $url === url()->full()) {
            return redirect()->route('payment')->with('error', 'Gagal membuat pembayaran, silakan coba lagi.');
        }

        return redirect()->to($url);
    }

    public function checkStatus($id)    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $payment = Payment::where('payment_id', $id)
            ->where('payment_id_user', auth()->user()->id)
            ->firstOrFail();

        if ($payment->payment_paid) {
            return redirect()->route('payment')->with('success', 'Pembayaran sudah lunas.');
        }

        $baseUrl = rtrim(env('CASHI_BASE_URL', 'https://cashi.id'), '/');
        $apiKey  = env('CASHI_KEY', '');
        $orderId = $payment->payment_code ?: $payment->payment_id;

        try {
            $response = Http::withHeaders(['x-api-key' => $apiKey])
                ->timeout(30)
                ->get($baseUrl . '/api/check-status/' . $orderId);

            $data = $response->json();

            if (! empty($data['success']) && ($data['status'] ?? '') === 'SETTLED') {
                $this->markPaid($payment->payment_id, null);
                return redirect()->route('payment')->with('success', 'Pembayaran lunas, terima kasih.');
            }

            return redirect()->route('payment')->with('error', 'Pembayaran belum diterima (status: ' . ($data['status'] ?? 'unknown') . ').');
        } catch (\Throwable $e) {
            Log::error('Cashi check-status exception: ' . $e->getMessage(), ['order_id' => $orderId]);
            return redirect()->route('payment')->with('error', 'Gagal cek status, silakan coba lagi.');
        }
    }

    // Cron via URL (untuk hosting tanpa SSH): lindungi dengan CRON_KEY
    public function cronGenerate()
    {
        if (request()->get('key') !== env('CRON_KEY', '')) {
            abort(404);
        }

        try {
            \Artisan::call('generate:payment');
            return response()->json(['success' => true, 'output' => trim(\Artisan::output())]);
        } catch (\Throwable $e) {
            Log::error('cronGenerate: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cronSend()
    {
        if (request()->get('key') !== env('CRON_KEY', '')) {
            abort(404);
        }

        try {
            \Artisan::call('send:payment');
            return response()->json(['success' => true, 'output' => trim(\Artisan::output())]);
        } catch (\Throwable $e) {
            Log::error('cronSend: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function involke($payment, $code, $total, $description = null)
    {
        $url = '';
        $amount = (int) $total;

        // Cashi: min 2.000, max 10.000.000
        if ($amount < 2000) {
            Log::warning('Cashi: amount below minimum', ['order_id' => $code, 'amount' => $amount]);
            return $url;
        }

        $baseUrl = rtrim(env('CASHI_BASE_URL', 'https://cashi.id'), '/');
        $apiKey  = env('CASHI_KEY', '');

        if (empty($apiKey)) {
            Log::error('Cashi: CASHI_KEY missing in .env');
            return $url;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key'    => $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($baseUrl . '/api/create-order', [
                'amount'       => $amount,
                'order_id'     => $code,
                'kode_channel' => env('CASHI_CHANNEL', 'QRIS_CUSTOM'),
            ]);

            $data = $response->json();

            if (! $response->successful() || empty($data['success'])) {
                Log::error('Cashi create-order failed', [
                    'order_id' => $code,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                return $url;
            }

            $url = $data['checkout_url'] ?? '';
            $payment->payment_code = $data['orderId'] ?? $code;
            $payment->payment_url  = $url;
            $payment->save();

        } catch (\Throwable $e) {
            Log::error('Cashi create-order exception: ' . $e->getMessage(), ['order_id' => $code]);
        }

        return $url;
    }

    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Gateway-Signature', '');

        // --- Cashi webhook path (HMAC-SHA256) ---
        if (! empty($signature)) {
            $secret = env('CASHI_WEBHOOK', '');

            if (empty($secret)) {
                Log::error('Cashi webhook: CASHI_WEBHOOK missing in .env');
                return response()->json(['message' => 'Server misconfigured'], 500);
            }

            $expected = hash_hmac('sha256', $payload, $secret);

            if (! hash_equals($expected, $signature)) {
                Log::warning('Cashi webhook: invalid signature', ['ip' => $request->ip()]);
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            $data = json_decode($payload, true);
            Log::info('Cashi webhook payload', ['body' => $data]);

            // Test connection dari dashboard Cashi
            $orderId = trim($data['data']['order_id'] ?? '');
            if (is_string($orderId) && strpos($orderId, 'TEST-') === 0) {
                return response('Test connection successful', 200);
            }

            $event  = strtoupper($data['event'] ?? '');
            $status = strtoupper($data['data']['status'] ?? '');

            if ($event === 'PAYMENT_SETTLED' && $status === 'SETTLED') {
                $ok = $this->markPaid($orderId, $data['data']['payment_method'] ?? $data['data']['channel'] ?? null);

                return response()->json(['message' => $ok ? 'OK' : 'Order not found']);
            }

            Log::warning('Cashi webhook: event/status ignored', ['event' => $event, 'status' => $status, 'order_id' => $orderId]);

            return response()->json(['message' => 'OK']);
        }

        // --- Legacy Xendit fallback (IP whitelist) ---
        Log::info($request->all());
        $status = $request->get('status');
        $external_id = $request->get('external_id');
        $method = $request->get('payment_method');

        $allow = [
            '52.89.130.89',
            '52.41.247.32',
            '52.11.161.195',
            '18.142.75.249',
            '18.142.89.214',
            '18.142.84.176',
            '52.221.140.31',
            '18.139.168.99',
            '18.142.72.148',
            '54.188.50.182',
            '54.245.87.198',
            '44.239.222.129',
        ];

        $ip = $request->ip();
        Log::info($ip);

        if(!in_array($ip, $allow)){
            Log::alert($ip);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if($status == 'PAID')
        {
            $this->markPaid($external_id, $method);
        }

        return response()->json($request->all());
    }

    private function markPaid($paymentId, $method = null)
    {
        $paymentId = trim((string) $paymentId);

        if ($paymentId === '') {
            Log::warning('Cashi markPaid: empty order id');
            return false;
        }

        // Cashi mengembalikan orderId internal; cocokkan payment_code dulu, lalu payment_id
        $payment = Payment::where('payment_code', $paymentId)->first()
            ?? Payment::find($paymentId);

        if (empty($payment)) {
            Log::warning('Cashi markPaid: payment not found', ['order_id' => $paymentId]);
            return false;
        }

        if ($payment->payment_paid) {
            Log::info('Cashi markPaid: already paid', ['order_id' => $paymentId]);
            return true;
        }

        $payment->update([
            'payment_paid'   => 1,
            'payment_done'   => date('Y-m-d H:i:s'),
            'payment_method' => $method,
        ]);

        Log::info('Cashi markPaid: payment_paid=1', ['order_id' => $paymentId]);

        return true;
    }

    public function updateProfile()
    {
        if (! auth()->check()) {
            return redirect('/');
        }

        $user = auth()->user();

        // Validate the input
        $validatedData = request()->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'birthday'         => 'nullable|date',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:500',
            'address_kk'       => 'nullable|string|max:500',
            'current_password' => 'nullable|string',
            'new_password'     => 'nullable|string|min:8|confirmed',
        ]);

        try {
            // Update basic profile information
            $user->update([
                'name'       => $validatedData['name'],
                'email'      => $validatedData['email'],
                'birthday'   => $validatedData['birthday'] ?? null,
                'phone'      => $validatedData['phone'] ?? null,
                'address'    => $validatedData['address'] ?? null,
                'address_kk' => $validatedData['address_kk'] ?? null,
            ]);

            // Handle password change if provided
            if (! empty($validatedData['current_password']) && ! empty($validatedData['new_password'])) {
                // Verify current password
                if (! Hash::check($validatedData['current_password'], $user->password)) {
                    return redirect()->back()
                        ->withErrors(['current_password' => 'Current password is incorrect'])
                        ->withInput();
                }

                // Update password
                $user->update([
                    'password' => Hash::make($validatedData['new_password']),
                ]);
            }

            return redirect()->back()->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update profile. Please try again.')
                ->withInput();
        }
    }

    public function blog($slug)
    {
        $page     = Page::slug($slug)->first();
        $template = $page->acf->template;

        return view('public.blog', $this->share([
            'template' => $template,
        ]));
    }
}
