@extends('layouts.public')

@section('content')

@if(!empty($template))
    @foreach($template as $section)
        @includeIf('public.section.'.$section->getType(), ['data' => $section])
    @endforeach
@endif

    <style>
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
                    <h3 class="mb-0">Pembayaran Iuran</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('iuran') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered">
                                <tr>
                                    <td class="text-center" style="width: 50px;">
                                        <input type="checkbox" id="select-all">
                                    </td>
                                    <td>Nama</td>
                                    <td style="width: 150px; text-align: right;">Harga</td>
                                </tr>

                                @foreach($iuran as $item)
                                @php
                                    $visit = (Carbon\Carbon::parse($item->iuran_tanggal)->format('MY') <= now()->format('MY') && $item->iuran_type == "VISIT" && Carbon\Carbon::parse($item->iuran_tanggal)->format('d') > 10)
                                    || (Carbon\Carbon::parse($item->iuran_tanggal)->format('MY') <= now()->format('MY') && $item->iuran_type == "VISIT");


                                    $bulanan = (Carbon\Carbon::parse($item->iuran_tanggal)->format('MY') >= now()->format('MY') && $item->iuran_type == "BULANAN" && Carbon\Carbon::parse($item->iuran_tanggal)->format('d') <= 10);
                                @endphp

                                @if ($visit)
                                <tr>
                                    <td class="text-center">
                                        @if ($payment->where('iuran_id', $item->iuran_id)->count() == 0)
                                            <input type="checkbox" name="iuran[{{ $item->iuran_id }}]" value="{{ $item->iuran_harga }}">
                                        @endif
                                    </td>
                                    <td>
                                        <b>{{ $item->iuran_nama }}</b> ({{ Carbon\Carbon::parse($item->iuran_tanggal)->format('d M Y') }})
                                        <br>
                                        {!! nl2br($item->iuran_keterangan) !!}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($item->iuran_harga, 0, ',', '.') }}</td>
                                </tr>
                                @endif

                                @if ($bulanan)
                                <tr>
                                    <td class="text-center">
                                         @if ($payment->where('iuran_id', $item->iuran_id)->count() == 0)
                                        <input type="checkbox" name="iuran[{{ $item->iuran_id }}]" value="{{ $item->iuran_harga }}">
                                        @endif
                                    </td>
                                    <td>
                                        <b>{{ $item->iuran_nama }}</b> - ({{ Carbon\Carbon::parse($item->iuran_tanggal)->format('M Y') }})
                                        <br>
                                        {!! nl2br($item->iuran_keterangan) !!}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($item->iuran_harga, 0, ',', '.') }}</td>
                                </tr>
                                @endif

                                @if ($item->iuran_type == "EVENT")
                                <tr>
                                    <td class="text-center">
                                        @if ($payment->where('iuran_id', $item->iuran_id)->count() == 0)
                                        <input type="checkbox" name="iuran[{{ $item->iuran_id }}]" value="{{ $item->iuran_harga }}">
                                        @endif
                                    </td>
                                    <td>
                                        <b>{{ $item->iuran_nama }}</b>
                                        <br>
                                        {!! nl2br($item->iuran_keterangan) !!}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($item->iuran_harga, 0, ',', '.') }}</td>
                                </tr>
                                @endif

                                @endforeach

                            </table>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('history') }}" style="margin-right: 10px;padding: 7px 15px;background: #6c757d;color: white;border-radius: 5px;text-decoration: none;">
                                <i class="fa fa-arrow-left"></i> History Pembayaran
                            </a>

                            <button style="padding: 7px 15px;background: var(--primary-color);color: white;border-radius: 5px;text-decoration: none;" type="submit">
                                <i class="fa fa-save"></i> Bayar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>



@endsection