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
				<th>JADWAL</th>
				<th>TANGGAL</th>
				<th>NAMA USER</th>
				<th>JUMLAH</th>
				<th>STATUS</th>
				<th>TANGGAL APPROVE</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_pembayaran = 0;
			@endphp

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->code }}</td>
				<td>{{ $table->jadwal_nama }}</td>
				<td>{{ $table->jadwal_tanggal }}</td>
				<td>{{ $table->name ?? 'User tidak ditemukan' }}</td>
				<td>Rp {{ number_format($table->amount ?? 0, 0, ',', '.') }}</td>
				<td>
					@if($table->payment == 1)
						<span class="badge bg-success">PAID</span>
					@else
						<span class="badge bg-danger">UNPAID</span>
					@endif
				</td>
				<td>{{ formatDate($table->tanggal) }}</td>
			</tr>
			@php
			$total_pembayaran += $table->amount ?? 0;
			@endphp
			@empty
			<tr>
				<td colspan="7" class="text-center">Tidak ada data pembayaran</td>
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