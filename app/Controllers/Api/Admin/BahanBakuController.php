<?php

namespace App\Controllers\Api\Admin;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BahanBakuModel;
use App\Controllers\BaseApiController;
use App\Models\PermintaanDetailModel;

class BahanBakuController extends BaseApiController
{
    protected BahanBakuModel $bahanBakuModel;
    protected PermintaanDetailModel $permintaanDetailModel;

    public function __construct()
    {
        parent::__construct();
        $this->bahanBakuModel = new BahanBakuModel();
        $this->permintaanDetailModel = new PermintaanDetailModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search') ?? null;
        $category = $this->request->getGet('category') ?? null;
        $status = $this->request->getGet('status') ?? null;

        $bahanBaku = $this->bahanBakuModel
            ->category($category)
            ->status($status)
            ->search($search)
            ->findAll();

        return $this->respond(
            status: 200,
            data: [
                'error' => false,
                'message' => 'Daftar bahan baku',
                'search' => $search,
                'category' => $category,
                'status' => $status,
                'data' => [
                    'bahan_baku' => $bahanBaku
                ]
            ]
        );
    }

    public function show($id = null)
    {
        $bahanBaku = $this->bahanBakuModel->asArray()->find($id);

        if (!$bahanBaku) {
            return $this->respond(
                status: 404,
                data: [
                    'error' => true,
                    'message' => 'Bahan baku tidak ditemukan.'
                ]
            );
        }

        $permintaan = $this->permintaanDetailModel->getAllPermintaanByBahanBakuId($id);
        $bahanBaku['riwayat_permintaan'] = $permintaan;

        return $this->respond(
            status: 200,
            data: [
                'error' => false,
                'message' => 'Detail bahan baku',
                'data' => [
                    'bahan_baku' => $bahanBaku
                ]
            ]
        );
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?? [];

        if (!$this->validator->setRules($this->getCreateValidationRules())->run($data)) {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'messages' => array_values($this->validator->getErrors())
                ]
            );
        }

        $data = [
            'nama' => $data['nama'],
            'kategori' => $data['kategori'],
            'jumlah' => $data['jumlah'],
            'satuan' => $data['satuan'],
            'tanggal_masuk' => $data['tanggal_masuk'],
            'tanggal_kadaluarsa' => $data['tanggal_kadaluarsa'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // status bahan baku berdasarkan aturan
        $today = date('Y-m-d');
        $diff = (strtotime($data['tanggal_kadaluarsa']) - strtotime($today)) / (60 * 60 * 24);
        if ($data['jumlah'] <= 0) {
            $data['status'] = 'habis';
        } elseif ($diff <= 0) {
            $data['status'] = 'kadaluarsa';
        } elseif ($diff <= 3) {
            $data['status'] = 'segera_kadaluarsa';
        } else {
            $data['status'] = 'tersedia';
        }

        try {
            $id = $this->bahanBakuModel->insert($data);

            return $this->respond(
                status: 201,
                data: [
                    'error' => false,
                    'message' => 'Bahan baku ditambahkan.',
                    'data' => [
                        'bahan_baku' => $this->bahanBakuModel->asArray()->find($id)
                    ]
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Error create bahan_baku: ' . $e->getMessage());
            return $this->respond(
                status: 500,
                data: [
                    'error' => true,
                    'message' => 'Gagal menambahkan bahan baku.'
                ]
            );
        }
    }

    public function update($id = null)
    {
        $bahanBaku = $this->bahanBakuModel->asArray()->find($id);
        if (!$bahanBaku) {
            return $this->respond(
                status: 404,
                data: [
                    'error' => true,
                    'message' => 'Bahan baku tidak ditemukan.'
                ]
            );
        }

        $data = $this->request->getJSON(true) ?? [];

        if (!$this->validator->setRules($this->getUpdateValidationRules($id))->run($data)) {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'messages' => array_values($this->validator->getErrors())
                ]
            );
        }

        $data = [
            'nama' => $data['nama'],
            'kategori' => $data['kategori'],
            'jumlah' => $data['jumlah'],
            'satuan' => $data['satuan'],
            'tanggal_masuk' => $data['tanggal_masuk'],
            'tanggal_kadaluarsa' => $data['tanggal_kadaluarsa'],
        ];

        // status bahan baku berdasarkan aturan
        $today = date('Y-m-d');
        $diff = (strtotime($data['tanggal_kadaluarsa']) - strtotime($today)) / (60 * 60 * 24);
        if ($data['jumlah'] <= 0) {
            $data['status'] = 'habis';
        } elseif ($diff <= 0) {
            $data['status'] = 'kadaluarsa';
        } elseif ($diff <= 3) {
            $data['status'] = 'segera_kadaluarsa';
        } else {
            $data['status'] = 'tersedia';
        }

        try {
            $this->bahanBakuModel->update($id, $data);

            return $this->respond(
                status: 200,
                data: [
                    'error' => false,
                    'message' => 'Bahan baku diperbarui.',
                    'data' => [
                        'bahan_baku' => $this->bahanBakuModel->asArray()->find($id)
                    ]
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Error update bahan_baku: ' . $e->getMessage());
            return $this->respond(
                status: 500,
                data: [
                    'error' => true,
                    'message' => 'Gagal memperbarui bahan baku.'
                ]
            );
        }
    }

    /**
     * Can be used for bulk delete
     * $id is not used 
     */
    public function delete($id = null)
    {
        $selected = $this->request->getJSON(true)['selected'] ?? null;

        if (empty($selected) || !is_array($selected)) {
            return $this->respond(status: 400, data: [
                'error' => true,
                'message' => 'Tidak ada yang dipilih untuk dihapus.'
            ]);
        }

        $selectedRows = $this->bahanBakuModel
            ->withPermintaanCount()
            ->whereIn('bahan_baku.id', $selected)->findAll();

        // filter hanya bahan baku yang kadaluarsa yang bisa dihapus dan permintaan_count = 0
        $idsToDelete = [];
        foreach ($selectedRows as $row) {
            if ($row['status'] === 'kadaluarsa' && $row['permintaan_count'] == 0) {
                $idsToDelete[] = $row['id'];
            }
        }

        if (empty($idsToDelete)) {
            return $this->respond(status: 400, data: [
                'error' => true,
                'message' => 'Tidak ada bahan baku kadaluarsa yang dipilih untuk dihapus.'
            ]);
        }

        try {
            $this->bahanBakuModel->whereIn('id', $idsToDelete)->delete();

            return $this->respond(
                status: 200,
                data: [
                    'error' => false,
                    'message' => 'Bahan baku berhasil dihapus.',
                    'data' => [
                        'deleted_ids' => $idsToDelete
                    ]
                ]
            );
        } catch (\Exception $e) {
            log_message('error', 'Error delete bahan_baku: ' . $e->getMessage());
            return $this->respond(
                status: 403,
                data: [
                    'error' => true,
                    'message' => 'Gagal menghapus bahan baku. Bahan baku mungkin sedang digunakan pada permintaan.'
                ]
            );
        }
    }

    protected function getCreateValidationRules(...$args): array
    {
        return [
            'nama' => 'required|min_length[3]|max_length[255]',
            'kategori' => 'required|min_length[3]|max_length[100]',
            'jumlah' => 'required|integer|greater_than[0]',
            'satuan' => 'required|min_length[1]|max_length[50]',
            'tanggal_masuk' => 'required|valid_date[Y-m-d]',
            'tanggal_kadaluarsa' => 'required|valid_date[Y-m-d]',
        ];
    }

    protected function getUpdateValidationRules(...$args): array
    {
        return [
            'nama' => 'required|min_length[3]|max_length[255]',
            'kategori' => 'required|min_length[3]|max_length[100]',
            'jumlah' => 'required|integer|greater_than[0]',
            'satuan' => 'required|min_length[1]|max_length[50]',
            'tanggal_masuk' => 'required|valid_date[Y-m-d]',
            'tanggal_kadaluarsa' => 'required|valid_date[Y-m-d]',
        ];
    }
}
