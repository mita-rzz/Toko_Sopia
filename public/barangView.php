<?php include '../config/database.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang - Toko Sopia</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body { background-color: #f4f6f9; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 80px; background: #fff; height: 100vh;
            border-right: 1px solid #e0e0e0; display: flex; flex-direction: column;
            align-items: center; padding-top: 20px; position: fixed; z-index: 100;
        }
        .logo-menu { font-size: 28px; color: #005371; margin-bottom: 40px; }
        .nav-item {
            position: relative; margin-bottom: 20px; text-decoration: none;
            color: #757575; width: 50px; height: 50px; display: flex;
            justify-content: center; align-items: center;
            border-radius: 12px; transition: all 0.3s ease;
        }
        .nav-item i { font-size: 24px; }
        .nav-item.active {
            background: linear-gradient(90deg, #22a699, #005371);
            color: #fff; box-shadow: 0 4px 10px rgba(34,166,153,0.4);
        }
        .nav-item:hover:not(.active) { background: #f0f0f0; color: #22a699; }
        .tooltip {
            position: absolute; left: 70px; background: #333; color: #fff;
            padding: 6px 12px; border-radius: 6px; font-size: 12px;
            white-space: nowrap; opacity: 0; pointer-events: none;
            transition: all 0.3s ease; transform: translateX(-10px); z-index: 200;
        }
        .tooltip::before {
            content: ''; position: absolute; top: 50%; left: -4px;
            transform: translateY(-50%); border-width: 5px; border-style: solid;
            border-color: transparent #333 transparent transparent;
        }
        .nav-item:hover .tooltip { opacity: 1; transform: translateX(0); }

        /* ── Main ── */
        .main-content { margin-left: 80px; padding: 24px; width: calc(100% - 80px); }

        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
        }
        .header-title { font-size: 24px; font-weight: 600; color: #333; }
        .header-title span { color: #22a699; }
        .header-sub { font-size: 13px; color: #888; margin-top: 3px; }

        .btn-tambah {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 20px; border: none; border-radius: 10px;
            background: linear-gradient(90deg, #22a699, #005371);
            color: #fff; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: 0.3s;
        }
        .btn-tambah:hover { opacity: 0.88; box-shadow: 0 4px 15px rgba(34,166,153,0.35); }

        /* ── Search bar ── */
        .toolbar { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; }
        .search-wrap { position: relative; flex: 1; max-width: 380px; }
        .search-wrap i {
            position: absolute; top: 50%; left: 13px;
            transform: translateY(-50%); color: #888; font-size: 20px;
        }
        .search-input {
            width: 100%; padding: 11px 16px 11px 42px;
            border: 1px solid #ddd; border-radius: 10px;
            font-size: 14px; outline: none; transition: 0.3s;
        }
        .search-input:focus { border-color: #22a699; box-shadow: 0 0 8px rgba(34,166,153,0.2); }

        /* ── Table panel ── */
        .table-panel {
            background: #fff; border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04); overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        thead { background: linear-gradient(90deg, #22a699, #005371); }
        thead th {
            padding: 14px 18px; text-align: left;
            font-size: 13px; font-weight: 600; color: #fff;
        }
        tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.2s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f9fbfd; }
        tbody td { padding: 13px 18px; font-size: 14px; color: #444; vertical-align: middle; }

        .badge-kategori {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 500;
            background: rgba(34,166,153,0.1); color: #22a699;
        }
        .stok-low { color: #e74c3c; font-weight: 600; }

        .btn-icon {
            width: 34px; height: 34px; border: none; border-radius: 8px;
            cursor: pointer; display: inline-flex; align-items: center;
            justify-content: center; font-size: 17px; transition: 0.25s;
        }
        .btn-edit  { background: rgba(0,83,113,0.1);    color: #005371; }
        .btn-hapus { background: rgba(231,76,60,0.1);   color: #e74c3c; }
        .btn-edit:hover  { background: #005371; color: #fff; }
        .btn-hapus:hover { background: #e74c3c; color: #fff; }

        /* ── Empty state ── */
        .empty-state {
            text-align: center; padding: 60px 20px; color: #bbb;
        }
        .empty-state i { font-size: 52px; margin-bottom: 12px; }
        .empty-state p { font-size: 14px; }

        /* ── Modal ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.45); z-index: 500;
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #fff; border-radius: 16px; width: 100%;
            max-width: 480px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            animation: popIn 0.25s ease;
        }
        @keyframes popIn {
            from { transform: scale(0.92); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }
        .modal-header {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 22px;
        }
        .modal-title { font-size: 18px; font-weight: 600; color: #333; }
        .btn-close {
            background: none; border: none; font-size: 22px;
            color: #888; cursor: pointer; line-height: 1;
        }
        .btn-close:hover { color: #e74c3c; }
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #555; margin-bottom: 6px;
        }
        .form-control {
            width: 100%; padding: 10px 14px; border: 1px solid #ddd;
            border-radius: 8px; font-size: 14px; outline: none; transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        .form-control:focus { border-color: #22a699; box-shadow: 0 0 0 3px rgba(34,166,153,0.12); }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px; }
        .btn-batal {
            padding: 10px 22px; border: 1.5px solid #ddd; border-radius: 8px;
            background: #fff; font-size: 14px; color: #666; cursor: pointer; transition: 0.25s;
        }
        .btn-batal:hover { background: #f5f5f5; }
        .btn-simpan {
            padding: 10px 24px; border: none; border-radius: 8px;
            background: linear-gradient(90deg, #22a699, #005371);
            color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        .btn-simpan:hover { opacity: 0.88; }

        /* loading skeleton row */
        .skeleton td { height: 44px; background: linear-gradient(90deg,#f0f0f0 25%,#e8e8e8 50%,#f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.2s infinite; border-radius: 4px; }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <i class='bx bx-store logo-menu'></i>
        <a href="index.php" class="nav-item">
            <i class='bx bx-laptop'></i><span class="tooltip">Kasir / Transaksi</span>
        </a>
        <a href="barangView.php" class="nav-item active">
            <i class='bx bx-package'></i><span class="tooltip">Manajemen Barang</span>
        </a>
        <a href="laporan.php" class="nav-item">
            <i class='bx bx-bar-chart-alt-2'></i><span class="tooltip">Laporan</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <div class="page-header">
            <div>
                <h1 class="header-title">Manajemen <span>Barang</span></h1>
                <p class="header-sub">Kelola data produk / barang Toko Sopia</p>
            </div>
            <button class="btn-tambah" onclick="openModal()">
                <i class='bx bx-plus'></i> Tambah Barang
            </button>
        </div>

        <div class="toolbar">
            <div class="search-wrap">
                <i class='bx bx-search'></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari nama barang atau kategori...">
            </div>
        </div>

        <div class="table-panel">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr class="skeleton"><td colspan="6">&nbsp;</td></tr>
                    <tr class="skeleton"><td colspan="6">&nbsp;</td></tr>
                    <tr class="skeleton"><td colspan="6">&nbsp;</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah / Edit -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title" id="modalTitle">Tambah Barang</span>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <input type="hidden" id="editId">
            <div class="form-group">
                <label>Nama Barang <span style="color:#e74c3c">*</span></label>
                <input type="text" id="fNama" class="form-control" placeholder="Contoh: Minyak Goreng 1L">
            </div>
            <div class="form-group">
                <label>Kategori <span style="color:#e74c3c">*</span></label>
                <select id="fKategori" class="form-control">
                    <option value="">-- Pilih Kategori --</option>
                </select>
            </div>
            <div class="form-group">
                <label>Harga (Rp) <span style="color:#e74c3c">*</span></label>
                <input type="number" id="fHarga" class="form-control" placeholder="0" min="0">
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" id="fStok" class="form-control" placeholder="0" min="0">
            </div>
            <div class="modal-footer">
                <button class="btn-batal" onclick="closeModal()">Batal</button>
                <button class="btn-simpan" onclick="simpanBarang()">Simpan</button>
            </div>
        </div>
    </div>

    <script>
        const API = '../process/barang.php';
        let allData = [];
        let kategoriList = [];

        /* ── Format Rupiah ── */
        function fmt(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        /* ── Load Kategori (untuk dropdown) ── */
        async function loadKategori() {
            const res  = await fetch(API + '?type=kategori');
            const json = await res.json();
            if (json.status !== 'success') return;
            kategoriList = json.data;
            const sel = document.getElementById('fKategori');
            sel.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            kategoriList.forEach(k => {
                sel.innerHTML += `<option value="${k.id_kategori}">${k.nama_kategori}</option>`;
            });
        }

        /* ── Render Tabel ── */
        function renderTable(data) {
            const tbody = document.getElementById('tableBody');
            if (!data.length) {
                tbody.innerHTML = `
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <i class='bx bx-package'></i>
                            <p>Belum ada data barang.</p>
                        </div>
                    </td></tr>`;
                return;
            }
            tbody.innerHTML = data.map((b, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td><strong>${escHtml(b.nama_barang)}</strong></td>
                    <td><span class="badge-kategori">${escHtml(b.nama_kategori ?? '-')}</span></td>
                    <td>${fmt(b.harga)}</td>
                    <td class="${b.stok <= 5 ? 'stok-low' : ''}">${b.stok} ${b.stok <= 5 ? '⚠️' : ''}</td>
                    <td>
                        <button class="btn-icon btn-edit" title="Edit" onclick='openEdit(${JSON.stringify(b)})'><i class='bx bx-edit'></i></button>
                        <button class="btn-icon btn-hapus" title="Hapus" onclick="hapusBarang(${b.id_barang}, '${escHtml(b.nama_barang)}')" style="margin-left:6px"><i class='bx bx-trash'></i></button>
                    </td>
                </tr>`).join('');
        }

        /* ── Load Data Barang ── */
        async function loadBarang(search = '') {
            const url = API + (search ? '?search=' + encodeURIComponent(search) : '');
            const res  = await fetch(url);
            const json = await res.json();
            if (json.status !== 'success') return;
            allData = json.data;
            renderTable(allData);
        }

        /* ── Escape HTML ── */
        function escHtml(str) {
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        /* ── Modal ── */
        function openModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Barang';
            document.getElementById('editId').value  = '';
            document.getElementById('fNama').value   = '';
            document.getElementById('fKategori').value = '';
            document.getElementById('fHarga').value  = '';
            document.getElementById('fStok').value   = '0';
            document.getElementById('modalOverlay').classList.add('open');
        }

        function openEdit(b) {
            document.getElementById('modalTitle').textContent = 'Edit Barang';
            document.getElementById('editId').value      = b.id_barang;
            document.getElementById('fNama').value       = b.nama_barang;
            document.getElementById('fKategori').value   = b.id_kategori;
            document.getElementById('fHarga').value      = b.harga;
            document.getElementById('fStok').value       = b.stok;
            document.getElementById('modalOverlay').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('open');
        }

        /* ── Simpan (Tambah / Edit) ── */
        async function simpanBarang() {
            const id          = document.getElementById('editId').value;
            const nama        = document.getElementById('fNama').value.trim();
            const id_kategori = document.getElementById('fKategori').value;
            const harga       = document.getElementById('fHarga').value;
            const stok        = document.getElementById('fStok').value;

            if (!nama || !id_kategori || !harga) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Nama barang, kategori, dan harga wajib diisi!' });
                return;
            }

            const isEdit = id !== '';
            const params = new URLSearchParams({ nama_barang: nama, id_kategori, harga, stok });
            if (isEdit) params.append('id_barang', id);

            const res  = await fetch(API, { method: isEdit ? 'PUT' : 'POST', body: isEdit ? params : (() => { params.append('action','tambah'); return params; })() });
            const json = await res.json();

            if (json.status === 'success') {
                closeModal();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: json.message, showConfirmButton: false, timer: 1800 });
                loadBarang(document.getElementById('searchInput').value);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: json.message });
            }
        }

        /* ── Hapus ── */
        async function hapusBarang(id, nama) {
            const result = await Swal.fire({
                icon: 'warning', title: 'Hapus Barang?',
                text: `"${nama}" akan dihapus permanen.`,
                showCancelButton: true, confirmButtonColor: '#e74c3c',
                cancelButtonText: 'Batal', confirmButtonText: 'Ya, Hapus!'
            });
            if (!result.isConfirmed) return;

            const params = new URLSearchParams({ id_barang: id });
            const res    = await fetch(API, { method: 'DELETE', body: params });
            const json   = await res.json();

            if (json.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: json.message, showConfirmButton: false, timer: 1600 });
                loadBarang(document.getElementById('searchInput').value);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: json.message });
            }
        }

        /* ── Tutup modal klik overlay ── */
        document.getElementById('modalOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        /* ── Search dengan debounce ── */
        let debounceTimer;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => loadBarang(this.value.trim()), 350);
        });

        /* ── Init ── */
        loadKategori();
        loadBarang();
    </script>
</body>
</html>
