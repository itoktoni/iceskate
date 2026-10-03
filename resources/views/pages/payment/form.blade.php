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
                    <div class="col-md-12 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <th width="180">Status</th>
                                <td>
                                    <span class="badge bg-{{ $model->payment_paid == 1 ? 'success' : 'warning' }}">
                                        {{ $model->payment_paid == 1 ? 'Paid' : 'Pending' }}
                                    </span>
                                    @if(! empty($model->payment_method))
                                        @php
                                            $isManualDetail = in_array(strtoupper((string) $model->payment_method), ['CASH', 'MANUAL', 'TRANSFER']);
                                        @endphp
                                        <span class="badge bg-{{ $isManualDetail ? 'primary' : 'success' }}">
                                            {{ $isManualDetail ? 'Manual' : 'Cashi' }}
                                        </span>
                                        <small>({{ $model->payment_method }})</small>
                                    @endif
                                    @if(! empty($model->payment_done))
                                        <small class="text-muted">{{ $model->payment_done }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>
                                    {{ $model->payment_note ?? '-' }}
                                    @if(! empty($model->payment_settle_by))
                                        <br><small class="text-muted">oleh {{ $model->payment_settle_by }}</small>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        @if($model->payment_paid != 1)
                            <a href="{{ moduleRoute('getSettle', ['code' => $model->payment_id]) }}" class="btn btn-success">Settle Cash / Manual</a>
                        @else
                            <a href="{{ moduleRoute('getPending', ['code' => $model->payment_id]) }}" class="btn btn-warning">Kembalikan ke Pending</a>
                        @endif
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
