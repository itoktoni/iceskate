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
                                    <th>code pembayaran</th>
                                    <th>Atlet</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
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
                                                <x-button module="getUpdate" key="{{ $table->field_primary }}" color="primary" label="Detail"/>
                                            </x-crud>
                                        </td>

										<td data-label="Code">{{ $table->payment_id }}</td>
										<td data-label="Atlet">{{ $table->name }}</td>
										<td data-label="Tanggal">{{ formatDate($table->payment_tanggal) }}</td>
										<td data-label="Total">{{ number_format($table->payment_value) }}</td>
										<td data-label="Status" class="column-action text-center">
                                            <span class="btn btn-{{ $table->payment_paid == 1 ? 'success' : 'warning' }}">
                                                {{ $table->payment_paid == 1 ? 'Paid' : 'Pending' }}
                                            </span>
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
