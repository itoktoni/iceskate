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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Plugins\Cms;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;
use Illuminate\Support\Facades\Crypt;
use LaravelQRCode\Facades\QRCode;

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

        $performance = Race::select('*')
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

            $qr = null;
            $path = public_path() . '/qr-code.png';

            $data = json_encode([
                'u' => $user_id,
                'j' => $jadwal->jadwal_id ?? null,
                'p' => $token->payment_id ?? null,
                't' => $token->total ?? 0
            ]);

            $encrypted = Crypt::encryptString($data);
            $qr   = QRCode::text($encrypted)->setOutfile($path)->png();

            return view('public.detailkehadiran', $this->share([
                'page'     => $page,
                'template' => $template,
                'jadwal'   => $jadwal,
                'single'   => $single,
                'kehadiran'   => $kehadiran,
                'qr'   => $qr,
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

        $five = Payment::where('payment_id_user', auth()->user()->id)
            ->where('payment_paid', 1)
            ->where('payment_iuran', 1)
            ->whereYear('payment_done', now('Y'))
            ->whereMonth('payment_done', now('m'))
            ->whereDay('payment_done', '<=', 10)
            ->count() > 1 ? true : false;

        return view('public.payment', $this->share([
            'page'     => $page,
            'template' => $template,
            'iuran'    => $iuran,
            'five'    => $five,
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

        $code    = unic(10) . date('Ymd');
        $payment = Payment::create([
            'payment_id'      => $code,
            'payment_tanggal' => now()->format('Y-m-d'),
            'payment_id_user' => auth()->user()->id,
            'payment_value'   => $total,
        ]);

        if(request()->get('iuran') == null)
        {
            return redirect()->back()->with('error', 'Tidak ada iuran yang dipilih');
        }

        foreach (request()->get('iuran') as $key => $value) {
            $payment->has_iuran()->attach($key, ['iuran_harga' => $value]);
        }

        $url = $this->involke($payment, $code, $total);

        return redirect()->to($url);
    }

    private function involke($payment, $code, $total)
    {
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));

        $apiInstance = new InvoiceApi;
        $url         = '';

        $create_invoice_request = new CreateInvoiceRequest([
            'external_id'                      => $code,
            'description'                      => 'Payment for Iceskate Membership',
            'amount'                           => $total,
            'invoice_duration'                 => 172800,
            'currency'                         => 'IDR',
            'reminder_time'                    => 1,
            'payment_methods'                  => [
                'CREDIT_CARD', 'OVO', 'ASTRAPAY', 'BNI', 'BSI', 'BRI', 'CIMB', 'BJB', 'PERMATA', 'QRIS', 'SHOPEEPAY', 'DANA', 'BCA', 'MANDIRI',
            ],
            'customer'                         => [
                'email'       => auth()->user()->email,
                'given_names' => auth()->user()->name,
                'surname'     => auth()->user()->name,
            ],
            "customer_notification_preference" => [
                "invoice_created"  => [
                    "whatsapp",
                    "email",
                ],
                "invoice_reminder" => [
                    "whatsapp",
                    "email",
                ],
                "invoice_paid"     => [
                    "whatsapp",
                    "email",
                ],
            ],
            'success_redirect_url'             => config('app.url').'payment',
            'failure_redirect_url'             => config('app.url').'payment',
        ]);

        try {

            $result = $apiInstance->createInvoice($create_invoice_request);
            $url    = $result->getInvoiceUrl();
            $payment->payment_code = $result->getId();
            $payment->payment_url  = $url;
            $payment->save();

        } catch (\Xendit\XenditSdkException $e) {
            echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
            echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }

        return $url;
    }

    public function webhook(Request $request)
    {
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
            $payment =  Payment::find($external_id);

            if(!empty($payment))
            {
                $payment->update([
                    'payment_paid' => 1,
                    'payment_done' => date('Y-m-d H:i:s'),
                    'payment_method' => $method,
                ]);
            }
        }

        return response()->json($request->all());
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
