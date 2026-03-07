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

    .payment  {
        margin-top: -5rem !important;
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
                    <h3 class="mb-0">Kehadiran</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('iuran') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered">
                                <tr>
                                    <td class="text-center" style="width: 50px;">
                                        No.
                                    </td>
                                    <td>Jadwal</td>
                                    <td style="width: 100px; text-align: center;">Info</td>
                                </tr>
                                @foreach($jadwal as $item)
                                @php
                                    $hadir = $kehadiran->where('jadwal_id', $item->jadwal_id)->count();
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>{{ $item->jadwal_tanggal }} - {{ $item->jadwal_keterangan }}</td>
                                    <td style="width: 100px; text-align: center; vertical-align: middle;">
                                        @if($hadir > 0)
                                            <span style="">Hadir</span>
                                        @else
                                            <a style="padding: 7px 15px;background: var(--primary-color);color: white;border-radius: 5px;text-decoration: none;" href="{{ route('kehadiran', ['id' => $item->jadwal_id]) }}">
                                                Detail
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            </div>
                        </div>

                        <div class="text-end">
                            {{ $jadwal->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>



@endsection