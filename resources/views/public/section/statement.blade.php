<section class="bg-color text-light pt-50 pb-50">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-9">
                <h3 class="mb-0 fs-32">{{ $data->content ?? '' }}</h3>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a class="btn-main fx-slide btn-line" href="{{ $data->link['url'] ?? '' }}">
                    <span>{{ $data->link['title'] ?? '' }}</span>
                </a>
            </div>
        </div>
    </div>
</section>
