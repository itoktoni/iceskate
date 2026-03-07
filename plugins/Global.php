<?php

use App\Dao\Enums\BellType;
use App\Events\SendBroadcast;
use App\Facades\Model\UserModel;
use Carbon\Carbon;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Bus\Batch;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravie\SerializesQuery\Eloquent;
use MBarlow\Megaphone\Types\General;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;

define('ACTION_CREATE', 'getCreate');
define('ACTION_UPDATE', 'getUpdate');
define('ACTION_DELETE', 'getDelete');
define('ACTION_EMPTY', 'empty');
define('ACTION_TABLE', 'getTable');
define('ACTION_PRINT', 'getPrint');
define('ACTION_EXPORT', 'getExport');
define('ERROR_PERMISION', 'Maaf anda tidak punya otorisasi untuk melakukan hal ini');

define('HAS_LEVEL_3', 'has_level3');
define('HAS_LEVEL_2', 'has_level2');
define('HAS_LEVEL_1', 'has_level1');
define('HAS_BRAND', 'has_brand');

define('UPLOAD', 'upload');
define('KEY', 'key');

function module($module = null)
{
    return SharedData::get($module);
}

function moduleCode($name = null)
{
    return ! empty($name) ? $name : SharedData::get('menu_code');
}

function moduleController($name = null)
{
    return ! empty($name) ? $name : SharedData::get('module_code');
}

function moduleName($name = null)
{
    return ! empty($name) ? __($name) : __(SharedData::get('menu_name'));
}

function moduleAction($name = null)
{
    return moduleCode().'.'.$name;
}

function moduleRoute($action, $param = false)
{
    return $param ? route(moduleAction($action), $param) : route(moduleAction($action));
}

function modulePath($name = null)
{
    return ! empty($name) ? $name : moduleController($name);
}

function modulePathTable($name = null, $core = false)
{
    $path = $core ? 'core.' : 'pages.';

    if ($name) {
        return $path.$name.'.table';
    }

    return $path.moduleController().'.table';
}

function modulePathPrint($name = null)
{
    if ($name) {
        return 'report.'.moduleController().'.'.$name;
    }

    return 'report.master.print';
}

function modulePathForm($name = null, $template = null, $path = false)
{
    if (is_string($path)) {
        $path = $path;
    } else {
        $path = $path ? 'core.' : 'pages.';
    }

    if ($template && $name) {
        return $path.$template.'.'.$name;
    }

    if ($name) {
        return $path.moduleController().'.'.$name;
    }

    if ($template) {
        return $path.$template.'.form';
    }

    return $path.moduleController().'.form';
}

function moduleView($template, $data = [])
{
    $view = view($template)->with($data);

    if (request()->header('hx-request') && env('APP_SPA', false)) {
        $view = $view->fragment('content');
    }

    return $view;
}

function formatLabel($value)
{

    $label = Str::of($value);
    if ($label->contains('_')) {
        $label = $label = $label->explode('_')->last();
    } else {
        $label = $label->replace('[]', '');
    }

    return ucfirst($label);
}

function formatAttribute($value)
{

    $label = Str::of($value);
    if ($label->contains(' ')) {
        $label = $label = $label->explode(' ')->last();
    } else {
        $label = $label->replace('[]', '');
    }

    return ucfirst($label);
}

function formatWorld($value)
{
    if (! empty($value)) {
        return Str::title(str_replace('_', ' ', Str::snake($value))) ?? 'Unknow';
    }
}

function showValue($value)
{
    if ($value == 0) {
        return '';
    }

    return $value;
}

function role($role)
{
    return auth()->check() && auth()->user()->role == $role;
}

function level($value)
{
    return auth()->check() && auth()->user()->level >= $value;
}

function lang($key, $default = null)
{
    if (is_array($key)) {
        return collect($key)->map(function ($item) use ($default) {
            return $this->lang($item, $default);
        })->all();
    }
    return trans($key, [], $default);
}

function uploadImage($file, $folder, $width = 300)
{
    if(empty($file))
    {
        return false;
    }

    $extension = $file->extension();
    $name = time().'.'.$extension;

    $image = Image::read($file);
    $resizedImage = $image->scaleDown($width);

    if(env('PATH_LINK', false))
    {
        File::ensureDirectoryExists(storage_path('app/public/files/'.$folder));
        $resizedImage->save(storage_path('app/public/files/'.$folder.'/'.$name));
    }
    else
    {
        File::ensureDirectoryExists(public_path('files/'.$folder));
        $resizedImage->save(public_path('files/'.$folder.'/'.$name));
    }

    return $name;
}

function imageUrl($value, $folder = null)
{
    $path = $folder ? $folder : moduleCode();

    if(empty($value))
    {
        return url('images/noimage.jpeg');
    }

    if(env('PATH_LINK', false))
    {
        return file_exists(storage_path('app/public/files/'.$path.'/'.$value)) ? url('storage/files/'.$path.'/'.$value) : url('images/noimage.jpeg');
    }
    else
    {
        return file_exists(public_path('files/'.$path.'/'.$value)) ? url('files/'.$path.'/'.$value) : url('images/noimage.jpeg');
    }


    $path = $folder ? $folder : moduleCode();

    return $value ? url('storage/files/' . $path.'/'.$value) : url('images/noimage.jpeg');
}

function logoUrl($isLogo = true)
{
    $logo = env('APP_LOGO');

    if(env('PATH_LINK', false))
    {
        return file_exists(storage_path('app/public/'.$logo)) && $logo ? url('storage/'.$logo) : url('images/logo.png');
    }
    else
    {
        return file_exists(public_path($logo)) && $logo ? url($logo) : url('images/logo.png');
    }
}

function backgroundUrl($isLogo = true)
{
    $logo = env('APP_BACKGROUND');

    if(env('PATH_LINK', false))
    {
        return file_exists(storage_path('app/public/'.$logo)) && $logo ? url('storage/'.$logo) : url('images/background.jpg');
    }
    else
    {
        return file_exists(public_path($logo)) && $logo ? url($logo) : url('images/background.jpg');
    }
}

function formatDateMySql($value, $datetime = false)
{

    if ($datetime === false) {
        $format = 'Y-m-d';
    } elseif ($datetime === true) {
        $format = 'Y-m-d H:i:s';
    } else {
        $format = $datetime;
    }

    if ($value instanceof Carbon) {
        $value = $value->format($format);
    } elseif (is_string($value)) {
        $value = SupportCarbon::parse($value)->format($format);
    }

    return $value ?: null;
}

function formatDate($value, $datetime = false)
{
    if ($datetime === false) {
        $format = 'd/m/Y';
    } elseif ($datetime === true) {
        $format = 'd/m/Y H:i:s';
    } else {
        $format = $datetime;
    }

    if (empty($value)) {
        return null;
    }

    if ($value instanceof Carbon) {
        $value = $value->format($format);
    } elseif (is_string($value)) {
        $value = SupportCarbon::parse($value)->format($format);
    }

    return $value ?: null;
}

function statusExpired($date)
{
    if (empty($date)) {
        return null;
    }

    return $date < date('Y-m-d') ? 'Expired' : 'Berlaku';
}

function iteration($model, $key)
{
    return $model->firstItem() + $key;
}

function unic($length)
{
    $chars = array_merge(range('a', 'z'), range('A', 'Z'));
    $length = intval($length) > 0 ? intval($length) : 16;
    $max = count($chars) - 1;
    $str = '';

    while ($length--) {
        shuffle($chars);
        $rand = mt_rand(0, $max);
        $str .= $chars[$rand];
    }

    return strtoupper($str);
}

function getClass($class)
{
    return (new \ReflectionClass($class))->getShortName();
}

function getLowerClass($class)
{
    return strtolower(getClass($class));
}

function setString($value)
{
    return '"'.$value.'"';
}

/*
megaphone
*/

if (! function_exists('getMegaphoneTypes')) {
    function getMegaphoneTypes(): array
    {
        return array_merge(
            (array) config('megaphone.types', []),
            array_keys((array) config('megaphone.customTypes', []))
        );
    }
}

if (! function_exists('getMegaphoneAdminTypes')) {
    function getMegaphoneAdminTypes(): array
    {
        $adminList = config('megaphone.adminTypeList');

        if (is_array($adminList)) {
            return $adminList;
        }

        return getMegaphoneTypes();
    }
}

if (! function_exists('sendNotification')) {
    function sendNotification($data, $type = BellType::Info, $user_id = 0)
    {
        if ($data instanceof General) {
            foreach (UserModel::all() as $model) {
                $model->notify($data);
            }

            event(new SendBroadcast($data, $type));
        } else {
            if (! empty($user_id)) {

                $model = UserModel::find($user_id);
            } else {
                $model = UserModel::find(auth()->user()->id);
            }

            event(new SendBroadcast($data, $type, $user_id));
            $model->notify($data);
        }
    }
}

if (! function_exists('exportCsv')) {
    function exportCsv($name, $query, $jobClass, $request, $delimiter = ',', $chunkSize = 1000)
    {
        $total = $query->count();
        $numberOfChunks = ceil($total / $chunkSize);

        $name = 'public/files/export/'.Str::snake($name).'-'.now()->toDateString().'-'.str_replace(':', '-', now()->toTimeString()).'.csv';
        $batches = [];

        for ($i = 1; $i <= $numberOfChunks; $i++) {
            $batches[] = new $jobClass($name, Eloquent::serialize($query), $request, $i, $chunkSize, $delimiter);
        }

        $user_id = auth()->user()->id;

        $bus = Bus::batch($batches)
            ->name('Export Users')
            ->then(function (Batch $batch) use ($name, $user_id) {
                Storage::put($name, file_get_contents($name));

                Log::info($name);

                $notification = new \MBarlow\Megaphone\Types\NewFeature(
                    'Download File Success',
                    'File Ready to download',
                    asset(str_replace('public/', '', $name)),
                    'Download'
                );

                Log::info('notif');

                sendNotification($notification, BellType::Success, $user_id);

            })
            ->catch(function (Batch $batch, Throwable $e) use ($user_id) {

                Log::error($e->getMessage());

                $notification = new \MBarlow\Megaphone\Types\Important(
                    'Download File Error',
                    $e->getMessage(),
                );

                sendNotification($notification, BellType::Error, $user_id);

            })
            ->finally(function (Batch $batch) use ($name) {
                Storage::disk('local')->delete($name);
            })
            ->dispatch();

        return $bus;
    }
}
