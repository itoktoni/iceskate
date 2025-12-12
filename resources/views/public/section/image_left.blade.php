<section>
    <div class="container">
        <div class="row g-4 align-items-center">

            <div class="col-lg-6">
                <div class="relative">
                    <div class="row g-4">
                        <div class="col-md-12 wow fadeInRight" data-wow-delay=".4s">
                            <div class="relative rounded-1 overflow-hidden">
                                <img src="{{ $data->photo->guid ?? '' }}" class="w-100" alt="">
                                <div class="de-overlay-gradient-color h-50 top-50"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="ms-lg-4">
                    <h2 class="wow fadeInUp" data-wow-delay=".2s">{{ $data->title }}</h2>

                    <p class="wow fadeIn" data-wow-delay=".4s">{!! nl2br($data->description) ?? '' !!}</p>

                    <a class="btn-main fx-slide mb10 mb-3 wow fadeIn" href="{{ $data->link['url'] ?? '' }}">
                        <span>
                            {{ $data->link['title'] ?? '' }}
                        </span>
                    </a>
                </div>
            </div>


        </div>

    </div>
</section>
