<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

            <div class="row">
                @bind($model)

                <x-form-input col="6" disabled label="Payment ID" name="payment_id" />
                <x-form-input col="6" disabled label="Payment Code" name="payment_code" />
                <x-form-input col="6" disabled label="User" value="{{ $user->name }}" name="payment_id_user" />
                <x-form-input col="2" disabled label="Tanggal" value="{{ formatDate($model->payment_tanggal) }}" name="payment_tanggal" />
                <x-form-input col="2" disabled label="Total" value="{{ number_format($model->payment_value, 0) }}" name="payment_value" />
                <x-form-input col="2" disabled label="Sudah Lunas ?" value="{{ $model->payment_paid == 1 ? 'Lunas' : 'Belum Lunas' }}" name="payment_paid" />
                <x-form-input col="6" disabled name="payment_method" />
                <div class="col-md-6">
                    <br>
                    <label for="">Payment Link</label>
                    <br>
                    <a href="http://{{ $model->payment_url }}">{{ $model->payment_url }}</a>
                </div>

                @endbind
            </div>

            <div class="row">
                <div class="col-md-12 mt-4">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Iuran</th>
                                <th>Harga</th>
                                <th>Token</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($iuran as $item)
                                <tr>
                                    <td>{{ $item->iuran_nama }}</td>
                                    <td>{{ number_format($item->pivot->iuran_harga, 0) }}</td>
                                    <td>{{ $item->pivot->token }}</td>
                                    <td>{{ formatDate($item->pivot->tanggal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

        </x-card>
    </x-form>
</x-layout>
