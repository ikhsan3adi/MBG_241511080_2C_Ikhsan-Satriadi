<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Tgl Permintaan</dt>
                    <dd class="col-sm-7" id="created_at">-</dd>

                    <dt class="col-sm-5">Tgl Masak</dt>
                    <dd class="col-sm-7" id="tgl_masak">-</dd>

                    <dt class="col-sm-5">Pemohon</dt>
                    <dd class="col-sm-7" id="pemohon">-</dd>

                    <dt class="col-sm-5">Menu Makan</dt>
                    <dd class="col-sm-7" id="menu_makan">-</dd>

                    <dt class="col-sm-5">Jml Porsi</dt>
                    <dd class="col-sm-7" id="jumlah_porsi">-</dd>

                    <dt class="col-sm-5">Status</dt>
                    <dd class="col-sm-7" id="status">
                        <span class="badge bg-secondary">-</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Detail Permintaan (Bahan Baku)</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Nama Bahan</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Tgl Masuk</th>
                                <th scope="col">Tgl Kadaluarsa</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody id="detail-table-body">
                        </tbody>
                        <tr id="detail-row-template" class="d-none">
                            <td id="nama">-</td>
                            <td id="kategori">-</td>
                            <td id="jumlah">-</td>
                            <td id="tanggal_masuk">-</td>
                            <td id="tanggal_kadaluarsa">-</td>
                            <td id="status">
                                <span class="badge bg-secondary">-</span>
                            </td>
                        </tr>
                        <tr id="no-data-detail-row-template" class="d-none">
                            <td colspan="6" class="text-center text-muted">
                                Tidak ada data
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function getDetails(id) {
        return await fetch(`${PERMINTAAN_ENDPOINT}/${id}`, {
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

    const rowTemplate = document.getElementById('detail-row-template');
    const noDataRow = document.getElementById('no-data-detail-row-template');

    const detailTBody = document.getElementById('detail-table-body');

    async function showDetail({
        id
    }) {
        try {
            resetDetailsView();

            const details = await getDetails(id);
            setDetailsView(details);
        } catch (error) {
            console.error('Error fetching details:', error);
            const alertParent = document.getElementById('alert-parent');
            showAlert(alertParent, error.message, 'danger');
        }
    }

    /**
     * @param {object} data
     */
    function setDetailsView(data) {
        document.getElementById('created_at').innerText = data.created_at ?
            new Date(data.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) : '-';
        document.getElementById('tgl_masak').innerText = data.tgl_masak ?
            new Date(data.tgl_masak).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }) : '-';
        document.getElementById('pemohon').innerText = data.pemohon ?? '-';
        document.getElementById('menu_makan').innerText = data.menu_makan ?? '-';
        document.getElementById('jumlah_porsi').innerText = data.jumlah_porsi ?? '-';

        const statusBadge = document.querySelector('#status').querySelector('.badge');
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

        //?================= Detail Permintaan (Bahan Baku) =================//

        if (data.detail_bahan && data.detail_bahan.length > 0) {
            noDataRow.classList.add('d-none');
            data.detail_bahan.forEach(item => {
                const row = rowTemplate.cloneNode(true);
                row.id = 'bahan-' + item.id;
                row.classList.remove('d-none');
                row.querySelector('#nama').innerText = item.nama || '-';
                row.querySelector('#kategori').innerText = item.kategori || '-';
                row.querySelector('#jumlah').innerText = `${item.jumlah} ${item.satuan}`;

                row.querySelector('#tanggal_masuk').innerText = item.tanggal_masuk ?
                    new Date(item.tanggal_masuk).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) : '-';
                row.querySelector('#tanggal_kadaluarsa').innerText = item.tanggal_kadaluarsa ?
                    new Date(item.tanggal_kadaluarsa).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) : '-';

                const statusBadge = row.querySelector('#status').querySelector('.badge');
                statusBadge.innerText = item.status.toUpperCase() || '-';
                switch (item.status) {
                    case 'tersedia':
                        statusBadge.classList.add('bg-success');
                        break;
                    case 'segera_kadaluarsa':
                        statusBadge.classList.add('bg-warning', 'text-dark');
                        break;
                    case 'kadaluarsa':
                        statusBadge.classList.add('bg-danger');
                        break;
                    case 'habis':
                        statusBadge.classList.add('bg-secondary');
                        break;
                }

                detailTBody.appendChild(row);
            });
        } else {
            noDataRow.classList.remove('d-none');
            detailTBody.appendChild(noDataRow);
        }
    }

    function resetDetailsView() {
        document.getElementById('created_at').innerText = '-';
        document.getElementById('tgl_masak').innerText = '-';
        document.getElementById('pemohon').innerText = '-';
        document.getElementById('menu_makan').innerText = '-';
        document.getElementById('jumlah_porsi').innerText = '-';

        const statusBadge = document.querySelector('#status').querySelector('.badge');
        statusBadge.textContent = 'LOADING...';
        statusBadge.className = 'badge bg-secondary';

        detailTBody.innerHTML = '';
        noDataRow.classList.remove('d-none');
        detailTBody.appendChild(noDataRow);
    }
</script>