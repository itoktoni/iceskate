<section class="pb-5">
    <div class="container">
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-6 text-center wow fadeIn" data-wow-delay=".2s">
                <h2 class="wow fadeInUp">{{ $data->title ?? '' }}</h2>
            </div>
        </div>
        <div class="row g-4 gx-5 align-items-center">
            <div class="col-lg-6">
                <div class="relative">
                    <div class="rounded-1 w-90 overflow-hidden wow zoomIn">
                        <img src="{{ $data->image->guid ?? '' }}" class="w-100 wow scaleIn" alt="Gym Training Session">
                        <div class="gradient-edge-bottom color abs w-100 h-40 bottom-0"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                @foreach ($data->item as $item)

                <div class="relative mb-4 wow fadeInUp" data-wow-delay=".2s">
                    <div class="absolute w-60px bg-color text-light text-center fs-32 py-3 rounded-1">
                        {{ $loop->iteration }}
                    </div>
                    <div class="ps-100">
                        <h4>{{ $item->name ?? '' }}</h4>
                        <p>{{ $item->description ?? '' }}.</p>
                    </div>
                </div>

                @endforeach

            </div>
        </div>

        <div class="spacer-single sm-hide"></div>
    </div>
</section>