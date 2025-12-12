<section class="">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-12">

                 <h2 class="wow fadeInUp mt-2 text-center">
                    {{ $data->title ?? '' }}
                </h2>

                <div class="accordion">
                    <div class="accordion-section">
                        @foreach ($data->item as $item)

                        <div class="accordion-section-title" data-tab="#accordion-{{ $loop->iteration }}">
                            {{ $item->question ?? '' }}
                        </div>
                        <div class="accordion-section-content" id="accordion-{{ $loop->iteration }}">
                            {{ $item->answer }}
                        </div>

                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
