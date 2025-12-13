<section class="text-light relative" data-bgimage="url('{{ $data->image->guid ?? null }}') top">
    <div class="container relative z-2">
        <div class="row g-4">
            <div class="col-lg-12 text-center">
                <div class="spacer-double"></div>
                <h1 class="mb-0">{{ $page->title ?? '' }}</h1>
                <div class="spacer-double"></div>
            </div>
        </div>
    </div>
    <div class="sw-overlay op-8"></div>
    <div class="gradient-edge-bottom"></div>
</section>

<section>
    <div class="container">
        <div class="row gx-5">
            <div class="col-lg-12">
                <div class="blog-read">

                    {!! nl2br($data->content) ?? '' !!}

                </div>

            </div>


        </div>
    </div>
</section>
