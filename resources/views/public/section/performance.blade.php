@if (!empty($data->image->guid))
    <section class="text-light relative" data-bgimage="url('{{ $data->image->guid ?? null }}') top">
        <div class="container relative z-2">
            <div class="row g-4">
                <div class="col-lg-12 text-center">
                    <div class="spacer-double"></div>
                    <h1 class="mb-0">{{ $page->title ?? $data->title }}</h1>
                    <div class="spacer-double"></div>
                </div>
            </div>
        </div>
        <div class="sw-overlay op-8"></div>
        <div class="gradient-edge-bottom"></div>
    </section>
@endif

<section>
    <div class="container mb-5">

        <div>
            <iframe src="https://j1st-tracker.ai.studio" width="100%" height="2500px" style="border: none; "title="J1st Tracker"></iframe>
        </div>

    </div>


</section>