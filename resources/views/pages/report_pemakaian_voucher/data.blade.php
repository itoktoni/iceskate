<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="8">
			<h3>
				<b>Report Data Pemakaian Voucher</b>
			</h3>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				laporan data pemakaian voucher berdasarkan tanggal
			</h3>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				Periode : {{ formatDate(request()->get('start_date')) }} - {{ formatDate(request()->get('end_date')) }}
			</h3>
		</td>
	</tr>
</table>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
			<tr>
				<th>NAMA KETERANGAN</th>
				<th>NAMA USER</th>
				<th>TGL PAKAI</th>
				<th>PEMAKAIAN</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_pembayaran = 0;
			@endphp

			@forelse($data as $key => $payment)
			@php
			$single = $payment->first();
			@endphp
			<tr>
				<td style="background-color: #d6d6d6" colspan="4">
					{{ $key }}
					{{ $single->iuran_nama }} -
					@if($single->iuran_type == 'BULANAN')
						{{ Carbon\Carbon::parse($single->iuran_tanggal)->translatedFormat('F Y') }}
					@else
						{{ Carbon\Carbon::parse($single->iuran_tanggal)->translatedFormat('d M Y') }}
					@endif
				</td>
			</tr>
			@foreach($payment as $table)
			@if(!empty($table->jadwal_nama))
			<tr>
				<td>
					{{ $table->jadwal_nama }} {{ formatDate($table->jadwal_tanggal) }} - {{ $table->jadwal_keterangan }}
				</td>
				<td>{{ $table->name ?? 'Atlet tidak ditemukan' }}</td>
				<td>{{ formatDate($table->use_date) }}</td>
				<td>{{ $table->code }}</td>
			</tr>
			@else
			<tr>
				<td colspan="4">tidak terpakai</td>
			</tr>
			@endif
			@endforeach
			@php
			$total_pembayaran += $table->iuran_harga ?? 0;
			@endphp
			@empty
			<tr>
				<td colspan="4" class="text-center">Tidak ada data pembayaran</td>
			</tr>
			@endforelse
		</tbody>
	</table>
</div>

<table class="footer">
	<tr>
		<td colspan="2" class="print-date">{{ date('d F Y') }}</td>
	</tr>
	<tr>
		<td colspan="2" class="print-person">{{ auth()->user()->name ?? '' }}</td>
	</tr>
</table>