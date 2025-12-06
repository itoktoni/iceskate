<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

            <div class="row">
                @bind($model)

                <x-form-select col="6" name="race_jadwal_id" label="Jadwal" :options="$jadwal" />
                <x-form-select col="6" name="race_user_id" label="User" :options="$user" />
                <x-form-input col="3" name="race_tanggal" value="{{ $model->race_tanggal ?? date('Y-m-d') }}" type="date"/>
                <x-form-input col="3" name="race_waktu" step="any" type="number" />

                <x-form-textarea col="6" name="race_notes" />

                @endbind
            </div>

        </x-card>
    </x-form>
</x-layout>
