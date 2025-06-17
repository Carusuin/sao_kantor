<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faktur extends Model
{
    protected $table = 'faktur';
    
    protected $fillable = [
        'tanggal_faktur',
        'jenis_faktur',
        'kode_transaksi',
        'referensi',
        'alamat_penjual',
        'id_tku_penjual',
        'npwp_penjual',
        'npwp_nik_pembeli',
        'jenis_id_pembeli',
        'negara_pembeli',
        'nomor_dokumen_pembeli',
        'nama_pembeli',
        'alamat_pembeli',
        'email_pembeli',
        'id_tku_pembeli',
        'uang_muka',
        'pelunasan',
        'nomor_faktur'
    ];

    protected $casts = [
        'tanggal_faktur' => 'date',
        'uang_muka' => 'boolean',
        'pelunasan' => 'boolean'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(DetailFaktur::class, 'faktur_id');
    }
} 