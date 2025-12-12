<section class="text-light relative" style="min-height: 60em" data-bgimage="url({{ $data->background->guid ?? null }}) center">
     <div class="container relative z-2">
         <div class="row g-4">
             <div class="col-xl-6 col-lg-6">
                 <div class="spacer-double"></div>
                 <h1 class="wow fadeInUp" data-wow-delay=".0s"> {!! nl2br($data->title) ?? '' !!}</h1>
                 <p class="me-lg-5 mb-4 wow fadeInUp" data-wow-delay=".2s">{!! nl2br($data->description) ?? '' !!}</p>

                 @if (!empty($data->link['title']))

                 <div class="d-flex align-items-center wow fadeInUp" data-wow-delay=".9s">
                     <a class="btn-main fx-slide me-4 wow fadeInUp" data-wow-delay=".4s" href="{{ $data->link['url'] ?? '' }}">
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
