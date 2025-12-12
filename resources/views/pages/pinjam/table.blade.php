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
                                    <th class="text-center column-action">{{ __('Action') }}</th>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Asset</th>
                                    <th>Peminjam</th>
                                    <th>Qty</th>
                                    <th class="text-center">Status</th>
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
                                            <x-crud :model="$table" :action="['delete']">
                                            </x-crud>
                                        </td>

										<td >{{ $table->pinjam_id }}</td>
										<td >{{ $table->pinjam_tanggal }}</td>
										<td >{{ $table->asset_nama }}</td>
										<td >{{ $table->name }}</td>
										<td >{{ $table->pinjam_qty }}</td>
										<td class="column-action text-center">
                                            <button type="button" class="btn btn-{{ $table->pinjam_qty == 0 ? 'success' : 'warning' }}">{{ $table->pinjam_qty == 0 ? 'SELESAI' : 'PINJAM' }}</button>
                                        </td>

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
