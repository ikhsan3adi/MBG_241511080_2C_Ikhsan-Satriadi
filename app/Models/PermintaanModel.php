<?php

namespace App\Models;

use CodeIgniter\Model;

class PermintaanModel extends Model
{
    protected $table            = 'permintaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pemohon_id',
        'tgl_masak',
        'menu_makan',
        'jumlah_porsi',
        'status',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    public function status(?string $status)
    {
        if (is_null($status)) return $this;

        return $this->where('status', $status);
    }

    public function withPemohon()
    {
        return $this->select('permintaan.*, user.name as pemohon')
            ->join('user', 'user.id = permintaan.pemohon_id');
    }

    public function search(?string $keyword, bool $withUser = true)
    {
        if (is_null($keyword)) return $this;

        $this->groupStart()
            ->like('menu_makan', $keyword)
            ->orLike('status', $keyword);

        if ($withUser) $this->orLike('user.name', $keyword);

        return $this->groupEnd();
    }

    public function approvePermintaan($id)
    {
        return $this->update($id, ['status' => 'disetujui']);
    }
}
