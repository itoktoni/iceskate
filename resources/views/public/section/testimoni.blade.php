<section class="relative" data-bgimage="url({{ $data->background->guid ?? '' }}) center">
    <div class="gradient-edge-top"></div>
    <div class="gradient-edge-bottom"></div>
    <div class="sw-overlay op-5"></div>
    <div class="container relative z-2">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="owl-single-dots owl-carousel owl-theme">

                    @foreach ($data->item as $item)

                    <div class="item">

                        <h3 class="mb-4 mt-5 wow fadeInUp fs-36 text-white">{!! nl2br($item->title) ?? '' !!}</h3>
                        <span class="text-white wow fadeInUp">{{ $item->user ?? '' }}</span>
                    </div>

                    @endforeach

                </div>
            </div>
        </div>
    </div>
</section>
