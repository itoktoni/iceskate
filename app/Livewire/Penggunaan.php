<?php

namespace App\Livewire;

use Livewire\Component;
use App\Dao\Models\Penggunaan as PenggunaanModel;

class Penggunaan extends Component
{
    // Search fields
    public $penggunaan_id = '';
    public $searchResult = null;
    public $error = '';

    // Form fields
    public $penggunaan_tanggal = '';
    public $penggunaan_id_iuran = '';
    public $penggunaan_id_jadwal = '';
    public $penggunaan_created_at = '';

    protected $rules = [
        'penggunaan_id' => 'required|string|max:50',
        'penggunaan_tanggal' => 'required|date',
        'penggunaan_id_iuran' => 'required|string|max:50',
        'penggunaan_id_jadwal' => 'required|string|max:50',
        'penggunaan_created_at' => 'required|date',
    ];

    public function search()
    {
        $this->validate([
            'penggunaan_id' => 'required|string|max:50',
        ]);

        // Search for the penggunaan record by code
        $this->searchResult = PenggunaanModel::where('penggunaan_code', $this->penggunaan_id)->first();

        if (!$this->searchResult) {
            $this->error = 'Data penggunaan tidak ditemukan!';
        } else {
            $this->error = '';
            // Populate form with search result data
            $this->penggunaan_tanggal = $this->searchResult->penggunaan_tanggal;
            $this->penggunaan_id_iuran = $this->searchResult->penggunaan_id_iuran;
            $this->penggunaan_id_jadwal = $this->searchResult->penggunaan_id_jadwal;
            $this->penggunaan_created_at = $this->searchResult->penggunaan_created_at;
        }
    }

    public function updatedPenggunaanId()
    {
        // Clear previous results when input changes
        $this->searchResult = null;
        $this->error = '';
    }

    public function save()
    {
        $this->validate();

        // Check if we're updating an existing record
        if ($this->searchResult) {
            // Update existing record
            $penggunaan = PenggunaanModel::find($this->searchResult->penggunaan_code);
            $penggunaan->update([
                'penggunaan_tanggal' => $this->penggunaan_tanggal,
                'penggunaan_id_iuran' => $this->penggunaan_id_iuran,
                'penggunaan_id_jadwal' => $this->penggunaan_id_jadwal,
                'penggunaan_created_at' => $this->penggunaan_created_at,
            ]);

            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Data penggunaan berhasil diperbarui!'
            ]);
        } else {
            // Create new record
            PenggunaanModel::create([
                'penggunaan_code' => $this->penggunaan_id ?: 'PGN' . time(),
                'penggunaan_tanggal' => $this->penggunaan_tanggal,
                'penggunaan_id_iuran' => $this->penggunaan_id_iuran,
                'penggunaan_id_jadwal' => $this->penggunaan_id_jadwal,
                'penggunaan_created_at' => $this->penggunaan_created_at,
            ]);

            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Data penggunaan berhasil disimpan!'
            ]);
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'penggunaan_id',
            'searchResult',
            'error',
            'penggunaan_tanggal',
            'penggunaan_id_iuran',
            'penggunaan_id_jadwal',
            'penggunaan_created_at'
        ]);
    }

    public function render()
    {
        return view('livewire.penggunaan');
    }
}