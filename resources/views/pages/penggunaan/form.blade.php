<x-layout>
    <x-form :model="$model">
        <x-card label="Form Penggunaan">
            <x-action form="form" />

            <livewire:penggunaan />

            <div class="row">
                @bind($model)

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="penggunaan_tanggal">Tanggal</label>
                        <input type="date"
                               class="form-control"
                               id="penggunaan_tanggal"
                               wire:model="penggunaan_tanggal">
                        @error('penggunaan_tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="penggunaan_id_iuran">ID Iuran</label>
                        <input type="text"
                               class="form-control"
                               id="penggunaan_id_iuran"
                               wire:model="penggunaan_id_iuran">
                        @error('penggunaan_id_iuran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="penggunaan_id_jadwal">ID Jadwal</label>
                        <input type="text"
                               class="form-control"
                               id="penggunaan_id_jadwal"
                               wire:model="penggunaan_id_jadwal">
                        @error('penggunaan_id_jadwal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="penggunaan_created_at">Dibuat Pada</label>
                        <input type="datetime-local"
                               class="form-control"
                               id="penggunaan_created_at"
                               wire:model="penggunaan_created_at">
                        @error('penggunaan_created_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-secondary" wire:click="resetForm">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </div>

                @endbind
            </div>
        </x-card>
    </x-form>
</x-layout>
