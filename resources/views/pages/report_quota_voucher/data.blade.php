<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>LAPORAN ADMINISTRASI PEMAKAIAN VOUCHER</b>
			</h3>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				Daftar User Quota Voucher (Habis & Tersisa)
			</h3>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				Periode Laporan : {{ formatDate(request()->get('start_date')) }} - {{ formatDate(request()->get('end_date')) }}
			</h3>
		</td>
	</tr>
</table>

@php
	$habis = $data['habis'] ?? [];
	$tersisa = $data['tersisa'] ?? [];
	$semua = array_merge($habis, $tersisa);
@endphp

<h3>Rekapitulasi Quota Voucher User</h3>
<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
			<tr>
				<th width="1">No.</th>
				<th>Nama User</th>
				<th>Kode &amp; Jenis Voucher</th>
				<th>Kuota</th>
				<th>Terpakai</th>
				<th>Sisa</th>
			</tr>
		</thead>
		<tbody>
			@forelse($semua as $row)
				@foreach($row['vouchers'] as $v)
				<tr>
					@if($loop->first)
					<td rowspan="{{ count($row['vouchers']) + 1 }}">{{ $loop->parent->iteration }}</td>
					<td rowspan="{{ count($row['vouchers']) + 1 }}"><b>{{ $row['name'] }}</b></td>
					@endif
					<td>
						<b>{{ $v['payment_id'] }}</b><br />
						{{ $v['iuran_nama'] ?? '-' }}
					</td>
					<td style="text-align:center">{{ $v['kuota'] }}</td>
					<td style="text-align:center">{{ $v['terpakai'] }}</td>
					<td style="text-align:center">{{ $v['sisa'] }}</td>
				</tr>
				@endforeach
				<tr>
					<td></td>
					<td style="text-align:center"><b>{{ $row['kuota'] }}</b></td>
					<td style="text-align:center"><b>{{ $row['terpakai'] }}</b></td>
					<td style="text-align:center"><b>{{ $row['sisa'] }}</b></td>
				</tr>
			@empty
			<tr>
				<td colspan="6" class="text-center">Tidak ada data voucher</td>
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
		<td colspan="2" class="print-person">Admin<br />{{ auth()->user()->name ?? '' }}</td>
	</tr>
</table>
