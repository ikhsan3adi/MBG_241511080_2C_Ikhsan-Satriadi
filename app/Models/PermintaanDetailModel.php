<?php

namespace App\Models;

use CodeIgniter\Model;

class PermintaanDetailModel extends Model
{
    protected $table            = 'permintaan_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'permintaan_id',
        'bahan_id',
        'jumlah_diminta'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    public function getAllPermintaanByBahanBakuId($bahanId)
    {
        return $this->select('permintaan.*, permintaan_detail.jumlah_diminta, user.name as pemohon')
            ->join('permintaan', 'permintaan.id = permintaan_detail.permintaan_id', 'left')
            ->join('user', 'user.id = permintaan.pemohon_id', 'left')
            ->where('bahan_id', $bahanId)
            ->orderBy('permintaan.created_at', 'DESC')
            ->asArray()->findAll();
    }

    public function getAllDetailByPermintaanId($permintaanId)
    {
        return $this->select('permintaan_detail.*, bahan_baku.*')
            ->join('bahan_baku', 'bahan_baku.id = permintaan_detail.bahan_id', 'left')
            ->where('permintaan_id', $permintaanId)
            ->asArray()->findAll();
    }
}
