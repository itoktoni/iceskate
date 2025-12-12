<x-layout>

    <x-card class="table-container">

        <div class="col-md-12">

            <x-form method="GET" x-init="" x-target="table" role="search" aria-label="Contacts"
                autocomplete="off" action="{{ moduleRoute('getTable') }}">
                <x-filter toggle="Filter" :fields="$fields" />
            </x-form>

            <x-form method="POST" action="{{ moduleRoute('getTable') }}">

                <x-action />

                <div class="container-fluid" id="table">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="9" class="center">
                                        <input class="btn-check-d" type="checkbox">
                                    </th>
                                    <th class="text-center col-md-2">{{ __('Action') }}</th>
                                    @foreach ($fields as $value)
                                        <th {{ Template::extractColumn($value) }}>
                                            {{ __($value->name) }}
                                        </th>
                                    @endforeach
                                    <th>Category</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $table)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox" name="code[]"
                                                value="{{ $table->field_primary }}">
                                        </td>
                                        <td class=" text-center">
                                            <x-crud :model="$table">
                                                <x-button module="getRace" key="{{ $table->field_primary }}" color="success" label="Performance"/>
                                            </x-crud>
                                        </td>

										<td >{{ $table->jadwal_id }}</td>
										<td >{{ $table->jadwal_nama }}</td>
										<td >{{ $table->category_nama }}</td>
										<td >{{ $table->jadwal_tanggal }}</td>
										<td >{{ $table->jadwal_keterangan }}</td>

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
