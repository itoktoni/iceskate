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

        @auth

        @if (auth()->user()->role == 'user')

        <div class="col-lg-12 mb-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a style="text-align: left;" href="{{ route('userprofile') }}">
                                <h3>{{ auth()->user()->name ?? '' }}</h3>
                            </a>
                        </div>

                        <div class="col-6">
                            <a style="text-align: right;" href="{{ route('userprofile') }}">
                                <h5 style="color:#007bff">Change Profile</h5>
                            </a>
                        </div>
                    </div>
                    <div id="">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Category</small>
                                <div class="h5">{{ !empty($user->has_category) ? $user->has_category->field_name : '' }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Age</small>
                                <div class="h5">
                                    @if($user)
                                    @php

                                    $birthday = \Carbon\Carbon::parse($user->birthday);
                                    $now = \Carbon\Carbon::now();
                                    $ageInYears = $birthday->age;
                                    $ageInMonths = $birthday->diffInMonths($now) % 12;
                                    echo $ageInYears . ' Years ' . $ageInMonths . ' Month';

                                    @endphp
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <small class="text-muted">Phone</small>
                                <div class="h5">{{ auth()->user()->phone ?? '' }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">ID Member</small>
                                <div class="h5">{{ auth()->user()->id ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         @endif

        @endauth

        <div>
            <iframe src="https://j1st-tracker.ai.studio" width="100%" height="2600px" style="border: none; " title="J1st Tracker"></iframe>
        </div>

    </div>


</section>