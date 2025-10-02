<?= $this->extend('templates/main_layout') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="row">
        <div class="col">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h1 class="mb-4"><?= $title ?></h1>
            </div>

            <div class="row">
                <div class="col-12">
                    <form id="search-form" method="get" class="row" role="search" novalidate>
                        <div class="col-4 mb-3">
                            <select class="form-select" name="status" aria-label="Filter by status">
                                <option value="" selected>Semua Status</option>
                                <option value="menunggu">Menunggu</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-8 mb-3">
                            <div class="input-group ">
                                <input class="form-control" type="search" name="keyword" placeholder="Cari permintaan" aria-label="Search">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="alert-parent"></div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th id="select-column-header" class="text-center">
                            <span>No</span>
                        </th>
                        <th>Tgl Masak</th>
                        <th>Pemohon</th>
                        <th>Menu Makan</th>
                        <th>Jumlah Porsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr>
                        <td colspan="7" class="text-center">Memuat...</td>
                    </tr>
                    <tr id="row-template" class="d-none">
                        <td id="tb-num" class="text-center"></td>
                        <td id="tb-tgl-masak"></td>
                        <td id="tb-pemohon"></td>
                        <td id="tb-menu-makan"></td>
                        <td id="tb-jumlah-porsi"></td>
                        <td id="tb-status">
                            <span class="badge bg-primary"></span>
                        </td>
                        <td>
                            <div class="d-flex flex-nowrap gap-1">
                                <button id="show-detail-button" type="button" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> <span class="d-none d-xl-inline">Detail</span>
                                </button>
                                <button id="approve-button" type="button" class="btn btn-success btn-sm">
                                    <i class="bi bi-check-lg"></i> <span class="d-none d-xl-inline">Setujui</span>
                                </button>
                                <button id="reject-button" type="button" class="btn btn-danger btn-sm">
                                    <i class="bi bi-x-lg"></i> <span class="d-none d-xl-inline">Tolak</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr id="no-data-row-template" class="d-none">
                        <td colspan="7" class="text-center">Data Tidak Ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('templates/scripts/input_validator_script') ?>
<?= $this->include('templates/scripts/alert_script') ?>

<!-- SHOW DETAIL -->
<?= view('templates/fullscreen_modal', [
    'modalId' => 'show-detail-modal',
    'modalTitle' => 'Detail Permintaan',
    'modalBody' => view('admin/permintaan/show'),
    'noConfirm' => true,
]) ?>

<!-- DELETE CONFIRMATION MODALS -->
<?= view('templates/simple_modal', [
    'modalId' => 'confirm-approval-modal',
    'modalTitle' => 'Peringatan',
    'modalBody' => 'Apakah Anda yakin ingin melanjutkan aksi ini?',
    'noConfirm' => false,
    'danger' => true,
    'submit' => true,
]) ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const PERMINTAAN_ENDPOINT = '<?= url_to('api/admin/permintaan') ?>';

    let isInSelectionMode = false;

    async function getAllPermintaan(search = null, status = null) {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);

        return await fetch(`${PERMINTAAN_ENDPOINT}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                },
            })
            .then(response => {
                if (response.status >= 500) throw new Error('Gagal memproses permintaan. Status: ' + response.status);
                return response.json();
            })
            .then(json => {
                console.log(json);

                if (json.error) {
                    console.error(json);
                    throw new Error(json.message);
                }
                return json.data.permintaan;
            });
    }

    async function approvePermintaan(id) {
        return await fetch(`${PERMINTAAN_ENDPOINT}/${id}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                },
            })
            .then(response => {
                if (response.status >= 500) throw new Error('Gagal memproses permintaan. Status: ' + response.status);
                return response.json();
            })
            .then(json => {
                console.log(json);

                if (json.error) {
                    console.error(json);
                    throw new Error(json.message);
                }
                return json;
            });
    }

    async function rejectPermintaan(id) {
        return await fetch(`${PERMINTAAN_ENDPOINT}/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                },
            })
            .then(response => {
                if (response.status >= 500) throw new Error('Gagal memproses permintaan. Status: ' + response.status);
                return response.json();
            })
            .then(json => {
                console.log(json);

                if (json.error) {
                    console.error(json);
                    throw new Error(json.message);
                }
                return json;
            });
    }

    document.addEventListener('DOMContentLoaded', async function() {
        const alertParent = document.getElementById('alert-parent');

        const searchForm = document.getElementById('search-form');

        const tbodyEl = document.getElementById('table-body');
        const rowTemplate = document.getElementById('row-template').cloneNode(true);
        const noDataRowTemplate = document.getElementById('no-data-row-template').cloneNode(true);

        const confirmApprovalModalEl = document.getElementById('confirm-approval-modal');

        const showDetailModal = new bootstrap.Modal(document.getElementById('show-detail-modal'));
        const confirmBulkDeleteModal = new bootstrap.Modal(confirmApprovalModalEl);

        function renderTable(allData) {
            try {
                tbodyEl.innerHTML = '';

                if (!allData || allData.length === 0) {
                    const rowEl = noDataRowTemplate.cloneNode(true);
                    rowEl.classList.remove('d-none');
                    tbodyEl.appendChild(rowEl);
                    return;
                }

                allData.forEach((data, index) => {
                    const rowEl = rowTemplate.cloneNode(true);

                    rowEl.classList.remove('d-none');

                    rowEl.id = data.id;
                    rowEl.querySelector('#tb-num').textContent = index + 1;
                    rowEl.querySelector('#tb-tgl-masak').textContent = data.tgl_masak ? new Date(data.tgl_masak).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }) : '-';
                    rowEl.querySelector('#tb-pemohon').textContent = data.pemohon ?? '-';
                    rowEl.querySelector('#tb-menu-makan').textContent = data.menu_makan ?? '-';
                    rowEl.querySelector('#tb-jumlah-porsi').textContent = data.jumlah_porsi ? `${data.jumlah_porsi} porsi` : '-';

                    const statusBadge = rowEl.querySelector('#tb-status').querySelector('.badge');
                    statusBadge.classList.remove('bg-primary');

                    statusBadge.innerText = data.status.toUpperCase() ?? '-';

                    switch (data.status) {
                        case 'menunggu':
                            statusBadge.classList.add('bg-warning', 'text-dark');
                            break;
                        case 'disetujui':
                            statusBadge.classList.add('bg-success');
                            break;
                        case 'ditolak':
                            statusBadge.classList.add('bg-danger');
                            break;
                    }

                    // Detail button
                    rowEl.querySelector('#show-detail-button').addEventListener('click', function() {
                        showDetail(data);
                        showDetailModal.show();
                    });

                    if (data.status === 'disetujui' || data.status === 'ditolak') {
                        rowEl.querySelector('#approve-button').disabled = true;
                        rowEl.querySelector('#approve-button').classList.remove('btn-success');
                        rowEl.querySelector('#approve-button').classList.add('btn-secondary');

                        rowEl.querySelector('#reject-button').disabled = true;
                        rowEl.querySelector('#reject-button').classList.remove('btn-danger');
                        rowEl.querySelector('#reject-button').classList.add('btn-secondary');
                    }

                    // Approve button
                    rowEl.querySelector('#approve-button').addEventListener('click', function() {
                        if (data.status !== 'menunggu') return;

                        confirmApprovalModalEl
                            .querySelector('.modal-body')
                            .innerHTML = `Apakah Anda yakin ingin menyetujui permintaan bahan baku untuk menu <b>"${data.menu_makan}"</b> oleh <b>"${data.pemohon}"</b>?`;

                        confirmBulkDeleteModal.show();

                        const modalConfirmButton = confirmApprovalModalEl.querySelector('#modal-confirm');
                        modalConfirmButton.onclick = approvalCallback(data.id, 'approve');
                    });

                    // Reject button
                    rowEl.querySelector('#reject-button').addEventListener('click', function() {
                        if (data.status !== 'menunggu') return;

                        confirmApprovalModalEl
                            .querySelector('.modal-body')
                            .innerHTML = `Apakah Anda yakin ingin menolak permintaan bahan baku untuk menu <b>"${data.menu_makan}"</b> oleh <b>"${data.pemohon}"</b>?`;

                        confirmBulkDeleteModal.show();

                        const modalConfirmButton = confirmApprovalModalEl.querySelector('#modal-confirm');
                        modalConfirmButton.onclick = approvalCallback(data.id, 'reject');
                    });

                    tbodyEl.appendChild(rowEl);
                });

            } catch (error) {
                console.error(error);
                showAlert(alertParent, error.message, 'danger');
            }
        }

        // SEARCH & FILTER FORM 
        async function searchBahanBaku() {
            const formData = new FormData(searchForm);
            const keyword = formData.get('keyword') || null;
            const status = formData.get('status') || null;
            return await getAllPermintaan(keyword, status);
        }

        searchForm.querySelectorAll('select').forEach(input => {
            input.addEventListener('change', function(e) {
                e.preventDefault();
                e.stopPropagation();
                searchBahanBaku().then(renderTable);
            });
        });

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            searchBahanBaku().then(renderTable);
        });

        // APPROVE & REJECT 
        const approvalCallback = (id, action) => async () => {
            const modalConfirmButton = confirmApprovalModalEl.querySelector('#modal-confirm');

            try {
                modalConfirmButton.setAttribute('disabled', 'true');
                modalConfirmButton.textContent = 'Memproses...';

                const res = action === 'approve' ? await approvePermintaan(id) : await rejectPermintaan(id);

                clearAlerts(alertParent);
                showAlert(alertParent, res.message ?? 'Berhasil', 'success');

                searchBahanBaku().then(renderTable);
            } catch (error) {
                clearAlerts(alertParent);
                showAlert(alertParent, error.message, 'danger');
            } finally {
                modalConfirmButton.removeAttribute('disabled');
                modalConfirmButton.textContent = 'Konfirmasi';
                confirmBulkDeleteModal.hide();
            }
        };


        const allData = await getAllPermintaan();
        renderTable(allData);
    });
</script>
<?= $this->endSection() ?>