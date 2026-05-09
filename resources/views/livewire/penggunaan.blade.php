<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-search"></i> Cari Data Penggunaan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="penggunaan_id" class="fw-bold">Masukkan Kode/Barcode Penggunaan</label>
                                <div class="input-group input-group-lg">
                                    <input
                                        wire:model="penggunaan_id"
                                        wire:keydown.enter="search"
                                        type="text"
                                        autofocus
                                        class="form-control form-control-lg @error('penggunaan_id') is-invalid @enderror"
                                        id="penggunaan_id"
                                        placeholder="Ketik kode penggunaan dan tekan Enter..."
                                        autocomplete="off"
                                    >
                                    <button type="button" wire:click="search" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                                @error('penggunaan_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            @if($searchResult)
                                <button type="button" wire:click="resetForm" class="btn btn-outline-secondary btn-lg w-100">
                                    <i class="fas fa-redo"></i> Cari Lagi
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($error)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fas fa-exclamation-circle"></i> <strong>Error:</strong> {{ $error }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($searchResult)
        <div class="row">
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle"></i> Data Ditemukan - Silakan Edit atau Simpan Perubahan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-info-circle"></i> Detail Penggunaan</h6>
                                <table class="table table-sm table-hover">
                                    <tbody>
                                        <tr>
                                            <th width="40%" class="bg-light">Kode Penggunaan</th>
                                            <td><strong>{{ $searchResult->penggunaan_code }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th width="40%" class="bg-light">Tanggal</th>
                                            <td>{{ $searchResult->penggunaan_tanggal }}</td>
                                        </tr>
                                        <tr>
                                            <th width="40%" class="bg-light">ID Iuran</th>
                                            <td>{{ $searchResult->penggunaan_id_iuran }}</td>
                                        </tr>
                                        <tr>
                                            <th width="40%" class="bg-light">ID Jadwal</th>
                                            <td>{{ $searchResult->penggunaan_id_jadwal }}</td>
                                        </tr>
                                        <tr>
                                            <th width="40%" class="bg-light">Dibuat Pada</th>
                                            <td>{{ $searchResult->penggunaan_created_at }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-cogs"></i> Form Input</h6>
                                <p class="text-muted small">Form di atas telah diisi otomatis. Anda dapat mengedit data dan menyimpannya.</p>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Data telah dimuat ke form. Lakukan perubahan jika diperlukan lalu klik Simpan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!$searchResult && !$error)
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Tips:</strong> Masukkan kode penggunaan di atas untuk mencari data. Tekan <kbd>Enter</kbd> atau klik tombol <strong>Cari</strong> untuk memulai pencarian.
                </div>
            </div>
        </div>
    @endif
</div>
