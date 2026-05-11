@extends('layouts.public')

@section('content')

    @if (!empty($template))
        @foreach ($template as $section)
            @includeIf('public.section.' . $section->getType(), ['data' => $section])
        @endforeach
    @endif

    <style>
        .payment {
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
                                        <td>Nama</td>
                                        <td style="width: 150px; text-align: right;">Harga</td>
                                        <td style="width: 150px; text-align: center;">Bayar</td>
                                    </tr>

                                    @foreach ($iuran as $item)
                                        @php
                                            $bulanan =
                                                Carbon\Carbon::parse($item->iuran_tanggal)->format('MY') >=
                                                    now()->format('MY') &&
                                                $item->iuran_type == 'BULANAN' &&
                                                Carbon\Carbon::parse($item->iuran_tanggal)->format('d') <= 10;

                                        @endphp

                                        @if ($bulanan)
                                            <tr>
                                                <td>
                                                    <b>{{ $item->iuran_nama }}</b> -
                                                    ({{ Carbon\Carbon::parse($item->iuran_tanggal)->format('M Y') }})
                                                    <br>
                                                    {!! nl2br($item->iuran_keterangan) !!}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{ number_format($item->iuran_harga, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('send', ['id' => $item->iuran_id]) }}"
                                                        style="padding: 7px 15px;background: #198754;color: white;border-radius: 5px;text-decoration: none;">
                                                        <i class="fa-brands fa-whatsapp"></i> Bayar WA
                                                    </a>
                                                </td>
                                            </tr>
                                        @elseif ($item->iuran_type == 'EVENT')
                                            <tr>
                                                <td>
                                                    <b>{{ $item->iuran_nama }}</b>
                                                    <br>
                                                    {!! nl2br($item->iuran_keterangan) !!}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{ number_format($item->iuran_harga, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('send', ['id' => $item->iuran_id]) }}"
                                                        style="padding: 7px 15px;background: #198754;color: white;border-radius: 5px;text-decoration: none;">
                                                        <i class="fa-brands fa-whatsapp"></i> Bayar WA
                                                    </a>
                                                </td>
                                            </tr>
                                        @else
                                            @if ($five && $item->iuran_id == 2)
                                                <tr>
                                                    <td>
                                                        <b>{{ $item->iuran_nama }}</b>
                                                        ({{ Carbon\Carbon::parse($item->iuran_tanggal)->format('M Y') }})
                                                        <br>
                                                        {!! nl2br($item->iuran_keterangan) !!}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ number_format($item->iuran_harga, 0, ',', '.') }}\
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('send', ['id' => $item->iuran_id]) }}"
                                                            style="padding: 7px 15px;background: #198754;color: white;border-radius: 5px;text-decoration: none;">
                                                            <i class="fa-brands fa-whatsapp"></i> Bayar WA
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (!$five && $item->iuran_id == 3)
                                                <tr>
                                                    <td>
                                                        <b>{{ $item->iuran_nama }}</b>
                                                        ({{ Carbon\Carbon::parse($item->iuran_tanggal)->format('M Y') }})
                                                        <br>
                                                        {!! nl2br($item->iuran_keterangan) !!}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ number_format($item->iuran_harga, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('send', ['id' => $item->iuran_id]) }}"
                                                            style="padding: 7px 15px;background: #198754;color: white;border-radius: 5px;text-decoration: none;">
                                                            <i class="fa-brands fa-whatsapp"></i> Bayar WA
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('history') }}"
                                style="padding: 7px 15px;background: #6c757d;color: white;border-radius: 5px;text-decoration: none;">
                                <i class="fa fa-arrow-left"></i> History Pembayaran
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const itemCheckboxes = document.querySelectorAll('input[name^="iuran["]');

            // Handle select-all checkbox
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            // Handle individual checkboxes
            itemCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                });
            });
        });
    </script>

@endsection
