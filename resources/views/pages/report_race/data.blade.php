<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="8">
			<h3>
				<b>Report Data Performance</b>
			</h3>
		</td>
		<td rowspan="3">
			<x-logo/>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				laporan data Performance berdasarkan tanggal
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
				<th>TANGGAL</th>
				<th>NAMA USER</th>
				<th>JADWAL</th>
				<th>WAKTU</th>
				<th>CATATAN</th>
			</tr>
		</thead>
		<tbody>

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ formatDate($table->race_tanggal) }}</td>
				<td>{{ $table->name ?? 'User tidak ditemukan' }}</td>
				<td>{{ $table->jadwal_nama }}</td>
				<td>{{ number_format($table->race_waktu, 2) }}</td>
				<td>{{ $table->race_notes }}</td>
			</tr>

			@empty
			<tr>
				<td colspan="7" class="text-center">Tidak ada data peformance</td>
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