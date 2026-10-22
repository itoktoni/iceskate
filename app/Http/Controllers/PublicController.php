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
        $footerMenu = Menu::slug('footer')->first();
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
            'footerMenu'          => $footerMenu,
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

        // VOUCHER 5 (iuran_id 1): tanpa batas jumlah, hanya tgl 1-10
        $countV5 = Payment::where('payment_id_user', auth()->user()->id)
            ->where('payment_paid', 1)
            ->where('payment_iuran', 1)
            ->whereYear('payment_tanggal', now()->format('Y'))
            ->whereMonth('payment_tanggal', now()->format('m'))
            ->count();

        $boughtV5Once = $countV5 >= 1; // sudah beli >=1x -> buka akses VOUCHER 1+
        $boughtV5 = false; // VOUCHER 5 tidak dikunci Lunas, boleh beli berulang

        // Aturan voucher:
        // - tgl 1-10 : VOUCHER 5 (tanpa batas) / VOUCHER 1+ (sudah beli V5 >=1x, boleh beli berulang)
        // - lewat tgl 10 : hanya VOUCHER 1
        $five = $boughtV5Once && $day <= 10; // tampilkan VOUCHER 1+
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

        // VOUCHER 5 (iuran_id 1): tanpa batas jumlah, hanya boleh tgl 1-10
        if ((int) $id === 1 && (int) now()->format('d') > 10) {
            return redirect()->route('payment')->with('error', 'VOUCHER 5 hanya bisa dibeli tanggal 1-10.');
        }

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
            Log::error('iPaymu involke exception: ' . $th->getMessage(), ['order_id' => $code]);
        }

        //InvoiceService::generate($payment->payment_id);

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

        if (! empty($payment->payment_url)) {
            return redirect()->route('payment')->with(
                'error',
                'Pembayaran belum diterima. Silakan lanjutkan pembayaran: ' . $payment->payment_url
            );
        }

        return redirect()->route('payment')->with('error', 'Pembayaran belum diterima.');
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
            'price' => [(string) $amount],
            'description' => [$label],
            'returnUrl' => route('payment'),
            'notifyUrl' => route('webhook.ipaymu'),
            'cancelUrl' => route('payment'),
            'referenceId' => $code,
        ];
        // iPaymu menolak signature (401) bila ada field kosong; kirim buyer hanya bila terisi
        if ($user) {
            foreach (['buyerName' => $user->name ?? '', 'buyerEmail' => $user->email ?? '', 'buyerPhone' => $user->phone ?? ''] as $key => $value) {
                if (trim((string) $value) !== '') {
                    $body[$key] = (string) $value;
                }
            }
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

    private static function isValidIpaymuCallback(array $data, string $receivedSignature, string $secret, ?string &$canonicalJson = null, ?string &$calculated = null): bool
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
            } elseif ($value === null) {
                $normalized[$key] = null;
            } else {
                $normalized[$key] = (string) $value;
            }
        }
        if (! array_key_exists('additional_info', $normalized)) {
            $normalized['additional_info'] = [];
        }

        ksort($normalized, SORT_STRING);
        $json = str_replace('/', '\\/', json_encode($normalized, JSON_UNESCAPED_SLASHES));
        $canonicalJson = $json;
        $calculated = hash_hmac('sha256', $json, $secret);

        return hash_equals($calculated, strtolower(trim($receivedSignature)));
    }

    public function webhook(Request $request)
    {
        $receivedSignature = $request->header('X-Signature', '');
        $secret = trim((string) env('IPAYMU_VA', ''));

        if (empty($receivedSignature) || empty($secret)) {
            Log::warning('iPaymu webhook: missing signature or IPAYMU_VA', ['ip' => $request->ip()]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = $request->isJson()
            ? ($request->json()->all() ?? [])
            : $request->post();

        if (empty($data)) {
            $data = $request->all();
        }

        // shortcut: verbose forensics until signature verified, then trim back to ip-only log.
        if (! self::isValidIpaymuCallback($data, $receivedSignature, $secret, $canonicalJson, $calculated)) {
            Log::warning('iPaymu webhook: invalid signature', [
                'ip' => $request->ip(),
                'content_type' => $request->header('Content-Type'),
                'received_prefix' => substr($receivedSignature, 0, 12) . '...len=' . strlen($receivedSignature),
                'calculated_prefix' => substr((string) $calculated, 0, 12) . '...',
                'canonical' => $canonicalJson,
                'raw' => mb_substr((string) $request->getContent(), 0, 2000),
                'va_len' => strlen($secret),
                'body' => $data,
            ]);
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
