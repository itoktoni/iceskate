<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

            <div class="row">
                @bind($model)

                <x-form-input col="4" name="asset_nama" />
                <x-form-input col="2" name="asset_qty" />
                <x-form-textarea col="6" name="asset_keterangan" />

                @endbind
            </div>

        </x-card>
    </x-form>
</x-layout>
