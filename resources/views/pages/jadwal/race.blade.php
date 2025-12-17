<x-layout>
    <x-form :model="$model" action="{{ moduleRoute('postRace', ['code' => $model->field_primary]) }}">
        <x-card>
            <x-action form="form">
                @if ($absen->count() > 0)
                <input type="hidden" name="jadwal_id" value="{{ $model->field_primary }}" />
                @endif
            </x-action>

            <div class="row">
                @bind($model)

                <x-form-select col="2" required name="jarak_id" label="Jarak" :options="$jarak" />
                <x-form-input col="2" type="date" value="{{ $model->jadwal_tanggal ?? date('Y-m-d') }}" name="jadwal_tanggal" />
                <x-form-select col="2" name="jadwal_nama" label="Type" :options="$jadwal" />
                <x-form-textarea col="6" name="jadwal_keterangan" />

                @if ($model)

                 <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th class="text-center column-action">Score</th>
                                    <th class="text-left">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($user as $table)
                                    <tr>
										<td data-label="Nama">{{ $table->name }}</td>
										<td data-label="Score" class="text-center">
                                            <input name="code[{{ $table->id }}][id]" type="hidden" value="{{ $table->id }}"/>
                                            <input name="code[{{ $table->id }}][score]" type="number" step="any" class="form-control" />
                                        </td>
                                        <td>
                                            <input name="code[{{ $table->id }}][notes]" type="text" class="form-control" />
                                        </td>
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
