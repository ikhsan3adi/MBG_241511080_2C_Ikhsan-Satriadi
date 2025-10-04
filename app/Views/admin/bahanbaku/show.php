<div class="row g-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Nama</dt>
                    <dd class="col-sm-7" id="nama">-</dd>

                    <dt class="col-sm-5">Kategori</dt>
                    <dd class="col-sm-7" id="kategori">-</dd>

                    <dt class="col-sm-5">Jumlah</dt>
                    <dd class="col-sm-7" id="jumlah">-</dd>

                    <dt class="col-sm-5">Tanggal Masuk</dt>
                    <dd class="col-sm-7" id="tanggal_masuk">-</dd>

                    <dt class="col-sm-5">Tanggal Kadaluarsa</dt>
                    <dd class="col-sm-7" id="tanggal_kadaluarsa">-</dd>

                    <dt class="col-sm-5">Status</dt>
                    <dd class="col-sm-7" id="status">
                        <span class="badge bg-secondary">-</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Riwayat Permintaan</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Tgl</th>
                                <th scope="col">Pemohon</th>
                                <th scope="col">Jml Diminta</th>
                                <th scope="col">Tgl Masak</th>
                                <th scope="col">Menu Makan</th>
                                <th scope="col">Jml Porsi</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody id="data-body">
                        </tbody>
                        <tr id="detail-row-template" class="d-none">
                            <td id="created_at">-</td>
                            <td id="pemohon">-</td>
                            <td id="jumlah_diminta">-</td>
                            <td id="tgl_masak">-</td>
                            <td id="menu_makan">-</td>
                            <td id="jumlah_porsi">-</td>
                            <td id="status">
                                <span class="badge bg-secondary">-</span>
                            </td>
                        </tr>
                        <tr id="no-data-detail-row-template" class="d-none">
                            <td colspan="7" class="text-center text-muted">
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
        return await fetch(`${BAHANBAKU_ENDPOINT}/${id}`, {
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

    const rowTemplate = document.getElementById('detail-row-template');
    const noDataRow = document.getElementById('no-data-detail-row-template');

    const tbody = document.getElementById('data-body');

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
        document.getElementById('nama').innerText = data.nama ?? '-';
        document.getElementById('kategori').innerText = data.kategori ?? '-';
        document.getElementById('jumlah').innerText = data.jumlah ? `${data.jumlah} ${data.satuan}` : '-';
        document.getElementById('tanggal_masuk').innerText = data.tanggal_masuk ?
            new Date(data.tanggal_masuk).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : '-';
        document.getElementById('tanggal_kadaluarsa').innerText = data.tanggal_kadaluarsa ?
            new Date(data.tanggal_kadaluarsa).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : '-';

        const statusBadge = document.querySelector('#status').querySelector('.badge');

        statusBadge.textContent = data.status.replace('_', ' ').toUpperCase();
        statusBadge.classList.remove('bg-primary');

        if (data.status === 'tersedia') statusBadge.classList.add('bg-success');
        else if (data.status === 'segera_kadaluarsa') statusBadge.classList.add('bg-warning', 'text-dark');
        else if (data.status === 'kadaluarsa') statusBadge.classList.add('bg-danger');
        else if (data.status === 'habis') statusBadge.classList.add('bg-secondary');
        else statusBadge.classList.add('bg-primary');


        //?================= Riwayat Permintaan =================//

        if (data.riwayat_permintaan && data.riwayat_permintaan.length > 0) {
            noDataRow.classList.add('d-none');
            data.riwayat_permintaan.forEach(item => {
                const row = rowTemplate.cloneNode(true);
                row.id = 'permintaan-' + item.id;
                row.classList.remove('d-none');

                row.querySelector('#created_at').innerText = item.created_at ?
                    new Date(item.created_at).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';
                row.querySelector('#pemohon').innerText = item.pemohon ?? '-';
                row.querySelector('#jumlah_diminta').innerText = item.jumlah_diminta ? `${item.jumlah_diminta} ${data.satuan}` : '-';
                row.querySelector('#tgl_masak').innerText = item.tgl_masak ?
                    new Date(item.tgl_masak).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) : '-';
                row.querySelector('#menu_makan').innerText = item.menu_makan ?? '-';
                row.querySelector('#jumlah_porsi').innerText = item.jumlah_porsi ?? '-';

                const statusBadge = row.querySelector('#status').querySelector('.badge');

                statusBadge.innerText = item.status.toUpperCase() ?? '-';

                switch (item.status) {
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

                tbody.appendChild(row);
            });
        } else {
            noDataRow.classList.remove('d-none');
            tbody.appendChild(noDataRow);
        }
    }

    function resetDetailsView() {
        document.getElementById('nama').innerText = '-';
        document.getElementById('kategori').innerText = '-';
        document.getElementById('jumlah').innerText = '-';
        document.getElementById('tanggal_masuk').innerText = '-';
        document.getElementById('tanggal_kadaluarsa').innerText = '-';

        const statusBadge = document.querySelector('#status').querySelector('.badge');
        statusBadge.textContent = 'LOADING...';
        statusBadge.className = 'badge bg-secondary';

        tbody.innerHTML = '';
        noDataRow.classList.remove('d-none');
        tbody.appendChild(noDataRow);
    }
</script>