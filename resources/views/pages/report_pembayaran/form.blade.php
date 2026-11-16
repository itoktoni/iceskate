<x-layout>
    <x-form :model="$model" method="GET" target="_blank" action="{{ moduleRoute('getPrint') }}" :upload="true">
        <x-card>
            <x-action form="print" />

            @bind($model)
                <x-form-input col="6" type="date" label="Tanggal Awal" name="start_date" />
                <x-form-input col="6" type="date" label="Tanggal Akhir" name="end_date" />

                <x-form-select col="4" name="payment_iuran" label="Iuran" :options="$iuran" />
                <x-form-select col="4" name="payment_id_user" label="Member" :options="$user" />
                <x-form-select col="4" name="payment_paid" label="Paid" :options="['1' => 'PAID', '0' => 'UNPAID']" />

                <input type="hidden" name="queue" value="batch" />
            @endbind

        </x-card>
    </x-form>
</x-layout>
