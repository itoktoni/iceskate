<x-layout>
    <x-form :model="$model" method="GET" target="_blank" action="{{ moduleRoute('getPrint') }}" :upload="true">
        <x-card>
            <x-action form="print" />

            @bind($model)
                <x-form-select col="6" name="asset_id" label="Nama Asset" :options="$asset" />

                <input type="hidden" name="queue" value="batch" />
            @endbind

        </x-card>
    </x-form>
</x-layout>
