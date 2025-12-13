<?php

namespace App\Http\Controllers;

use App\Dao\Enums\Core\RoleType;
use App\Dao\Models\Jadwal;
use App\Dao\Models\Race;
use App\Models\Menu;
use App\Models\Page;
use Plugins\Cms;

class PublicController extends Controller
{
    public function share($data)
    {
        $menu = Menu::slug('top')->first();
        $jadwal = Jadwal::leftJoinRelationship('has_category')->get();
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
       $page = Page::slug('gallery')->first();
       $template = $page->acf->template;

        return view('public.homepage', $this->share([
            'page' => $page,
            'template' => $template
        ]));
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
