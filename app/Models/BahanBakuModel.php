<?php

namespace App\Models;

use CodeIgniter\Model;

class BahanBakuModel extends Model
{
    protected $table            = 'bahan_baku';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
        'kategori',
        'jumlah',
        'satuan',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'status',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    public function category(?string $kategori)
    {
        if (is_null($kategori)) return $this;

        return $this->where('kategori', $kategori);
    }

    public function status(?string $status)
    {
        if (is_null($status)) return $this;

        return $this->where('status', $status);
    }

    public function withPermintaanCount()
    {
        return $this->select('bahan_baku.*, COUNT(permintaan_detail.id) as permintaan_count')
            ->join('permintaan_detail', 'bahan_baku.id = permintaan_detail.bahan_id', 'left')
            ->groupBy('bahan_baku.id');
    }

    public function search(?string $keyword)
    {
        if (is_null($keyword)) return $this;

        return $this->groupStart()
            ->like('nama', $keyword)
            ->orLike('kategori', $keyword)
            ->orLike('satuan', $keyword)
            ->orLike('status', $keyword)
            ->groupEnd();
    }

    public function updateJumlah($id, $jumlah)
    {
        $status = 'tersedia';
        if ($jumlah <= 0) {
            $status = 'habis';
            $jumlah = 0;
        }

        return $this->update($id, [
            'jumlah' => $jumlah,
            'status' => $status
        ]);
    }
}
