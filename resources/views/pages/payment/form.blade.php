<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

            <div class="row">
                @bind($model)

                <x-form-input col="3" type="date" name="payment_tanggal" />
                <x-form-select col="4" name="payment_iuran" label="Iuran" :options="$iuran" />
                <x-form-select col="5" class="search" name="payment_id_user" label="Atlet" :options="$user" />

                @if ($model && $model->payment_id)
                    <x-form-select col="3" name="payment_paid" label="Status bayar" :options="$status_bayar" />
                    <x-form-select col="4" name="payment_method" label="Metode" :options="$method_options" />
                    <x-form-input col="5" name="payment_done" label="Waktu lunas" readonly />

                    <x-form-textarea col="12" rows="3" name="payment_note" label="Keterangan" />

                    <div class="col-md-12 mt-2">
                        <div class="alert alert-info py-2">
                            Mengubah <b>Status bayar</b> wajib disertai perubahan <b>Keterangan</b>
                            (mis. "ternyata belum terima uang" / "sudah bayar cash di kasir").
                            @if(! empty($model->payment_settle_by))
                                Terakhir diubah oleh <b>{{ $model->payment_settle_by }}</b>.
                            @endif
                            Untuk settle cepat pakai
                            @if($model->payment_paid != 1)
                                <a href="{{ moduleRoute('getSettle', ['code' => $model->payment_id]) }}" class="alert-link">form Settle Cash / Manual</a>.
                            @else
                                <a href="{{ moduleRoute('getPending', ['code' => $model->payment_id]) }}" class="alert-link">form Pembatalan</a>.
                            @endif
                        </div>
                    </div>
                @endif

                @if ($model && isset($jadwal))

                 <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Jadwal</th>
                                    <th>Tgl Jadwal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($jadwal as $table)
                                    <tr>
										<td data-label="Nama">{{ $table->jadwal_nama }}</td>
										<td data-label="Kehadiran">{{ $table->jadwal_tanggal }}</td>
										<td data-label="Voucher">{{ $table->jadwal_keterangan }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                @endif

                @endbind
            </div>

        </x-card>
    </x-form>
</x-layout>
