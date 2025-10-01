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
                <div class="col-12 col-lg-4">
                    <button id="add-button" class="btn btn-primary mb-3">
                        <i class="bi bi-plus-circle"></i> Tambah Bahan Baku
                    </button>
                </div>
                <div class="col-12 col-lg-8">
                    <form id="search-form" method="get" class="row" role="search" novalidate>
                        <div class="col-3 mb-3">
                            <select class="form-select" name="category" aria-label="Filter by category">
                                <option value="" selected>Semua Kategori</option>
                                <option value="Karbohidrat">Karbohidrat</option>
                                <option value="Protein Hewani">Protein Hewani</option>
                                <option value="Protein Nabati">Protein Nabati</option>
                                <option value="Sayuran">Sayuran</option>
                                <option value="Bahan Masak">Bahan Masak</option>
                            </select>
                        </div>
                        <div class="col-3 mb-3">
                            <select class="form-select" name="status" aria-label="Filter by status">
                                <option value="" selected>Semua Status</option>
                                <option value="tersedia">Tersedia</option>
                                <option value="segera_kadaluarsa">Segera Kadaluarsa</option>
                                <option value="kadaluarsa">Kadaluarsa</option>
                                <option value="habis">Habis</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="input-group ">
                                <input class="form-control" type="search" name="keyword" placeholder="Cari bahan baku" aria-label="Search">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="alert-parent"></div>

            <div class="d-flex mb-2 gap-2">
                <button id="select-toggle-button" type="button" class="btn btn-outline-info mb-3">
                    <i class="bi bi-check2-square"></i> Pilih
                </button>
                <button id="bulk-delete-button" type="button" class="btn btn-danger mb-3 d-none" disabled>
                    <i class="bi bi-trash"></i> Hapus Terpilih
                </button>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th id="select-column-header" class="text-center">
                            <label for="select-all-checkbox" class="d-none w-100 h-100 px-1 py-1 text-center">
                                <input id="select-all-checkbox" class="form-check-input" type="checkbox" aria-label="Select all">
                            </label>
                            <span>No</span>
                        </th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Kadaluarsa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr>
                        <td colspan="8" class="text-center">Memuat...</td>
                    </tr>
                    <tr id="row-template" class="d-none">
                        <td id="tb-num-checkbox" class="text-center">
                            <label class="d-none w-100 h-100 px-1 py-3 text-center">
                                <input class="form-check-input" type="checkbox" name="selected[]" aria-label="Select one">
                            </label>
                            <span></span>
                        </td>
                        <td id="tb-nama"></td>
                        <td id="tb-kategori"></td>
                        <td id="tb-jumlah"></td>
                        <td id="tb-tanggal_masuk"></td>
                        <td id="tb-tanggal_kadaluarsa"></td>
                        <td id="tb-status">
                            <span class="badge bg-primary"></span>
                        </td>
                        <td>
                            <div class="d-flex flex-nowrap gap-1">
                                <button id="show-detail-button" type="button" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> <span class="d-none d-xl-inline">Detail</span>
                                </button>
                                <button id="edit-button" type="button" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> <span class="d-none d-xl-inline">Edit</span>
                                </button>
                                <button id="single-delete-button" type="button" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> <span class="d-none d-xl-inline">Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr id="no-data-row-template" class="d-none">
                        <td colspan="8" class="text-center">Data Tidak Ditemukan</td>
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
    'modalTitle' => 'Detail Bahan Baku',
    'modalBody' => view('admin/bahanbaku/show'),
    'noConfirm' => true,
]) ?>

<!-- ADD OR EDIT FORM -->
<?= view('templates/fullscreen_modal', [
    'modalId' => 'form-modal',
    'modalTitle' => 'Tambah atau Ubah Bahan Baku',
    'modalBody' => view('admin/bahanbaku/form'),
    'closeText' => 'Batal',
    'confirmText' => 'Simpan',
    'noConfirm' => false,
]) ?>

<!-- DELETE CONFIRMATION MODALS -->
<?= view('templates/simple_modal', [
    'modalId' => 'confirm-bulk-delete-modal',
    'modalTitle' => 'Peringatan',
    'modalBody' => 'Apakah Anda yakin ingin menghapus bahan baku terpilih? Tindakan ini tidak dapat dibatalkan.',
    'noConfirm' => false,
    'danger' => true,
    'submit' => true,
]) ?>

<?= view('templates/simple_modal', [
    'modalId' => 'no-selection-modal',
    'modalBody' => 'Tidak ada bahan baku yang dipilih. Silakan pilih setidaknya satu bahan baku untuk dihapus.',
    'noConfirm' => true,
]) ?>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const BAHANBAKU_ENDPOINT = '<?= url_to('api/admin/bahanbaku') ?>';

    let isInSelectionMode = false;

    async function getAllBahanBaku(search = null, category = null, status = null) {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (category) params.append('category', category);
        if (status) params.append('status', status);

        return await fetch(`${BAHANBAKU_ENDPOINT}?${params.toString()}`, {
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
                return json.data.bahan_baku;
            });
    }

    async function deleteBahanBaku(selectedIds) {
        return await fetch(BAHANBAKU_ENDPOINT, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                },
                body: JSON.stringify({
                    selected: selectedIds,
                }),
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

        const addButton = document.getElementById('add-button');

        const selectToggleButton = document.getElementById('select-toggle-button');
        const selectAllCheckBox = document.getElementById('select-all-checkbox');
        const bulkDeleteButton = document.getElementById('bulk-delete-button');

        const tbodyEl = document.getElementById('table-body');
        const rowTemplate = document.getElementById('row-template').cloneNode(true);
        const noDataRowTemplate = document.getElementById('no-data-row-template').cloneNode(true);

        const confirmBulkDeleteModalEl = document.getElementById('confirm-bulk-delete-modal');
        const formModalEl = document.getElementById('form-modal');

        const formModal = new bootstrap.Modal(document.getElementById('form-modal'));
        const showDetailModal = new bootstrap.Modal(document.getElementById('show-detail-modal'));
        const confirmBulkDeleteModal = new bootstrap.Modal(confirmBulkDeleteModalEl);
        const noSelectionModal = new bootstrap.Modal(document.getElementById('no-selection-modal'));

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

                    selectAllCheckBox.checked = false;

                    rowEl.id = data.id;
                    rowEl.querySelector('#tb-num-checkbox input').value = data.id;
                    rowEl.querySelector('#tb-num-checkbox span').textContent = index + 1;

                    if (!isInSelectionMode) {
                        rowEl.querySelector('#tb-num-checkbox label').classList.add('d-none');
                        rowEl.querySelector('#tb-num-checkbox span').classList.remove('d-none');
                    } else {
                        rowEl.querySelector('#tb-num-checkbox label').classList.remove('d-none');
                        rowEl.querySelector('#tb-num-checkbox span').classList.add('d-none');
                    }

                    rowEl.querySelector('#tb-nama').textContent = data.nama;
                    rowEl.querySelector('#tb-kategori').textContent = data.kategori;
                    rowEl.querySelector('#tb-jumlah').textContent = data.jumlah + ' ' + data.satuan;
                    rowEl.querySelector('#tb-tanggal_masuk').textContent = new Date(data.tanggal_masuk).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    rowEl.querySelector('#tb-tanggal_kadaluarsa').textContent = new Date(data.tanggal_kadaluarsa).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    const statusBadge = rowEl.querySelector('#tb-status').querySelector('.badge');

                    statusBadge.textContent = data.status.replace('_', ' ').toUpperCase();
                    statusBadge.classList.remove('bg-primary');

                    if (data.status === 'tersedia') statusBadge.classList.add('bg-success');
                    else if (data.status === 'segera_kadaluarsa') statusBadge.classList.add('bg-warning', 'text-dark');
                    else if (data.status === 'kadaluarsa') statusBadge.classList.add('bg-danger');
                    else if (data.status === 'habis') statusBadge.classList.add('bg-secondary');
                    else statusBadge.classList.add('bg-primary');


                    // Detail button
                    rowEl.querySelector('#show-detail-button').addEventListener('click', function() {
                        showDetail(data);
                        showDetailModal.show();
                    });

                    // Edit button
                    rowEl.querySelector('#edit-button').addEventListener('click', function() {
                        populateForm(data);
                        formModal.show();
                    });

                    if (data.status !== 'kadaluarsa') {
                        rowEl.querySelector('#single-delete-button').disabled = true;
                        rowEl.querySelector('#single-delete-button').classList.remove('btn-danger');
                        rowEl.querySelector('#single-delete-button').classList.add('btn-secondary');
                    }

                    // Single delete button
                    rowEl.querySelector('#single-delete-button').addEventListener('click', function() {
                        if (data.status !== 'kadaluarsa') return;

                        confirmBulkDeleteModalEl
                            .querySelector('.modal-body')
                            .textContent = `Apakah Anda yakin ingin menghapus bahan baku "${data.nama}"? Tindakan ini tidak dapat dibatalkan.`;

                        confirmBulkDeleteModal.show();

                        const modalConfirmButton = confirmBulkDeleteModalEl.querySelector('#modal-confirm');
                        modalConfirmButton.onclick = bulkDeleteCallback([data.id]);
                    });

                    tbodyEl.appendChild(rowEl);
                });

            } catch (error) {
                console.error(error);
                showAlert(alertParent, error.message, 'danger');
            }
        }

        // CREATE & EDIT FORM 
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            resetForm();
            formModal.show();
        });

        formModalEl.querySelector('#modal-confirm').addEventListener('click', async function() {
            try {
                formModalEl.querySelector('#modal-confirm').setAttribute('disabled', 'true');
                formModalEl.querySelector('#modal-confirm').textContent = 'Menyimpan...';

                const formAction = document.getElementById('id_bahan_baku').value ? 'edit' : 'create';
                const res = await submitForm(formAction);

                formModalEl.querySelector('#modal-confirm').removeAttribute('disabled');
                formModalEl.querySelector('#modal-confirm').textContent = 'Simpan';

                if (!res) return;

                clearAlerts(alertParent);
                showAlert(alertParent, res.message ?? 'Berhasil', 'success');
                searchBahanBaku().then(renderTable);
            } catch (error) {
                showAlert(alertParent, error.message, 'danger');
            }

            formModal.hide();

        });

        // SEARCH & FILTER FORM 
        async function searchBahanBaku() {
            const formData = new FormData(searchForm);
            const keyword = formData.get('keyword') || null;
            const category = formData.get('category') || null;
            const status = formData.get('status') || null;
            return await getAllBahanBaku(keyword, category, status);
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

        // BULK DELETE 
        const bulkDeleteCallback = (selected) => async () => {
            const modalConfirmButton = confirmBulkDeleteModalEl.querySelector('#modal-confirm');

            try {
                modalConfirmButton.setAttribute('disabled', 'true');
                modalConfirmButton.textContent = 'Menghapus...';

                const res = await deleteBahanBaku(selected);

                clearAlerts(alertParent);
                showAlert(alertParent, res.message ?? 'Berhasil', 'success');

                searchBahanBaku().then(renderTable);
            } catch (error) {
                clearAlerts(alertParent);
                showAlert(alertParent, error.message, 'danger');
            } finally {
                modalConfirmButton.removeAttribute('disabled');
                modalConfirmButton.textContent = 'Hapus';
                confirmBulkDeleteModal.hide();
            }
        };

        selectToggleButton.addEventListener('click', function() {
            isInSelectionMode = !isInSelectionMode;

            const selectColumnHeader = document.getElementById('select-column-header');

            if (isInSelectionMode) {
                selectToggleButton.classList.add('btn-info');
                selectToggleButton.classList.remove('btn-outline-info');
                selectToggleButton.innerHTML = '<i class="bi bi-x-square"></i> Batal';

                bulkDeleteButton.disabled = true;
                bulkDeleteButton.classList.remove('d-none');

                tbodyEl.querySelectorAll('tr').forEach(row => {
                    const label = row.querySelector('#tb-num-checkbox label');
                    const num = row.querySelector('#tb-num-checkbox span');
                    label.classList.remove('d-none');
                    num.classList.add('d-none');
                });

                selectAllCheckBox.parentElement.classList.remove('d-none');
                selectColumnHeader.querySelector('span').classList.add('d-none');
            } else {
                selectToggleButton.classList.remove('btn-info');
                selectToggleButton.classList.add('btn-outline-info');
                selectToggleButton.innerHTML = '<i class="bi bi-check2-square"></i> Pilih';

                selectAllCheckBox.checked = false;
                selectAllCheckBox.indeterminate = false;
                bulkDeleteButton.disabled = true;
                bulkDeleteButton.classList.add('d-none');

                tbodyEl.querySelectorAll('tr').forEach(row => {
                    const label = row.querySelector('#tb-num-checkbox label');
                    const checkbox = row.querySelector('#tb-num-checkbox input');
                    const num = row.querySelector('#tb-num-checkbox span');
                    checkbox.checked = false;
                    label.classList.add('d-none');
                    num.classList.remove('d-none');
                });

                selectAllCheckBox.parentElement.classList.add('d-none');
                selectColumnHeader.querySelector('span').classList.remove('d-none');
            }
        });

        selectAllCheckBox.addEventListener('change', function() {
            const isChecked = selectAllCheckBox.checked;
            tbodyEl.querySelectorAll('tr').forEach(row => {
                const checkbox = row.querySelector('#tb-num-checkbox input');
                checkbox.checked = isChecked;
            });
            bulkDeleteButton.disabled = !isChecked;
        });

        tbodyEl.addEventListener('change', function(e) {
            if (e.target && e.target.matches('#tb-num-checkbox input')) {
                const anyChecked = Array.from(tbodyEl.querySelectorAll('#tb-num-checkbox input')).some(checkbox => checkbox.checked);
                bulkDeleteButton.disabled = !anyChecked;

                const allChecked = Array.from(tbodyEl.querySelectorAll('#tb-num-checkbox input')).every(checkbox => checkbox.checked);
                selectAllCheckBox.checked = allChecked;
                selectAllCheckBox.indeterminate = !allChecked && anyChecked;
            }
        });

        bulkDeleteButton.addEventListener('click', function() {
            const selectedIds = Array.from(tbodyEl.querySelectorAll('#tb-num-checkbox input'))
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            if (selectedIds.length < 1) {
                noSelectionModal.show();
                return;
            }

            // tampilkan daftar nama bahan baku yang akan dihapus
            const selected = Array.from(tbodyEl.querySelectorAll('#tb-num-checkbox input'))
                .filter(checkbox => checkbox.checked)
                .map(checkbox => {
                    const row = checkbox.closest('tr');
                    return [row.querySelector('#tb-nama').textContent, row.querySelector('#tb-status .badge').outerHTML]
                });
            confirmBulkDeleteModalEl
                .querySelector('.modal-body')
                .innerHTML = `Apakah Anda yakin ingin menghapus bahan baku berikut? Tindakan ini tidak dapat dibatalkan.<br>`;
            const ul = document.createElement('ul');
            selected.forEach(([nama, status]) => {
                const li = document.createElement('li');
                li.innerHTML = `${nama} ${status}`;
                ul.appendChild(li);
            });
            confirmBulkDeleteModalEl.querySelector('.modal-body').appendChild(ul);

            confirmBulkDeleteModal.show();

            const modalConfirmButton = confirmBulkDeleteModalEl.querySelector('#modal-confirm');
            modalConfirmButton.onclick = bulkDeleteCallback(selectedIds);
        });

        const allData = await getAllBahanBaku();
        renderTable(allData);
    });
</script>
<?= $this->endSection() ?>