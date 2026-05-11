@extends('layouts.public')

@section('content')

@if(!empty($template))
    @foreach($template as $section)
        @includeIf('public.section.'.$section->getType(), ['data' => $section])
    @endforeach
@endif

    <style>
    .payment  {
        margin-top: -10rem !important;
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
                    <h3 class="mb-0">Pembayaran Iuran</h3>
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered">
                                <tr>
                                    <td class="text-center" style="width: 50px;">
                                        No
                                    </td>
                                    <td>Nama</td>
                                    <td style="width: 150px; text-align: right;">Harga</td>
                                    <td style="width: 150px; text-align: center;">Status</td>
                                </tr>

                                @foreach($history as $item)

                                <tr>
                                    <td class="text-center">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        <b>{{ $item->payment_id }} - {{ $item->iuran_nama }}</b> - ({{ Carbon\Carbon::parse($item->payment_tanggal)->format('M Y') }})
                                        <br>
                                        {!! nl2br($item->iuran_keterangan) !!}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($item->iuran_harga, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($item->payment_paid == 1)
                                        LUNAS
                                        @else
                                        <a href="{{ $item->payment_url }}">PENDING</a>
                                        @endif
                                    </td>
                                </tr>

                                @endforeach

                            </table>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('payment') }}" style="padding: 7px 15px;background: var(--primary-color);color: white;border-radius: 5px;text-decoration: none;">
                                <i class="fa fa-arrow-left"></i> Pembayaran
                            </a>
                        </div>
                </div>
            </div>
        </div>

    </div>



@endsection