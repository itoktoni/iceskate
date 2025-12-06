<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form">
                @if ($absen->count() > 0)
                <input type="hidden" name="jadwal_id" value="{{ $model->field_primary }}" />
                <x-button type="submit" name="race" value="race" color="danger" label="Race" />
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
                                    <th width="9" class="center">
                                        <input class="btn-check-d" type="checkbox">
                                    </th>
                                    <th>Nama</th>
                                    <th class="text-center column-action">Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($user as $table)
                                    @php
                                        $selected = $absen->where('id', $table->field_primary)->count()  > 0 ? 'checked' : null ;
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox" {{ $selected }} name="code[]"
                                                value="{{ $table->field_primary }}">
                                        </td>

										<td data-label="Nama">{{ $table->field_name }}</td>
										<td data-label="Nama" class="text-center">{{ empty($selected) ? 'Absen' : 'Hadir' }}</td>
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
