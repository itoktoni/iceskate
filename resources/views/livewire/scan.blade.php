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

        @if (!empty($error) && ($error != 'Success Create Record !' || $error != 'Success Update Record !'))
            <div class="alert alert-danger mb-0">
                <i class="fas fa-exclamation-circle"></i> <strong>Error:</strong> {{ $error }}
            </div>
        @endif

        @if(!empty($error) && ($error != 'Success Create Record !' || $error != 'Success Update Record !'))
         <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle"></i> <strong>Success :</strong> {{ $error }}
            </div>
        @endif
    </div>
</div>
