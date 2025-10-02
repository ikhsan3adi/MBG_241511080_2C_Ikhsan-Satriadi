<?php

namespace App\Controllers\Api\Admin;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseApiController;
use App\Models\BahanBakuModel;
use App\Models\PermintaanDetailModel;
use App\Models\PermintaanModel;

class PermintaanController extends BaseApiController
{
    protected PermintaanModel $permintaanModel;
    protected PermintaanDetailModel $permintaanDetailModel;
    protected BahanBakuModel $bahanBakuModel;

    public function __construct()
    {
        parent::__construct();
        $this->permintaanModel = new PermintaanModel();
        $this->permintaanDetailModel = new PermintaanDetailModel();
        $this->bahanBakuModel = new BahanBakuModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?? null;
        $search = $this->request->getGet('search') ?? null;

        $permintaan = $this->permintaanModel
            ->withPemohon()
            ->status($status)
            ->search($search)
            ->findAll();

        return $this->respond(
            status: 200,
            data: [
                'error' => false,
                'message' => 'Daftar permintaan bahan baku',
                'search' => $search,
                'status' => $status,
                'data' => [
                    'permintaan' => $permintaan
                ]
            ]
        );
    }

    public function show($id = null)
    {
        $permintaan = $this->permintaanModel
            ->withPemohon()
            ->asArray()
            ->find($id);

        if (!$permintaan) {
            return $this->respond(
                status: 404,
                data: [
                    'error' => true,
                    'message' => 'Permintaan tidak ditemukan.'
                ]
            );
        }

        $details = $this->permintaanDetailModel->getAllDetailByPermintaanId($id);

        $permintaan['detail_bahan'] = $details;

        return $this->respond(
            status: 200,
            data: [
                'error' => false,
                'message' => 'Detail permintaan bahan baku',
                'data' => [
                    'permintaan' => $permintaan
                ]
            ]
        );
    }

    public function approve($id = null)
    {
        $permintaan = $this->permintaanModel
            ->withPemohon()
            ->asArray()
            ->find($id);

        if (!$permintaan) {
            return $this->respond(
                status: 404,
                data: [
                    'error' => true,
                    'message' => 'Permintaan tidak ditemukan.'
                ]
            );
        }

        if ($permintaan['status'] === 'disetujui') {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'message' => 'Permintaan sudah disetujui.'
                ]
            );
        }

        if ($permintaan['status'] === 'ditolak') {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'message' => 'Permintaan sudah ditolak, tidak bisa disetujui.'
                ]
            );
        }

        $details = $this->permintaanDetailModel->getAllDetailByPermintaanId($id);

        try {
            $dataJumlahBaru = [];
            //! Cek dan update stok bahan baku
            foreach ($details as $detail) {
                $bahanId = $detail['bahan_id'];
                $status = $detail['status'];
                $jumlah = $detail['jumlah'];
                $jumlahDiminta = $detail['jumlah_diminta'];

                //! Cek apakah stok bahan baku mencukupi
                if ($status === 'habis' || $jumlah <= 0 || $jumlah < $jumlahDiminta) {
                    return $this->respond(
                        status: 400,
                        data: [
                            'error' => true,
                            'message' => "Stok bahan baku '{$detail['nama']}' tidak mencukupi.",
                        ]
                    );
                }

                //! Cek apakah bahan baku sudah kadaluarsa
                $today = date('Y-m-d');
                $diff = (strtotime($detail['tanggal_kadaluarsa']) - strtotime($today)) / (60 * 60 * 24);
                if ($diff <= 0) {
                    return $this->respond(
                        status: 400,
                        data: [
                            'error' => true,
                            'message' => "Bahan baku '{$detail['nama']}' sudah kadaluarsa.",
                        ]
                    );
                }

                $jumlahBaru = $jumlah - $jumlahDiminta;
                $dataJumlahBaru[$bahanId] = $jumlahBaru;
            }

            //! Jika semua bahan baku mencukupi, baru update stok
            foreach ($dataJumlahBaru as $bahanId => $jumlahBaru) {
                $this->bahanBakuModel->updateJumlah($bahanId, $jumlahBaru);
            }

            $this->permintaanModel->approvePermintaan($id);

            return $this->respond(
                status: 200,
                data: [
                    'error' => false,
                    'message' => 'Permintaan bahan baku disetujui.',
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Error approving permintaan: ' . $e->getMessage());
            return $this->respond(
                status: 500,
                data: [
                    'error' => true,
                    'message' => 'Terjadi kesalahan saat menyetujui permintaan.',
                ]
            );
        }
    }

    public function reject($id = null)
    {
        $permintaan = $this->permintaanModel
            ->withPemohon()
            ->asArray()
            ->find($id);

        if (!$permintaan) {
            return $this->respond(
                status: 404,
                data: [
                    'error' => true,
                    'message' => 'Permintaan tidak ditemukan.'
                ]
            );
        }

        if ($permintaan['status'] === 'ditolak') {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'message' => 'Permintaan sudah ditolak.'
                ]
            );
        }

        if ($permintaan['status'] === 'disetujui') {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'message' => 'Permintaan sudah disetujui, tidak bisa ditolak.'
                ]
            );
        }

        try {
            $this->permintaanModel->update($id, ['status' => 'ditolak']);

            return $this->respond(
                status: 200,
                data: [
                    'error' => false,
                    'message' => 'Permintaan bahan baku ditolak.',
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Error rejecting permintaan: ' . $e->getMessage());
            return $this->respond(
                status: 500,
                data: [
                    'error' => true,
                    'message' => 'Terjadi kesalahan saat menolak permintaan.',
                ]
            );
        }
    }
}
