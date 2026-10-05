<section id="hero" class="text-light relative" style="min-height: 60em"
    data-bgimage="url('{{ $data->background->guid ?? null }}') center">
    <div class="container relative z-2">
        <div class="row g-4">
            <div class="col-xl-6 col-lg-6">
                <div class="spacer-double"></div>
                <h1 class="wow fadeInUp" data-wow-delay=".0s"> {!! nl2br($data->title) ?? '' !!}</h1>
                <p class="me-lg-5 mb-4 wow fadeInUp" data-wow-delay=".2s">{!! nl2br($data->description) ?? '' !!}</p>

                @if (!empty($data->link['title']))
                    <div class="d-flex align-items-center wow fadeInUp" data-wow-delay=".9s">
                        <a class="btn-main fx-slide me-4 wow fadeInUp" data-wow-delay=".4s"
                            href="{{ $data->link['url'] ?? '' }}">
                            <span>{{ $data->link['title'] ?? '' }}</span>
                        </a>

                        <a class="de-flex align-items-center text-white popup-youtube"
                            href="{{ $data->video['url'] ?? '' }}">
                            <div class="btn-play sm circle wow scaleIn"><span></span></div>
                            <div class="ms-3 fw-bold">{{ $data->video['title'] ?? '' }}</div>
                        </a>
                    </div>
                @endif

                @php
                    $heroQuickLinks = [];
                    try {
                        if (isset($footerMenu) && optional($footerMenu)->items && $footerMenu->items->count()) {
                            $heroQuickLinks = \App\Services\MenuTreeBuilder::toTree(
                                \App\Services\MenuTreeBuilder::fromCorcel($footerMenu->items)
                            );
                        }
                    } catch (\Throwable $e) { $heroQuickLinks = []; }
                @endphp
                @if(count($heroQuickLinks))
                    <ul class="hero-quick-links wow fadeInUp" data-wow-delay=".6s">
                        @foreach($heroQuickLinks as $node)
                        @php
                            $m = $node['item']['_model'] ?? null;
                            $inst = null;
                            try { $inst = $m ? $m->instance() : null; } catch (\Throwable $e) {}
                            $t = $inst->post_title ?? $m->title ?? $node['item']['title'] ?? 'Menu';
                            $mu = null;
                            try { $mu = $m->meta->_menu_item_url ?? null; } catch (\Throwable $e) {}
                            $sl = $inst->post_name ?? $m->post_name ?? null;
                            $href = $mu ?: $sl ?: '#';
                        @endphp
                        <li><a href="{{ $href }}">{{ $t }}</a></li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>
    </div>
    <div class="gradient-edge-bottom"></div>
</section>

<style>
    /* Gradasi bawah (opsional, menyesuaikan kode sebelumnya) */
    .gradient-edge-bottom {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 80px;
        background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.8));
    }

    /* Spacer supaya ada ruang ekstra jika diperlukan */
    .spacer-double {
        padding-top: 6rem;
        padding-bottom: 6rem;
    }

    /* Bar horizontal di bawah deskripsi hero */
    .hero-quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem 1.5rem;
        list-style: none;
        padding-left: 0;
        margin-top: 1.5rem;
        margin-bottom: 0;
    }
    .hero-quick-links a {
        color: #fff;
        font-weight: 600;
        text-decoration: none;
        border-bottom: 1px solid rgba(255,255,255,.4);
        padding-bottom: 2px;
    }
    .hero-quick-links a:hover { border-color: #fff; }
    @media (max-width: 576px) {
        .hero-quick-links { flex-wrap: nowrap; overflow-x: auto; gap: 0 1.25rem; padding-bottom: .5rem; }
        .hero-quick-links li { white-space: nowrap; }
    }
</style>

<script>
    function setHeroHeight() {
        var hero = document.querySelector('#hero');
        if (!hero) return;
        hero.style.minHeight = window.innerHeight + 'px';
    }

    // Set pertama kali
    setHeroHeight();

    // Update saat resize / rotate
    window.addEventListener('resize', setHeroHeight);
    window.addEventListener('orientationchange', setHeroHeight);
</script>
