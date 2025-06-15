<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailFaktur extends Model
{
    protected $table = 'detail_faktur';
    
    protected $fillable = [
        'faktur_id',
        'baris',
        'barang_jasa',
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
        'ppnbm'
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'jumlah_barang_jasa' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'dpp' => 'decimal:2',
        'dpp_nilai_lain' => 'decimal:2',
        'tarif_ppn' => 'decimal:2',
        'ppn' => 'decimal:2',
        'tarif_ppnbm' => 'decimal:2',
        'ppnbm' => 'decimal:2'
    ];

    public function faktur(): BelongsTo
    {
        return $this->belongsTo(Faktur::class, 'faktur_id');
    }
} 