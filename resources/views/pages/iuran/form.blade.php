<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

            <div class="row">
                @bind($model)

                <x-form-input col="4" name="iuran_nama" />
                <x-form-input col="2" type="date" name="iuran_tanggal" />
                <x-form-input col="2" type="number" name="iuran_token" />
                <x-form-input type="number" col="2" name="iuran_harga" />
                <x-form-select col="2" name="iuran_type" label="Type" :options="$iuran" />
                <x-form-textarea col="12" name="iuran_keterangan" rows="7" label="Keterangan" />

                @endbind
            </div>

        </x-card>
    </x-form>
</x-layout>
