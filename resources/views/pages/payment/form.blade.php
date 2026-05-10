<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

            <div class="row">
                @bind($model)

                <x-form-input col="3" type="date" name="payment_tanggal" />
                <x-form-select col="4" name="payment_iuran" label="Iuran" :options="$iuran" />
                <x-form-select col="5" class="search" name="payment_id_user" label="Atlet" :options="$user" />

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
