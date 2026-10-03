<x-layout>
    @php
        $isPending = ($mode ?? 'settle') === 'pending';
        $postAction = $isPending
            ? moduleRoute('postPending', ['code' => $model->payment_id ?? $model->field_primary])
            : moduleRoute('postSettle', ['code' => $model->payment_id ?? $model->field_primary]);
    @endphp

    <x-form :model="$model" action="{{ $postAction }}">
        <x-card>
            <x-action form="form">
                <x-button module="getTable" color="secondary" label="Kembali" />
            </x-action>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th width="180">Code</th>
                            <td>{{ $model->payment_id }}</td>
                        </tr>
                        <tr>
                            <th>Atlet</th>
                            <td>{{ $model->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Voucher</th>
                            <td>{{ $model->iuran_nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td>Rp {{ number_format($model->payment_value ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Status saat ini</th>
                            <td>
                                <span class="badge bg-{{ ($model->payment_paid ?? 0) == 1 ? 'success' : 'warning' }}">
                                    {{ ($model->payment_paid ?? 0) == 1 ? 'Paid' : 'Pending' }}
                                </span>
                                @if(! empty($model->payment_method))
                                    <small>({{ $model->payment_method }})</small>
                                @endif
                            </td>
                        </tr>
                        @if(! empty($model->payment_note))
                        <tr>
                            <th>Catatan sebelumnya</th>
                            <td>
                                {{ $model->payment_note }}
                                @if(! empty($model->payment_settle_by))
                                    <br><small class="text-muted">oleh {{ $model->payment_settle_by }}</small>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>

                @if(! $isPending)
                    <x-form-select col="4" required name="method" label="Metode manual" :options="$methods" :default="old('method', 'CASH')" />
                    <div class="col-md-8">
                        <div class="alert alert-info py-2">
                            Pilih <b>CASH</b> jika atlet bayar tunai di kasir, <b>MANUAL</b> untuk koreksi admin, <b>TRANSFER</b> untuk transfer bank manual.
                        </div>
                    </div>
                @else
                    <div class="col-md-12">
                        <div class="alert alert-warning py-2">
                            Pembayaran ini <b>sudah lunas</b> ({{ $model->payment_method ?? '-' }}).
                            Tulis alasan kenapa dikembalikan ke <b>pending</b> (mis. salah input, double bayar, belum terima uang).
                        </div>
                    </div>
                @endif

                <x-form-textarea col="12" required rows="4" name="note" label="{{ $isPending ? 'Alasan pembatalan (wajib)' : 'Keterangan kenapa manual (wajib)' }}" placeholder="cth: bayar cash Rp 150.000 di kasir tgl 3/10, diterima oleh ..." />

                @if ($errors->any())
                    <div class="col-md-12">
                        <div class="alert alert-danger py-2">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </x-card>
    </x-form>
</x-layout>
