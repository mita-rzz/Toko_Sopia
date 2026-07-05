<?php include '../config/database.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Toko Sopia</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 80px;
            background-color: #ffffff;
            height: 100vh;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            position: fixed;
            z-index: 100;
        }

        .logo-menu {
            font-size: 28px;
            color: #005371;
            margin-bottom: 40px;
            cursor: pointer;
        }

        .nav-item {
            position: relative;
            margin-bottom: 20px;
            text-decoration: none;
            color: #757575;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .nav-item i {
            font-size: 24px;
        }

        .nav-item.active {
            background: linear-gradient(90deg, #22a699, #005371);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(34, 166, 153, 0.4);
        }

        .nav-item:hover:not(.active) {
            background-color: #f0f0f0;
            color: #22a699;
        }

        .tooltip {
            position: absolute;
            left: 70px;
            background-color: #333;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            transform: translateX(-10px);
            z-index: 200;
        }

        .tooltip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: -4px;
            transform: translateY(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: transparent #333 transparent transparent;
        }

        .nav-item:hover .tooltip {
            opacity: 1;
            transform: translateX(0);
        }

        .main-content {
            margin-left: 80px;
            padding: 24px;
            width: calc(100% - 80px);
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* ===== HEADER ===== */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .header-title span {
            color: #22a699;
        }

        .header-sub {
            font-size: 13px;
            color: #888;
            margin-top: 3px;
        }

        .filter-bar {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-filter {
            padding: 8px 18px;
            border: 1.5px solid #ddd;
            background: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #555;
            cursor: pointer;
            transition: 0.25s;
        }

        .btn-filter.active,
        .btn-filter:hover {
            background: linear-gradient(90deg, #22a699, #005371);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 3px 10px rgba(34, 166, 153, 0.25);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
        }

        .summary-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px 22px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .card-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }

        .card-icon.green  { background: rgba(34,166,153,0.12); color: #22a699; }
        .card-icon.blue   { background: rgba(0,83,113,0.10);   color: #005371; }
        .card-icon.orange { background: rgba(243,156,18,0.12);  color: #f39c12; }
        .card-icon.purple { background: rgba(155,89,182,0.12);  color: #9b59b6; }

        .card-info .card-label {
            font-size: 12px;
            color: #888;
            font-weight: 500;
        }

        .card-info .card-value {
            font-size: 20px;
            font-weight: 700;
            color: #222;
            margin-top: 2px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .chart-panel {
            background: #fff;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .chart-panel.full-width {
            grid-column: 1 / -1;
        }

        .chart-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .chart-subtitle {
            font-size: 12px;
            color: #999;
            margin-top: 2px;
        }

        .chart-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(34,166,153,0.1);
            color: #22a699;
        }

        .chart-wrapper {
            position: relative;
        }

        .loading-overlay {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 220px;
            flex-direction: column;
            gap: 12px;
            color: #aaa;
            font-size: 14px;
        }

        .spinner {
            width: 36px;
            height: 36px;
            border: 3px solid #eee;
            border-top-color: #22a699;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 900px) {
            .charts-grid { grid-template-columns: 1fr; }
            .chart-panel.full-width { grid-column: auto; }
        }

        @media (max-width: 600px) {
            .main-content { padding: 14px; }
            .summary-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <i class='bx bx-store logo-menu'></i>
        <a href="index.php" class="nav-item">
            <i class='bx bx-laptop'></i>
            <span class="tooltip">Kasir / Transaksi</span>
        </a>
        <a href="barangView.php" class="nav-item">
            <i class='bx bx-package'></i>
            <span class="tooltip">Manajemen Barang</span>
        </a>
        <a href="laporan.php" class="nav-item active">
            <i class='bx bx-bar-chart-alt-2'></i>
            <span class="tooltip">Laporan</span>
        </a>
    </div>
    <div class="main-content">
        <div class="page-header">
            <div>
                <h1 class="header-title">Laporan <span>Penjualan</span></h1>
                <p class="header-sub">Statistik dan grafik penjualan Toko Sopia</p>
            </div>
            <div class="filter-bar">
                <button class="btn-filter active" id="btn-harian"  onclick="setMode('harian')">30 Hari</button>
                <button class="btn-filter"         id="btn-bulanan" onclick="setMode('bulanan')">12 Bulan</button>
            </div>
        </div>
        <div class="summary-grid" id="summary-grid">
            <div class="summary-card">
                <div class="card-icon green"><i class='bx bx-money'></i></div>
                <div class="card-info">
                    <div class="card-label">Penjualan Hari Ini</div>
                    <div class="card-value" id="val-hari-ini">—</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon blue"><i class='bx bx-receipt'></i></div>
                <div class="card-info">
                    <div class="card-label">Transaksi Hari Ini</div>
                    <div class="card-value" id="val-transaksi-hari">—</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon orange"><i class='bx bx-trending-up'></i></div>
                <div class="card-info">
                    <div class="card-label">Penjualan Bulan Ini</div>
                    <div class="card-value" id="val-bulan-ini">—</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon purple"><i class='bx bx-package'></i></div>
                <div class="card-info">
                    <div class="card-label">Produk Aktif</div>
                    <div class="card-value" id="val-produk">—</div>
                </div>
            </div>
        </div>
        <div class="charts-grid">
            <div class="chart-panel">
                <div class="chart-panel-header">
                    <div>
                        <div class="chart-title" id="main-chart-title">Penjualan 30 Hari Terakhir</div>
                        <div class="chart-subtitle">Total omzet per hari (Rupiah)</div>
                    </div>
                    <span class="chart-badge" id="main-chart-badge">Harian</span>
                </div>
                <div class="chart-wrapper">
                    <div class="loading-overlay" id="loading-main">
                        <div class="spinner"></div>
                        <span>Memuat data...</span>
                    </div>
                    <canvas id="chartPenjualan" style="display:none;"></canvas>
                </div>
            </div>
            <div class="chart-panel">
                <div class="chart-panel-header">
                    <div>
                        <div class="chart-title">Jumlah Transaksi</div>
                        <div class="chart-subtitle">Frekuensi transaksi</div>
                    </div>
                    <span class="chart-badge">Count</span>
                </div>
                <div class="chart-wrapper">
                    <div class="loading-overlay" id="loading-transaksi">
                        <div class="spinner"></div>
                        <span>Memuat data...</span>
                    </div>
                    <canvas id="chartTransaksi" style="display:none;"></canvas>
                </div>
            </div>
            <div class="chart-panel full-width">
                <div class="chart-panel-header">
                    <div>
                        <div class="chart-title">Top 10 Barang Terlaris</div>
                        <div class="chart-subtitle">Berdasarkan total qty terjual (12 bulan terakhir)</div>
                    </div>
                    <span class="chart-badge">Qty Terjual</span>
                </div>
                <div class="chart-wrapper">
                    <div class="loading-overlay" id="loading-barang">
                        <div class="spinner"></div>
                        <span>Memuat data...</span>
                    </div>
                    <canvas id="chartBarang" style="display:none;"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script>
        const PROCESS_URL = '../process/grafik.php';
        let chartPenjualan = null;
        let chartTransaksi = null;
        let chartBarang     = null;
        let currentMode     = 'harian';
        function formatRupiah(angka) {
            if (angka >= 1_000_000) return 'Rp ' + (angka / 1_000_000).toFixed(1) + ' Jt';
            if (angka >= 1_000)     return 'Rp ' + (angka / 1_000).toFixed(0) + ' Rb';
            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        function formatRupiahFull(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID');
        }
        function showLoading(id)  { document.getElementById(id).style.display = 'flex'; }
        function hideLoading(id)  { document.getElementById(id).style.display = 'none'; }
        function showCanvas(id)   { document.getElementById(id).style.display = 'block'; }
        function makeGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { family: 'Poppins', size: 13 },
                    bodyFont:  { family: 'Poppins', size: 12 },
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins', size: 11 }, color: '#888' }
                },
                y: {
                    grid: { color: '#f0f0f0', drawBorder: false },
                    ticks: { font: { family: 'Poppins', size: 11 }, color: '#888' }
                }
            },
            animation: { duration: 700, easing: 'easeInOutQuart' }
        };

        async function loadRingkasan() {
            try {
                const res  = await fetch(`${PROCESS_URL}?type=ringkasan`);
                const json = await res.json();
                if (json.status !== 'success') return;
                const d = json.data;
                document.getElementById('val-hari-ini').textContent    = formatRupiahFull(d.penjualan_hari_ini);
                document.getElementById('val-transaksi-hari').textContent = d.transaksi_hari_ini + ' Transaksi';
                document.getElementById('val-bulan-ini').textContent   = formatRupiahFull(d.penjualan_bulan_ini);
                document.getElementById('val-produk').textContent      = d.produk_aktif + ' Produk';
            } catch(e) { console.warn('Gagal load ringkasan', e); }
        }

        async function loadGrafikPenjualan(mode) {
            const type = mode === 'harian' ? 'penjualan_harian' : 'penjualan_bulanan';
            showLoading('loading-main');
            showLoading('loading-transaksi');
            document.getElementById('chartPenjualan').style.display = 'none';
            document.getElementById('chartTransaksi').style.display = 'none';

            try {
                const res  = await fetch(`${PROCESS_URL}?type=${type}`);
                const json = await res.json();

                if (json.status !== 'success' || json.data.length === 0) {
                    hideLoading('loading-main');
                    hideLoading('loading-transaksi');
                    document.getElementById('loading-main').innerHTML     = '<span style="color:#bbb;font-size:13px;">Belum ada data penjualan.</span>';
                    document.getElementById('loading-transaksi').innerHTML = '<span style="color:#bbb;font-size:13px;">Belum ada data.</span>';
                    return;
                }

                const labels    = json.data.map(d => d.label);
                const omzet     = json.data.map(d => d.total_penjualan);
                const transaksi = json.data.map(d => d.jumlah_transaksi);

                hideLoading('loading-main');
                hideLoading('loading-transaksi');
                showCanvas('chartPenjualan');
                showCanvas('chartTransaksi');

                const ctx1 = document.getElementById('chartPenjualan').getContext('2d');
                document.getElementById('chartPenjualan').height = 260;
                if (chartPenjualan) chartPenjualan.destroy();
                chartPenjualan = new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Total Omzet',
                            data: omzet,
                            backgroundColor: makeGradient(ctx1, 'rgba(34,166,153,0.85)', 'rgba(0,83,113,0.50)'),
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        ...baseOptions,
                        plugins: {
                            ...baseOptions.plugins,
                            tooltip: {
                                ...baseOptions.plugins.tooltip,
                                callbacks: {
                                    label: ctx => ' ' + formatRupiahFull(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            ...baseOptions.scales,
                            y: {
                                ...baseOptions.scales.y,
                                ticks: {
                                    ...baseOptions.scales.y.ticks,
                                    callback: val => formatRupiah(val)
                                }
                            }
                        }
                    }
                });

                const ctx2 = document.getElementById('chartTransaksi').getContext('2d');
                document.getElementById('chartTransaksi').height = 260;
                if (chartTransaksi) chartTransaksi.destroy();
                chartTransaksi = new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: transaksi,
                            backgroundColor: makeGradient(ctx2, 'rgba(155,89,182,0.80)', 'rgba(243,156,18,0.50)'),
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        ...baseOptions,
                        plugins: {
                            ...baseOptions.plugins,
                            tooltip: {
                                ...baseOptions.plugins.tooltip,
                                callbacks: {
                                    label: ctx => ' ' + ctx.parsed.y + ' transaksi'
                                }
                            }
                        },
                        scales: {
                            ...baseOptions.scales,
                            y: {
                                ...baseOptions.scales.y,
                                ticks: {
                                    ...baseOptions.scales.y.ticks,
                                    stepSize: 1,
                                    callback: val => Number.isInteger(val) ? val : ''
                                }
                            }
                        }
                    }
                });

            } catch(e) {
                console.warn('Gagal load grafik penjualan', e);
                hideLoading('loading-main');
                hideLoading('loading-transaksi');
            }
        }

        async function loadGrafikBarang() {
            showLoading('loading-barang');
            document.getElementById('chartBarang').style.display = 'none';

            try {
                const res  = await fetch(`${PROCESS_URL}?type=barang_terlaris`);
                const json = await res.json();

                if (json.status !== 'success' || json.data.length === 0) {
                    hideLoading('loading-barang');
                    document.getElementById('loading-barang').innerHTML = '<span style="color:#bbb;font-size:13px;">Belum ada data barang terlaris.</span>';
                    return;
                }

                const labels  = json.data.map(d => d.nama_barang);
                const qty     = json.data.map(d => d.total_terjual);
                const palette = [
                    'rgba(34,166,153,0.82)',
                    'rgba(0,83,113,0.78)',
                    'rgba(243,156,18,0.80)',
                    'rgba(155,89,182,0.80)',
                    'rgba(231,76,60,0.78)',
                    'rgba(52,152,219,0.80)',
                    'rgba(26,188,156,0.80)',
                    'rgba(241,196,15,0.82)',
                    'rgba(230,126,34,0.80)',
                    'rgba(149,165,166,0.80)',
                ];

                hideLoading('loading-barang');
                showCanvas('chartBarang');

                const ctx3 = document.getElementById('chartBarang').getContext('2d');
                document.getElementById('chartBarang').height = 300;
                if (chartBarang) chartBarang.destroy();
                chartBarang = new Chart(ctx3, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Qty Terjual',
                            data: qty,
                            backgroundColor: palette.slice(0, labels.length),
                            borderRadius: 7,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        ...baseOptions,
                        plugins: {
                            ...baseOptions.plugins,
                            tooltip: {
                                ...baseOptions.plugins.tooltip,
                                callbacks: {
                                    label: ctx => ' ' + ctx.parsed.y + ' pcs terjual'
                                }
                            }
                        }
                    }
                });

            } catch(e) {
                console.warn('Gagal load barang terlaris', e);
                hideLoading('loading-barang');
            }
        }

        function setMode(mode) {
            currentMode = mode;

            document.getElementById('btn-harian').classList.toggle('active',  mode === 'harian');
            document.getElementById('btn-bulanan').classList.toggle('active', mode === 'bulanan');

            if (mode === 'harian') {
                document.getElementById('main-chart-title').textContent = 'Penjualan 30 Hari Terakhir';
                document.getElementById('main-chart-badge').textContent = 'Harian';
            } else {
                document.getElementById('main-chart-title').textContent = 'Penjualan 12 Bulan Terakhir';
                document.getElementById('main-chart-badge').textContent = 'Bulanan';
            }

            loadGrafikPenjualan(mode);
        }

        loadRingkasan();
        loadGrafikPenjualan('harian');
        loadGrafikBarang();
    </script>

</body>
</html>
