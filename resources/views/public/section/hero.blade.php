<section class="text-light relative" style="min-height: 60em"
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

            </div>
        </div>
    </div>
    <div class="gradient-edge-bottom"></div>
</section>

<style>
    /* Hero selalu setinggi layar */
    .bgcustom {
        position: relative;
        min-height: 100vh;
        /* fallback untuk browser lama */
        min-height: 100dvh;
        /* tinggi dinamis di browser modern */
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        /* konten vertikal center */
    }

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
</style>

<script>
    function setHeroHeight() {
        var hero = document.querySelector('.bgcustom');
        if (!hero) return;
        hero.style.minHeight = window.innerHeight + 'px';
    }

    // Set pertama kali
    setHeroHeight();

    // Update saat resize / rotate
    window.addEventListener('resize', setHeroHeight);
    window.addEventListener('orientationchange', setHeroHeight);
</script>
