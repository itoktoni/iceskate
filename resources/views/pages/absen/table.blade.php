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
                                    <th>Jadwal ID</th>
                                    <th>Member</th>
                                    <th>Jadwal</th>
                                    <th>Pembayaran</th>
                                    <th>Code Billing</th>
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
                                            <x-crud :model="$table" :action="['blank']">
                                                <x-button module="getDelete" key="{{ $table->field_primary }}" color="danger" label="Hapus"/>
                                                <x-button module="getUpdate" key="{{ $table->field_primary }}" color="primary" label="Bayar"/>
                                            </x-crud>
                                        </td>

										<td class="column-action">{{ $table->payment_id }}</td>
										<td >{{ $table->name }}</td>
										<td >{{ $table->jadwal_nama.' - '.$table->jadwal_tanggal }}</td>
										<td class="column-action text-center">
                                            <button class="btn btn-{{ empty($table->payment) ? 'warning' : 'success' }}">
                                                {{ empty($table->payment) ? 'Pending' : 'Paid' }}
                                            </button>
                                        </td>
										<td >{{ $table->code }}</td>

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
