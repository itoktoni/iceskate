<x-layout>

    <x-card class="table-container">

        <div class="col-md-12">

            <x-form method="GET" x-init="" x-target="table" role="search" aria-label="Contacts"
                autocomplete="off" action="{{ moduleRoute('getTable') }}">

                 <div class="container-fluid filter-container mb-2">
                    <div class="row">

                        <x-form-input type="date" col="3" label="Start Date" name="start_date" />
                        <x-form-input type="date" col="3" label="End Date" name="end_date" />

                        <x-form-select col="3" name="jarak_id" label="Jarak" :options="$jarak" />
                        <x-form-select col="3" name="id" label="User" :options="$user" />

                    </div>
                </div>

                <x-filter toggle="Filter" :fields="$fields" />
            </x-form>

            <x-form method="POST" :upload="true" action="{{ moduleRoute('getTable') }}">

                <x-action>
                    <input type="file" name="file" accept=".xls,.xlsx" class="btn btn-primary btn-sm pb-2">
                    <x-button type="submit" label="Upload" class="btn-dark" name="upload" />
                </x-action>

                <div class="container-fluid" id="table">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="9" class="center">
                                        <input class="btn-check-d" type="checkbox">
                                    </th>
                                    <th class="text-center column-action">{{ __('Action') }}</th>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Jarak</th>
                                    <th>Tanggal</th>
                                    <th>Jadwal</th>
                                    <th>Waktu</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $table)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox" name="code[]"
                                                value="{{ $table->field_primary }}">
                                        </td>
                                        <td class="col-md-2 text-center column-action">
                                            <x-crud :model="$table" />
                                        </td>

										<td >{{ $table->id }}</td>
										<td >{{ $table->name }}</td>
										<td >{{ $table->jarak_nama }}</td>
										<td >{{ $table->race_tanggal }}</td>
										<td >{{ $table->jadwal_nama ?? 'TEST RACE' }}</td>
										<td >{{ $table->race_waktu }}</td>
										<td >{{ $table->race_notes }}</td>

                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <x-pagination :data="$data" />
                </div>

            </x-form>

        </div>

    </x-card>

</x-layout>
