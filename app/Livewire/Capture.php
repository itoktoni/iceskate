<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Native\Mobile\Events\Camera\PhotoTaken;
use Native\Mobile\Facades\Camera;
use Native\Mobile\Facades\Browser;

class Capture extends Component
{
    public $photoPath;

    public function render()
    {
        Camera::getPhoto();
        Camera::pickImages('images', false);

        return view('livewire.capture');
    }

    #[On('native:'.PhotoTaken::class)]
    public function handlePhotoTaken(string $path)
    {
        // Process the captured photo
        $this->photoPath = $path;
    }
}
