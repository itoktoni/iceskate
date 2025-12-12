<section class="p-0 mt-5 mb-5">

    <div class="container">
        <div class="row mb-3 g-4 align-items-center justify-content-between">
            <div class="col-lg-6 mb-5 wow fadeIn" data-wow-delay=".2s">
                <h2 class="wow fadeInUp">{{ $data->title ?? '' }}</h2>
                {!! nl2br($data->description) ?? '' !!}
            </div>

            <div class="col-lg-6">
                <div class="relative">
                    <div class="de-custom-nav d-flex flex-end" data-target="#services-carousel">
                        <div class="d-prev"></div>
                        <div class="d-next"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div id="services-carousel" class="owl-2-cols-center owl-carousel owl-theme">

                    @foreach ($data->item as $item)
                        <div class="item">
                            <div class="relative bg-color text-light rounded-1 overflow-hidden">
                                <div class="row g-0">
                                    <div class="col-5 d-flex flex-column justify-content-between p-40">
                                        <div>
                                            <h3 class="fw-bold mb-2">{{ $item->title ?? '' }}</h3>
                                            {!! nl2br($item->description) ?? '' !!}
                                        </div>
                                        <div>
                                            <a href="{{ $item->link['url'] ?? '' }}"
                                                class="id-color text-uppercase fw-semibold small text-decoration-none">
                                                {{ $item->link['title'] ?? '' }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-7 angled-wrapper">
                                        <img src="{{ $item->image->guid ?? '' }}" alt="{{ $item->title ?? '' }}"
                                            class="h-100 w-100 object-cover">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>

            </div>
        </div>
    </div>

</section>
