<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanFaktur extends Model
{
    use HasFactory;

    protected $table = 'barang_jasa';

    protected $fillable = [
        'baris',
        'jenis_barang_jasa',
        'kode_barang_jasa',
        'nama_barang_jasa',
        'nama_satuan_ukur',
        'harga_satuan',
        'jumlah_barang_jasa',
        'total_diskon',
        'dpp',
        'dpp_nilai_lain',
        'tarif_ppn',
        'ppn',
        'tarif_ppnbm',
        'ppnbm',
        'dokumen_id'
    ];

    protected $casts = [
        'baris' => 'integer',
        'harga_satuan' => 'decimal:2',
        'jumlah_barang_jasa' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'dpp' => 'decimal:2',
        'dpp_nilai_lain' => 'decimal:2',
        'tarif_ppn' => 'decimal:2',
        'ppn' => 'decimal:2',
        'tarif_ppnbm' => 'decimal:2',
        'ppnbm' => 'decimal:2',
        'dokumen_id' => 'integer'
    ];

    // Validasi jenis barang/jasa
    public function getJenisBarangJasaTextAttribute()
    {
        return $this->jenis_barang_jasa === 'B' ? 'Barang' : 'Jasa';
    }

    // Scope untuk filter berdasarkan jenis
    public function scopeBarang($query)
    {
        return $query->where('jenis_barang_jasa', 'B');
    }

    public function scopeJasa($query)
    {
        return $query->where('jenis_barang_jasa', 'J');
    }

    // Scope untuk filter berdasarkan dokumen
    public function scopeByDokumen($query, $dokumenId)
    {
        return $query->where('dokumen_id', $dokumenId);
    }

    // Relationship jika ada model Dokumen
    // public function dokumen()
    // {
    //     return $this->belongsTo(Dokumen::class, 'dokumen_id');
    // }

    // Accessor untuk format mata uang
    public function getFormattedHargaSatuanAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan, 2, ',', '.');
    }

    public function getFormattedDppAttribute()
    {
        return 'Rp ' . number_format($this->dpp, 2, ',', '.');
    }

    public function getFormattedPpnAttribute()
    {
        return 'Rp ' . number_format($this->ppn, 2, ',', '.');
    }

    // Method untuk kalkulasi otomatis
    public function calculateDpp()
    {
        $subtotal = $this->harga_satuan * $this->jumlah_barang_jasa;
        return $subtotal - $this->total_diskon;
    }

    public function calculatePpn()
    {
        return $this->dpp * ($this->tarif_ppn / 100);
    }

    public function calculatePpnbm()
    {
        return $this->dpp * ($this->tarif_ppnbm / 100);
    }

    public function calculateTotal()
    {
        return $this->dpp + $this->ppn + $this->ppnbm;
    }

    // Boot method untuk auto-calculate sebelum save
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto calculate DPP jika belum diset
            if (empty($model->dpp)) {
                $model->dpp = $model->calculateDpp();
            }

            // Auto calculate PPN jika belum diset
            if (empty($model->ppn) && $model->tarif_ppn > 0) {
                $model->ppn = $model->calculatePpn();
            }

            // Auto calculate PPnBM jika belum diset
            if (empty($model->ppnbm) && $model->tarif_ppnbm > 0) {
                $model->ppnbm = $model->calculatePpnbm();
            }
        });
    }
}