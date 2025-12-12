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

                <x-form-select col="6" name="jadwal_category_id" label="Category" :options="$category" />
                <x-form-input col="3" type="date" value="{{ $model->jadwal_tanggal ?? date('Y-m-d') }}" name="jadwal_tanggal" />
                <x-form-select col="3" name="jadwal_nama" label="Type" :options="$jadwal" />
                <x-form-textarea col="12" name="jadwal_keterangan" />

                @if ($model)

                 <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th class="text-center column-action">Score</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($user as $table)
                                    <tr>
										<td data-label="Nama">{{ $table->name }}</td>
										<td data-label="Score" class="text-center">
                                            <input name="code[{{ $table->race_user_id }}][id]" type="hidden" value="{{ $table->race_id }}"/>
                                            <input name="code[{{ $table->race_user_id }}][score]" type="number" value="{{ $table->race_waktu }}" step="any" class="form-control" />
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
