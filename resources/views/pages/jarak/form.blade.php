<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

            <div class="row">
                @bind($model)

                <x-form-input col="6" label="Code" name="jarak_id" />
                <x-form-input col="6" name="jarak_nama" />
                <x-form-input col="6" name="jarak_australia" />
                <x-form-input col="6" name="jarak_asian" />

                @endbind
            </div>

        </x-card>
    </x-form>
</x-layout>
