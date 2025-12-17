<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="8">
			<h3>
				<b>Report Data Asset</b>
			</h3>
		</td>
		<td rowspan="3">
			<x-logo/>
		</td>
	</tr>

</table>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
			<tr>
				<th width="1">No. </th>
				<th>NAMA ASSET</th>
				<th>KETERANGAN</th>
				<th>QTY</th>
				<th>LIST PEMINJAMAN</th>
			</tr>
		</thead>
		<tbody>

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->name ?? 'Asset tidak ditemukan' }}</td>
				<td>{{ $table->asset_keterangan }}</td>
				<td class="text-center">{{ $table->asset_qty }}</td>
				<td>
						@foreach ($table->has_pinjam->where('pinjam_qty', '!=', 0) as $user)
						<span>
							{{ $loop->iteration }}.
							{{ $user->pinjam_tanggal ?? '' }} - {{ $user->has_user->name ?? '' }} = ( {{ $user->pinjam_qty ?? '' }} )
							<br>
						</span>
						@endforeach
				</td>
			</tr>

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