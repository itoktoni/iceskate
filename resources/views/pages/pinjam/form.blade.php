<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

            <div class="row">
                @bind($model)

                    <x-form-select col="6" required name="pinjam_asset_id" label="Asset" :options="$asset" />
                    <x-form-select col="6" required name="pinjam_user_id" label="User" :options="$user" />
                    <x-form-input col="6" name="pinjam_tanggal" label="Tanggal Pinjam" type="date" value="{{ $model->pinjam_tanggal ?? date('Y-m-d') }}" />

                    <br>

                    @if ($model)
                    <x-form-textarea col="12" name="pinjam_catatan" />
                    <x-form-input col="6"  value="{{ $model->pinjam_qty }}" name="pinjam" />
                    <x-form-input col="6" type="date" value="{{ $model->pinjam_kembali ?? date('Y-m-d') }}" label="Tgl Kembali" name="pinjam_tanggal" />
                    <x-form-input col="6" label="Qty Kembali" name="qty" />
                    @else
                    <x-form-input col="6" name="pinjam_qty" />
                    <x-form-textarea col="12" name="pinjam_catatan" />

                    @endif

                @endbind
            </div>

        </x-card>
    </x-form>
</x-layout>
