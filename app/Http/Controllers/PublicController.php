<?php

namespace App\Http\Controllers;

use App\Dao\Enums\Core\RoleType;
use App\Dao\Models\Core\User;
use App\Dao\Models\Jadwal;
use App\Dao\Models\Race;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Support\Facades\Hash;
use Plugins\Cms;

class PublicController extends Controller
{
    public function share($data)
    {
        $menu = Menu::slug('top')->first();
        $jadwal = Jadwal::leftJoinRelationship('has_category')->get();

        $user = null;
        if(auth()->check())
        {
            $user = User::with('has_category')->find(auth()->user()->id);
        }

        $performance = Race::select('*')
            ->leftJoinRelationship('has_jarak')
            ->leftJoinRelationship('has_user');

            if(auth()->check() && auth()->user()->role == RoleType::User)
            {
                $performance = $performance->where('race_user_id', auth()->user()->id);
            }

        $performance = $performance->get();

        $default = [
            'logo_url' => Cms::logo_url(),
            'website_address' => Cms::website_address(),
            'website_email' => Cms::website_email(),
            'website_description' => Cms::website_description(),
            'website_phone' => Cms::website_phone(),
            'performance' => $performance,
            'menu' => $menu,
            'jadwal' => $jadwal,
            'user' => $user,
        ];

        return array_merge($default, $data);
    }

    public function index()
    {
       $homepage = Page::slug('homepage')->first();
       $template = $homepage->acf->template;

        return view('public.homepage', $this->share([
            'template' => $template
        ]));
    }

    public function performance()
    {
        if(!auth()->check())
        {
            return redirect('/');
        }

       $page = Page::slug('performance')->first();
       $user = User::where('role', RoleType::User);

       if(auth()->user()->role == RoleType::User)
       {
           $user = $user->where('id', auth()->user()->id)->first();
       }
       else
       {
           $user = $user->get();
       }

       $template = $page->acf->template;

        return view('public.homepage', $this->share([
            'page' => $page,
            'template' => $template,
            'user' => $user
        ]));
    }


    public function page($slug)
    {
       $page = Page::slug($slug)->first();
       $template = $page->acf->template;

        return view('public.homepage', $this->share([
            'page' => $page,
            'template' => $template
        ]));
    }

    public function userprofile()
    {
        if(!auth()->check())
        {
            return redirect('/');
        }

       $page = Page::slug('performance')->first();
       $template = $page->acf->template->first();

        return view('public.userprofile', $this->share([
            'page' => $page,
            'data' => $template
        ]));
    }

    public function updateProfile()
    {
        if(!auth()->check())
        {
            return redirect('/');
        }

        $user = auth()->user();

        // Validate the input
        $validatedData = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'address_kk' => 'nullable|string|max:500',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            // Update basic profile information
            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'birthday' => $validatedData['birthday'] ?? null,
                'phone' => $validatedData['phone'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'address_kk' => $validatedData['address_kk'] ?? null,
            ]);

            // Handle password change if provided
            if (!empty($validatedData['current_password']) && !empty($validatedData['new_password'])) {
                // Verify current password
                if (!Hash::check($validatedData['current_password'], $user->password)) {
                    return redirect()->back()
                        ->withErrors(['current_password' => 'Current password is incorrect'])
                        ->withInput();
                }

                // Update password
                $user->update([
                    'password' => Hash::make($validatedData['new_password'])
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
       $page = Page::slug($slug)->first();
       $template = $page->acf->template;

        return view('public.blog', $this->share([
            'template' => $template
        ]));
    }
}
