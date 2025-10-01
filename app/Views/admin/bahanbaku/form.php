<form id="bahan-baku-form" class="needs-validation" novalidate>
    <input type="hidden" name="id" id="id_bahan_baku" value="">

    <div class="row g-3">
        <div class="col-12">
            <label for="nama" class="form-label">Nama Bahan Baku</label>
            <input type="text" class="form-control" id="nama" name="nama" placeholder="Bahan Baku MBG" required>
            <div class="invalid-feedback">Nama bahan baku wajib diisi.</div>
        </div>

        <div class="col-md-4">
            <label for="kategori" class="form-label">Kategori</label>
            <select id="kategori" class="form-select" name="kategori" required>
                <option value="" selected>Semua Kategori</option>
                <option value="Karbohidrat">Karbohidrat</option>
                <option value="Protein Hewani">Protein Hewani</option>
                <option value="Protein Nabati">Protein Nabati</option>
                <option value="Sayuran">Sayuran</option>
                <option value="Bahan Masak">Bahan Masak</option>
            </select>
            <div class="invalid-feedback">Kategori wajib diisi.</div>
        </div>

        <div class="col-md-4">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
            <div class="invalid-feedback">Jumlah wajib diisi dan harus berupa angka non-negatif.</div>
        </div>

        <div class="col-md-4">
            <label for="satuan" class="form-label">Satuan</label>
            <input type="text" class="form-control" id="satuan" name="satuan" placeholder="cth: kg" required>
            <div class="invalid-feedback">Satuan wajib diisi.</div>
        </div>

        <div class="col-md-6">
            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
            <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?= date('Y-m-d') ?>" required>
            <div class="invalid-feedback">Tanggal masuk wajib diisi dan harus berupa tanggal yang valid.</div>
        </div>

        <div class="col-md-6">
            <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
            <input type="date" class="form-control" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" required>
            <div class="invalid-feedback">Tanggal kadaluarsa wajib diisi dan harus berupa tanggal yang valid.</div>
        </div>
    </div>
</form>

<script>
    const form = document.getElementById('bahan-baku-form');
    const namaInput = form.querySelector('#nama');
    const kategoriInput = form.querySelector('#kategori');
    const jumlahInput = form.querySelector('#jumlah');
    const satuanInput = form.querySelector('#satuan');
    const tanggalMasukInput = form.querySelector('#tanggal_masuk');
    const tanggalKadaluarsaInput = form.querySelector('#tanggal_kadaluarsa');

    const validateNama = () => validateInputForm(
        namaInput,
        (value) => {
            if (value.length < 1) return 'Nama bahan baku wajib diisi.'
        },
        namaInput.nextElementSibling
    );

    const validateKategori = () => validateInputForm(
        kategoriInput,
        (value) => {
            if (value.length < 1) return 'Kategori wajib diisi.'
        },
        kategoriInput.nextElementSibling
    );

    const validateJumlah = () => validateInputForm(
        jumlahInput,
        (value) => {
            if (value.length < 1 || isNaN(value) || Number(value) < 1) {
                return 'Jumlah wajib diisi dan harus berupa angka positif.'
            }
        },
        jumlahInput.nextElementSibling
    );

    const validateSatuan = () => validateInputForm(
        satuanInput,
        (value) => {
            if (value.length < 1) return 'Satuan wajib diisi.'
        },
        satuanInput.nextElementSibling
    );

    const validateTanggalMasuk = () => validateInputForm(
        tanggalMasukInput,
        (value) => {
            if (value.length < 1 || isNaN(new Date(value).getTime())) {
                return 'Tanggal masuk wajib diisi dan harus berupa tanggal yang valid.'
            }
        },
        tanggalMasukInput.nextElementSibling
    );

    const validateTanggalKadaluarsa = () => validateInputForm(
        tanggalKadaluarsaInput,
        (value) => {
            const tanggalMasukValue = tanggalMasukInput.value;
            const now = new Date();
            const tanggalMasukDate = new Date(tanggalMasukValue);
            const tanggalKadaluarsaDate = new Date(value);
            const formAction = form.querySelector('#id_bahan_baku').value ? 'edit' : 'create';

            if (value.length < 1 || isNaN(tanggalKadaluarsaDate.getTime())) {
                return 'Tanggal kadaluarsa wajib diisi dan harus berupa tanggal yang valid.'
            }
            if (tanggalMasukValue.length > 0 && !isNaN(tanggalMasukDate.getTime())) {
                if (tanggalKadaluarsaDate < tanggalMasukDate) {
                    return 'Tanggal kadaluarsa harus lebih besar dari tanggal masuk.'
                }
            }
        },
        tanggalKadaluarsaInput.nextElementSibling
    );

    namaInput.addEventListener('input', validateNama);
    kategoriInput.addEventListener('input', validateKategori);
    jumlahInput.addEventListener('input', validateJumlah);
    satuanInput.addEventListener('input', validateSatuan);
    tanggalMasukInput.addEventListener('input', validateTanggalMasuk);
    tanggalKadaluarsaInput.addEventListener('input', validateTanggalKadaluarsa);

    let validators = [validateNama, validateKategori, validateJumlah, validateSatuan, validateTanggalMasuk, validateTanggalKadaluarsa];

    /**
     * function to populate the form with data
     * @param {object} data
     */
    function populateForm(data) {
        resetForm();

        form.querySelector('#id_bahan_baku').value = data.id || '';
        form.querySelector('#nama').value = data.nama || '';
        form.querySelector('#kategori').value = data.kategori || '';
        form.querySelector('#jumlah').value = data.jumlah || '';
        form.querySelector('#satuan').value = data.satuan || '';
        form.querySelector('#tanggal_masuk').value = data.tanggal_masuk ? data.tanggal_masuk.split(' ')[0] : '';
        form.querySelector('#tanggal_kadaluarsa').value = data.tanggal_kadaluarsa ? data.tanggal_kadaluarsa.split(' ')[0] : '';
    }

    /**
     * function to reset the form
     */
    function resetForm() {
        document.getElementById('bahan-baku-form').reset();
        document.getElementById('id_bahan_baku').value = '';
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
    }

    /**
     * function to submit the form
     * @param {string} action - 'create' or 'edit'
     */
    async function submitForm(action) {
        if (!validateForm()) return false;
        const form = document.getElementById('bahan-baku-form');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const url = action === 'create' ? BAHANBAKU_ENDPOINT : `${BAHANBAKU_ENDPOINT}/${data.id}`;

        return await fetch(url, {
                method: action === 'create' ? 'POST' : 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                },
                body: JSON.stringify(data),
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

    /**
     * function to validate the form
     * @returns {boolean} - true if valid, false otherwise
     */
    function validateForm() {
        return validators.every(fn => fn());
    }
</script>