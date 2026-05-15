<div class="mb-3">

    <div class="card-body">

        <div class="form-group">
            <label for="scan" class="fw-bold mb-2">Masukkan Voucher QR Code</label>
            <div class="input-group input-group-lg">
                <input wire:model="scan" wire:keydown.enter="search" type="text" autofocus
                    class="form-control form-control-lg @error('scan') is-invalid @enderror" id="scan"
                    placeholder="Ketik kode penggunaan dan tekan Enter..." autocomplete="off">
                <button type="button" wire:click="search" class="btn btn-primary btn-lg">
                    Scan
                </button>
            </div>

        </div>

         <!-- Perbaikan pada bagian Error Alert -->
        @if (!empty($error))
            <div class="alert alert-danger alert-dismissible mb-0 mt-3" role="alert">
                <i id="alert" class="fas fa-exclamation-circle"></i> <strong>Error:</strong> {{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Perbaikan pada bagian Success Alert -->
        @if (!empty($result))
            <div class="alert alert-success alert-dismissible mb-0 mt-3">
                <i id="alert" class="fas fa-check-circle"></i> <strong>Success :</strong> {{ $result }}
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

    </div>
</div>
