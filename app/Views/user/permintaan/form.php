<form id="permintaan-form" class="needs-validation" novalidate>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="tgl_masak" class="form-label">Tanggal Masak</label>
            <input type="date" class="form-control" id="tgl_masak" name="tgl_masak" required>
            <div class="invalid-feedback">Tanggal masak wajib diisi dan harus H-1.</div>
        </div>

        <div class="col-md-6">
            <label for="jumlah_porsi" class="form-label">Jumlah Porsi</label>
            <input type="number" class="form-control" id="jumlah_porsi" name="jumlah_porsi" min="1" required>
            <div class="invalid-feedback">Jumlah porsi wajib diisi dan harus berupa angka positif.</div>
        </div>

        <div class="col-12">
            <label for="menu_makan" class="form-label">Menu Makan</label>
            <input type="text" class="form-control" id="menu_makan" name="menu_makan" placeholder="Contoh: Ayam Goreng" required>
            <div class="invalid-feedback">Menu makan wajib diisi.</div>
        </div>

        <div class="col-12">
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="form-label mb-0">Detail Bahan</label>
                <button type="button" id="add-detail-bahan" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Bahan
                </button>
            </div>

            <div id="detail-bahan-error" class="alert alert-danger d-none">
                Minimal harus ada 1 bahan yang diminta.
            </div>

            <div id="detail-bahan-container"></div>
        </div>
    </div>
</form>

<!-- Template untuk detail bahan -->
<div id="detail-bahan-template" class="d-none">
    <div class="row g-3 mb-3 detail-bahan-item border p-3 my-2 mx-0 rounded">
        <div class="col-md-6">
            <label class="form-label">Bahan Baku</label>
            <select class="form-select bahan-select" name="bahan_id" required>
                <option value="">Pilih Bahan</option>
            </select>
            <div class="invalid-feedback">Bahan wajib dipilih.</div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Diminta</label>
            <input type="number" class="form-control jumlah-diminta" name="jumlah_diminta" min="1" required>
            <div class="invalid-feedback">Jumlah diminta wajib diisi.</div>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-outline-danger btn-remove-bahan w-100">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>

<script>
    const BAHANBAKU_ENDPOINT = '<?= url_to('api/user/bahanbaku') ?>';

    const form = document.getElementById('permintaan-form');
    const tglMasakInput = form.querySelector('#tgl_masak');
    const menuMakanInput = form.querySelector('#menu_makan');
    const jumlahPorsiInput = form.querySelector('#jumlah_porsi');
    const detailBahanContainer = document.getElementById('detail-bahan-container');
    const detailBahanError = document.getElementById('detail-bahan-error');
    const detailBahanTemplate = document.getElementById('detail-bahan-template').querySelector('.detail-bahan-item');
    const addDetailBahanButton = document.getElementById('add-detail-bahan');

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tglMasakInput.value = tomorrow.toISOString().split('T')[0];

    const validateTglMasak = () => validateInputForm(
        tglMasakInput,
        (value) => {
            if (value.length < 1) return 'Tanggal masak wajib diisi.';

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(value);
            selectedDate.setHours(0, 0, 0, 0);

            const diff = (selectedDate - today) / (1000 * 60 * 60 * 24);

            if (diff !== 1) return 'Tanggal masak harus H-1 (besok).';
        },
        tglMasakInput.nextElementSibling
    );

    const validateMenuMakan = () => validateInputForm(
        menuMakanInput,
        (value) => {
            if (value.length < 1) return 'Menu makan wajib diisi.';
        },
        menuMakanInput.nextElementSibling
    );

    const validateJumlahPorsi = () => validateInputForm(
        jumlahPorsiInput,
        (value) => {
            if (value.length < 1 || isNaN(value) || Number(value) < 1) {
                return 'Jumlah porsi wajib diisi dan harus berupa angka positif.';
            }
        },
        jumlahPorsiInput.nextElementSibling
    );

    tglMasakInput.addEventListener('input', validateTglMasak);
    menuMakanInput.addEventListener('input', validateMenuMakan);
    jumlahPorsiInput.addEventListener('input', validateJumlahPorsi);

    let validators = [validateTglMasak, validateMenuMakan, validateJumlahPorsi];

    async function loadBahanBaku() {
        try {
            const response = await fetch(`${BAHANBAKU_ENDPOINT}?status=tersedia`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                }
            });

            const json = await response.json();
            return json.data?.bahan_baku || [];
        } catch (error) {
            console.error('Error loading bahan baku:', error);
            return [];
        }
    }

    async function addDetailBahan() {
        const newItem = detailBahanTemplate.cloneNode(true);
        const bahanSelect = newItem.querySelector('.bahan-select');

        const bahanBakuList = await loadBahanBaku();
        bahanBakuList.forEach(bahan => {
            const option = document.createElement('option');
            option.value = bahan.id;
            option.textContent = `${bahan.nama} (${bahan.jumlah} ${bahan.satuan})`;
            option.dataset.stok = bahan.jumlah;
            bahanSelect.appendChild(option);
        });

        const removeButton = newItem.querySelector('.btn-remove-bahan');
        removeButton.addEventListener('click', function() {
            newItem.remove();

            if (detailBahanContainer.querySelectorAll('.detail-bahan-item').length > 0) {
                detailBahanError.classList.add('d-none');
            }
        });

        const jumlahInput = newItem.querySelector('.jumlah-diminta');

        bahanSelect.addEventListener('change', function() {
            validateInputForm(
                bahanSelect,
                (value) => {
                    if (!value) return 'Bahan wajib dipilih.';
                },
                bahanSelect.nextElementSibling
            );
        });

        jumlahInput.addEventListener('input', function() {
            const selectedOption = bahanSelect.options[bahanSelect.selectedIndex];
            const stokTersedia = selectedOption?.dataset?.stok || 0;

            validateInputForm(
                jumlahInput,
                (value) => {
                    if (value.length < 1 || isNaN(value) || Number(value) < 1) {
                        return 'Jumlah diminta wajib diisi dan harus berupa angka positif.';
                    }
                    if (Number(value) > Number(stokTersedia)) {
                        return `Jumlah melebihi stok tersedia (${stokTersedia}).`;
                    }
                },
                jumlahInput.nextElementSibling
            );
        });

        detailBahanContainer.appendChild(newItem);

        detailBahanError.classList.add('d-none');
    }


    addDetailBahanButton.addEventListener('click', addDetailBahan);

    addDetailBahan();

    /**
     * function to reset the form
     */
    function resetForm() {
        form.reset();
        detailBahanContainer.innerHTML = '';
        detailBahanError.classList.add('d-none');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));

        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tglMasakInput.value = tomorrow.toISOString().split('T')[0];

        addDetailBahan();
    }

    /**
     * function to submit the form
     */
    async function submitForm() {
        if (!validateForm()) return false;

        const detailBahanItems = detailBahanContainer.querySelectorAll('.detail-bahan-item');
        if (detailBahanItems.length === 0) {
            detailBahanError.classList.remove('d-none');
            return false;
        }

        const detailBahan = [];
        let isValid = true;

        detailBahanItems.forEach(item => {
            const bahanSelect = item.querySelector('.bahan-select');
            const jumlahInput = item.querySelector('.jumlah-diminta');

            if (!bahanSelect.value || !jumlahInput.value || Number(jumlahInput.value) < 1) {
                bahanSelect.classList.add('is-invalid');
                jumlahInput.classList.add('is-invalid');
                isValid = false;
                return;
            }

            detailBahan.push({
                bahan_id: parseInt(bahanSelect.value),
                jumlah_diminta: parseInt(jumlahInput.value)
            });
        });

        if (!isValid) {
            return false;
        }

        const data = {
            tgl_masak: tglMasakInput.value,
            menu_makan: menuMakanInput.value,
            jumlah_porsi: parseInt(jumlahPorsiInput.value),
            detail_bahan: detailBahan
        };

        return await fetch(PERMINTAAN_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (response.status >= 500) throw new Error('Gagal memproses permintaan. Status: ' + response.status);
                return response.json();
            })
            .then(json => {
                if (json.error) {
                    const messages = Array.isArray(json.messages) ? json.messages.join(', ') : json.message;
                    throw new Error(messages || 'Terjadi kesalahan saat menyimpan data.');
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