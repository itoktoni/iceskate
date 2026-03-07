@extends('layouts.public')

@section('content')

@if(!empty($template))
    @foreach($template as $section)
        @includeIf('public.section.'.$section->getType(), ['data' => $section])
    @endforeach
@endif

    <style>
    .page-item  {
        text-align: center !important;
    }

    .payment{
        margin-top: -5rem !important;
    }

    .float-right {
        float: right !important;
        text-align: right !important;
    }

    @media (max-width: 768px) {
        .payment {
            margin-top: 0 !important;
        }
    }
    </style>

    <div class="container payment">

        <!-- Profile Update Form -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{ $single->jadwal_nama }} - {{ $single->jadwal_tanggal }}</h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">

                            {!! nl2br($single->jadwal_keterangan) !!}

                        </div>
                    </div>

                </div>
                <div class="card-footer float-right">
                    <a href="{{ route('kehadiran') }}" style="padding: 7px 15px;margin-right:10px;background: #6c757d;color: white;border-radius: 5px;text-decoration: none;">
                            Kembali
                    </a>
                    <a href="{{ route('hadir', ['id' => $single->jadwal_id]) }}" onclick="return confirm('Are you sure you want to mark this attendance?');" style="padding: 7px 15px;background: var(--primary-color);color: white;border-radius: 5px;text-decoration: none;" >
                            <i class="fa fa-eye"></i> Hadir
                    </a>
                </div>
            </div>
        </div>

    </div>

    <br>

@endsection