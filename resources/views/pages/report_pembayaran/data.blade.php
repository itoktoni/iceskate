<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="8">
			<h3>
				<b>Report Data Pembayaran</b>
			</h3>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				laporan data pembayaran berdasarkan tanggal
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
				<th width="1">No. </th>
				<th>CODE PEMBAYARAN</th>
				<th>NAMA PEMBAYARAN</th>
				<th>TANGGAL PEMBAYARAN</th>
				<th>METODE PEMBAYARAN</th>
				<th>NAMA USER</th>
				<th>STATUS</th>
				<th>JUMLAH</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_pembayaran = 0;
			@endphp

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->payment_code }}</td>
				<td>{{ $table->iuran_nama }} -
					@if($table->iuran_type == 'BULANAN')
						{{ Carbon\Carbon::parse($table->iuran_tanggal)->translatedFormat('F Y') }}
					@else
						{{ Carbon\Carbon::parse($table->iuran_tanggal)->translatedFormat('d M Y') }}
					@endif
				</td>
				<td>{{ $table->payment_done }}</td>
				<td>{{ $table->payment_method }}</td>
				<td>{{ $table->name ?? 'User tidak ditemukan' }}</td>
				<td>
					@if($table->payment_paid == 1)
						<span class="badge bg-success">PAID</span>
					@else
						<span class="badge bg-danger">UNPAID</span>
					@endif
				</td>
				<td class="text-right">{{ number_format($table->iuran_harga ?? 0, 0, ',', '.') }}</td>
			</tr>
			@php
			$total_pembayaran += $table->iuran_harga ?? 0;
			@endphp
			@empty
			<tr>
				<td colspan="7" class="text-center">Tidak ada data pembayaran</td>
			</tr>
			@endforelse

			<tr>
				<td colspan="6" class="text-right"><b>Total Pembayaran</b></td>
				<td class="text-right"><b>{{ number_format($total_pembayaran, 0, ',', '.') }}</b></td>
			</tr>

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