<?php

namespace App\Controllers\Api\User;

use App\Controllers\BaseApiController;
use App\Models\BahanBakuModel;
use App\Models\PermintaanDetailModel;
use App\Models\PermintaanModel;
use CodeIgniter\HTTP\ResponseInterface;

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

        $currentUser = currentUser();

        $permintaan = $this->permintaanModel
            ->withPemohon()
            ->status($status)
            ->search($search)
            ->where('pemohon_id', $currentUser['id'])
            ->findAll();

        return $this->respond(
            status: 200,
            data: [
                'error' => false,
                'message' => 'Daftar permintaan bahan baku',
                'search' => $search,
                'status' => $status,
                'data' => [
                    'permintaan' => $permintaan,
                    'pemohon' => $currentUser
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

        $currentUser = currentUser();
        if ($permintaan['pemohon_id'] != $currentUser['id']) {
            return $this->respond(
                status: 403,
                data: [
                    'error' => true,
                    'message' => 'Anda tidak memiliki akses ke permintaan ini.'
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

    public function create()
    {
        $data = $this->request->getJSON(true) ?? [];

        if (
            !$this->validator->setRules($this->getCreateValidationRules())->run($data) ||
            !isset($data['detail_bahan']) ||
            !is_array($data['detail_bahan']) ||
            count($data['detail_bahan']) < 1
        ) {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'messages' => array_values($this->validator->getErrors())
                ]
            );
        }

        $currentUser = currentUser();

        $dataPermintaan = [
            'pemohon_id' => $currentUser['id'],
            'tgl_masak' => $data['tgl_masak'],
            'menu_makan' => $data['menu_makan'],
            'jumlah_porsi' => $data['jumlah_porsi'],
            'status' => 'menunggu',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        //! Cek tanggal masak harus eksak H-1, tidak boleh hari ini atau H-2 dst
        $today = date('Y-m-d');
        $diff = (strtotime($data['tgl_masak']) - strtotime($today)) / (60 * 60 * 24);
        if ($diff < 1 || $diff > 1) {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'message' => 'Tanggal masak harus minimal H-1 dari hari ini.',
                ]
            );
        }

        try {
            //! Cek setiap bahan apakah stoknya cukup dan belum kadaluarsa
            foreach ($data['detail_bahan'] as $bahan) {
                $bahanId = $bahan['bahan_id'];
                $jumlahDiminta = $bahan['jumlah_diminta'];
                $bahan = $this->bahanBakuModel->asArray()->find($bahanId);

                if (!$bahan) {
                    return $this->respond(
                        status: 400,
                        data: [
                            'error' => true,
                            'message' => "Bahan baku dengan ID {$bahanId} tidak ditemukan.",
                        ]
                    );
                }

                //! Cek apakah stok bahan baku mencukupi
                if ($bahan['status'] === 'habis' || $bahan['jumlah'] <= 0 || $bahan['jumlah'] < $jumlahDiminta) {
                    return $this->respond(
                        status: 400,
                        data: [
                            'error' => true,
                            'message' => "Stok bahan baku '{$bahan['nama']}' tidak mencukupi.",
                        ]
                    );
                }

                //! Cek apakah bahan baku sudah kadaluarsa
                $today = date('Y-m-d');
                $diff = (strtotime($bahan['tanggal_kadaluarsa']) - strtotime($today)) / (60 * 60 * 24);
                if ($diff <= 0) {
                    return $this->respond(
                        status: 400,
                        data: [
                            'error' => true,
                            'message' => "Bahan baku '{$bahan['nama']}' sudah kadaluarsa.",
                        ]
                    );
                }
            }

            $id = $this->permintaanModel->insert($dataPermintaan);

            $this->permintaanDetailModel->insertBatch(array_map(
                fn($bahan) => [
                    'permintaan_id' => $id,
                    'bahan_id' => $bahan['bahan_id'],
                    'jumlah_diminta' => $bahan['jumlah_diminta']
                ],
                $data['detail_bahan']
            ));

            return $this->respond(
                status: 201,
                data: [
                    'error' => false,
                    'message' => 'Permintaan bahan baku berhasil dibuat.',
                    'data' => [
                        'permintaan' => $this->permintaanModel->find($id)
                    ]
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Error creating permintaan: ' . $e->getMessage());
            return $this->respond(
                status: 500,
                data: [
                    'error' => true,
                    'message' => 'Terjadi kesalahan saat membuat permintaan.',
                ]
            );
        }
    }

    protected function getCreateValidationRules(...$args): array
    {
        return [
            'tgl_masak' => 'required|valid_date[Y-m-d]',
            'menu_makan' => 'required|string|max_length[255]',
            'jumlah_porsi' => 'required|integer|greater_than[0]',
            'detail_bahan.*.bahan_id' => 'required',
            'detail_bahan.*.jumlah_diminta' => 'required|integer|greater_than[0]',
        ];
    }
}
