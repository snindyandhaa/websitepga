<template>
  <div class="page-wrapper">
    <!-- Navbar Header (3 Dropdown Utama) -->
    <header class="navbar">
      <div class="brand">
        <NuxtLink to="/home">
          <img src="/logo.png" alt="Logo" class="nav-logo" />
        </NuxtLink>
      </div>

      <nav class="nav-menu">
        <!-- 1. Data MP -->
        <div class="dropdown">
          <button class="dropdown-btn">Data MP ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/data-karyawan">Data Karyawan</NuxtLink>
            <NuxtLink to="/visualisasi-data">Visualisasi Data</NuxtLink>
            <NuxtLink to="/employee-rotation">Employee Rotation</NuxtLink>
          </div>
        </div>

        <!-- 2. General Affair Asset -->
        <div class="dropdown">
          <button class="dropdown-btn">General Affair Asset ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/asset-car">Asset Car</NuxtLink>
            <NuxtLink to="/asset-pga">Asset PGA</NuxtLink>
          </div>
        </div>

        <!-- 3. More -->
        <div class="dropdown">
          <button class="dropdown-btn">More ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/data-summary">Data Summary</NuxtLink>
            <NuxtLink to="/bpjs">BPJS</NuxtLink>
          </div>
        </div>
      </nav>
    </header>

    <!-- Controls Bar (Toggle Mode: Tabel vs Visualisasi Data) -->
    <div class="control-bar">
      <div class="top-controls">
        <div class="view-type-toggle">
          <button 
            :class="['type-btn', { active: viewMode === 'table' }]" 
            @click="viewMode = 'table'">
            📊 Tabel Data BPJS
          </button>
          <button 
            :class="['type-btn', { active: viewMode === 'chart' }]" 
            @click="viewMode = 'chart'">
            📈 Visualisasi Data
          </button>
        </div>

        <!-- Search Input khusus mode tabel -->
        <div v-if="viewMode === 'table'" class="search-box">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Cari Periode / Tanggal..." 
            class="search-input"
          />
          <span class="search-icon">🔍</span>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <main class="content-container">
      <div v-if="loading" class="status-msg">Memuat data Pembayaran Asuransi BPJS...</div>
      <div v-else-if="errorMessage" class="status-error">{{ errorMessage }}</div>

      <template v-else>
        <!-- TAMPILAN 1: TABEL DATA -->
        <div v-if="viewMode === 'table'" class="table-card">
          <div class="table-title-header">
            <h2>Data Pembayaran Asuransi BPJS</h2>
            <span class="total-badge">Total: {{ filteredBpjsData.length }} Record</span>
          </div>

          <table class="main-table">
            <thead>
              <tr>
                <th class="col-no center">No</th>
                <th>PERIODE</th>
                <th class="text-right">JUMLAH YANG DIBAYARKAN</th>
                <th class="center">JUMLAH PEGAWAI</th>
                <th class="center">JUMLAH TANGGUNGAN</th>
                <th class="center">TANGGAL CETAK</th>
                <th class="center">TANGGAL JATUH TEMPO</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in filteredBpjsData" :key="index" class="table-row">
                <td class="col-no text-data center">{{ index + 1 }}</td>
                <td class="text-data bold">{{ item.Periode || '-' }}</td>
                <td class="text-data text-right bold text-blue">
                  {{ formatRupiah(item['Jumlah yang dibayarkan']) }}
                </td>
                <td class="text-data center">{{ item['Jumlah pegawai'] || 0 }}</td>
                <td class="text-data center">{{ item['Jumlah tanggungan'] || 0 }}</td>
                <td class="text-data center">{{ item['Tanggal cetak'] || '-' }}</td>
                <td class="text-data center">{{ item['Tanggal jatuh tempo'] || '-' }}</td>
              </tr>
              <tr v-if="filteredBpjsData.length === 0">
                <td colspan="7" class="empty-data">Data pembayaran BPJS tidak ditemukan.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- TAMPILAN 2: VISUALISASI DATA (3 LINE CHARTS) -->
        <div v-else class="charts-wrapper">
          <!-- Chart 1: Jumlah Pembayaran -->
          <div class="chart-card">
            <div class="chart-card-header">
              <h2>Jumlah Pembayaran</h2>
            </div>
            <div class="chart-box" style="height: 320px;">
              <Line :data="pembayaranChartData" :options="pembayaranChartOptions" />
            </div>
          </div>

          <!-- Chart 2: Jumlah Pegawai -->
          <div class="chart-card">
            <div class="chart-card-header">
              <h2>Jumlah Pegawai</h2>
            </div>
            <div class="chart-box" style="height: 320px;">
              <Line :data="pegawaiChartData" :options="singleValueChartOptions" />
            </div>
          </div>

          <!-- Chart 3: Jumlah Tanggungan -->
          <div class="chart-card">
            <div class="chart-card-header">
              <h2>Jumlah Tanggungan</h2>
            </div>
            <div class="chart-box" style="height: 320px;">
              <Line :data="tanggunganChartData" :options="singleValueChartOptions" />
            </div>
          </div>
        </div>
      </template>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  type ChartData,
  type ChartOptions
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)

const viewMode = ref<'table' | 'chart'>('table')
const searchQuery = ref<string>('')
const loading = ref<boolean>(true)
const errorMessage = ref<string>('')

const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gidBpjs = '957383272' // Sheet Data Pembayaran Asuransi BPJS

const bpjsList = ref<Record<string, any>[]>([])

const parseCsvLine = (text: string): string[] => {
  const result: string[] = []
  let entry = ''
  let inQuotes = false

  for (let i = 0; i < text.length; i++) {
    const char = text[i]
    if (char === '"') {
      inQuotes = !inQuotes
    } else if (char === ',' && !inQuotes) {
      result.push(entry.replace(/^"|"$/g, '').trim())
      entry = ''
    } else {
      entry += char
    }
  }
  result.push(entry.replace(/^"|"$/g, '').trim())
  return result
}

const parseCurrencyNumber = (val: any): number => {
  if (!val) return 0
  const cleanStr = String(val)
    .replace(/Rp\.?/gi, '')
    .replace(/\s+/g, '')
    .replace(/\./g, '')
    .replace(/,/g, '.')
  const parsed = parseFloat(cleanStr)
  return isNaN(parsed) ? 0 : parsed
}

const formatRupiah = (val: any): string => {
  const num = typeof val === 'number' ? val : parseCurrencyNumber(val)
  return 'Rp' + num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const loadBpjsData = async (): Promise<void> => {
  loading.value = true
  errorMessage.value = ''

  try {
    const csvUrl = `https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gidBpjs}`
    const csvText = await $fetch<string>(csvUrl, { responseType: 'text' })

    if (!csvText || csvText.includes('<!DOCTYPE html>')) {
      throw new Error('Gagal mengambil data BPJS. Pastikan Spreadsheet disetel Publik.')
    }

    let cleanCsv = csvText
    if (cleanCsv.startsWith('\uFEFF')) {
      cleanCsv = cleanCsv.slice(1)
    }

    const lines = cleanCsv.split(/\r?\n/).filter(l => l.trim() !== '')
    if (lines.length < 2) {
      bpjsList.value = []
      return
    }

    const rawHeaders = lines.shift() || ''
    const headers = parseCsvLine(rawHeaders).map(h => h.replace(/^\uFEFF/, '').trim())

    const parsedData: Record<string, any>[] = []

    lines.forEach(line => {
      const rowValues = parseCsvLine(line)
      const item: Record<string, any> = {}

      headers.forEach((header, idx) => {
        if (header) {
          item[header] = rowValues[idx] ? rowValues[idx].trim() : ''
        }
      })

      if (item['Periode'] || item['Jumlah yang dibayarkan']) {
        parsedData.push(item)
      }
    })

    bpjsList.value = parsedData

  } catch (err: any) {
    console.error('Error fetching BPJS data:', err)
    errorMessage.value = err.message || 'Gagal terhubung ke Google Sheets.'
  } finally {
    loading.value = false
  }
}

const filteredBpjsData = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  if (!q) return bpjsList.value

  return bpjsList.value.filter(item => {
    return Object.values(item).some(val => String(val).toLowerCase().includes(q))
  })
})

// Data Chart 1: Jumlah Pembayaran
const pembayaranChartData = computed<ChartData<'line'>>(() => {
  const labels = bpjsList.value.map(r => r.Periode || '-')
  const dataValues = bpjsList.value.map(r => parseCurrencyNumber(r['Jumlah yang dibayarkan']))

  return {
    labels,
    datasets: [
      {
        label: 'JUMLAH YANG DIBAYARKAN',
        data: dataValues,
        borderColor: '#3b82f6',
        backgroundColor: '#3b82f6',
        pointRadius: 5,
        tension: 0.1
      }
    ]
  }
})

const pembayaranChartOptions: ChartOptions<'line'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true, position: 'top' },
    tooltip: {
      callbacks: {
        label: (ctx) => ` Dibayar: ${formatRupiah(ctx.raw)}`
      }
    }
  },
  scales: {
    y: {
      ticks: {
        callback: (val) => `Rp ${(Number(val) / 1000000).toFixed(0)} jt`
      }
    }
  }
}

// Data Chart 2: Jumlah Pegawai
const pegawaiChartData = computed<ChartData<'line'>>(() => {
  const labels = bpjsList.value.map(r => r.Periode || '-')
  const dataValues = bpjsList.value.map(r => parseInt(r['Jumlah pegawai'] || '0', 10))

  return {
    labels,
    datasets: [
      {
        label: 'JUMLAH PEGAWAI',
        data: dataValues,
        borderColor: '#3b82f6',
        backgroundColor: '#3b82f6',
        pointRadius: 5,
        tension: 0.1
      }
    ]
  }
})

// Data Chart 3: Jumlah Tanggungan
const tanggunganChartData = computed<ChartData<'line'>>(() => {
  const labels = bpjsList.value.map(r => r.Periode || '-')
  const dataValues = bpjsList.value.map(r => parseInt(r['Jumlah tanggungan'] || '0', 10))

  return {
    labels,
    datasets: [
      {
        label: 'JUMLAH TANGGUNGAN',
        data: dataValues,
        borderColor: '#3b82f6',
        backgroundColor: '#3b82f6',
        pointRadius: 5,
        tension: 0.1
      }
    ]
  }
})

const singleValueChartOptions: ChartOptions<'line'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: true, position: 'top' }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { precision: 0 }
    }
  }
}

onMounted(() => {
  loadBpjsData()
})
</script>

<style scoped>
.page-wrapper {
  min-height: 100vh;
  background-color: #f1f5f9;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  width: 100%;
  box-sizing: border-box;
}

/* Navbar Style */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 48px;
  background: #ffffff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  position: sticky;
  top: 0;
  z-index: 50;
}

.nav-logo {
  height: 42px;
  object-fit: contain;
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dropdown {
  position: relative;
}

.dropdown-btn {
  background: transparent;
  border: none;
  font-size: 13px;
  cursor: pointer;
  color: #334155;
  padding: 8px 12px;
  font-weight: 600;
  border-radius: 6px;
  transition: all 0.2s;
}

.dropdown-btn:hover {
  background: #f1f5f9;
  color: #0d1b7a;
}

.dropdown-content {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background-color: #ffffff;
  min-width: 190px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
  z-index: 20;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  padding: 6px;
}

.dropdown-content a {
  color: #475569;
  padding: 8px 12px;
  text-decoration: none;
  display: block;
  font-size: 13px;
  font-weight: 500;
  border-radius: 6px;
}

.dropdown-content a:hover {
  background-color: #eff6ff;
  color: #0d1b7a;
}

.dropdown:hover .dropdown-content {
  display: block;
}

/* Control Bar Toggle */
.control-bar {
  max-width: 1200px;
  margin: 24px auto 0 auto;
  padding: 0 16px;
}

.top-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.view-type-toggle {
  display: flex;
  background: #e2e8f0;
  padding: 4px;
  border-radius: 8px;
}

.type-btn {
  background: transparent;
  border: none;
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 700;
  color: #475569;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.type-btn.active {
  background: #0d1b7a;
  color: #ffffff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.search-box {
  position: relative;
  min-width: 280px;
}

.search-input {
  width: 100%;
  padding: 10px 36px 10px 14px;
  border-radius: 8px;
  border: 1px solid #cbd5e1;
  outline: none;
  font-size: 13px;
  box-sizing: border-box;
}

.search-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  color: #94a3b8;
}

/* Main Content */
.content-container {
  width: 100%;
  max-width: 1200px;
  margin: 20px auto 40px auto;
  padding: 0 16px;
  box-sizing: border-box;
}

/* Table Design */
.table-card {
  background: #ffffff;
  border-radius: 12px;
  overflow-x: auto;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #e2e8f0;
}

.table-title-header {
  padding: 16px 20px;
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.table-title-header h2 {
  margin: 0;
  font-size: 16px;
  font-weight: 800;
  color: #0d1b7a;
}

.total-badge {
  background-color: #e0e7ff;
  color: #3730a3;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.main-table {
  width: 100%;
  border-collapse: collapse;
  white-space: nowrap;
}

.main-table thead {
  background-color: #0d1b7a;
  color: #ffffff;
}

.main-table th {
  padding: 14px 16px;
  font-size: 13px;
  font-weight: 700;
  text-align: left;
}

.table-row {
  border-bottom: 1px solid #e2e8f0;
}

.table-row:nth-child(even) {
  background-color: #f8fafc;
}

.main-table td {
  padding: 12px 16px;
  font-size: 13px;
}

.text-data {
  color: #1e293b;
}

.text-right { text-align: right; }
.center { text-align: center; }
.bold { font-weight: 700; }
.text-blue { color: #1d4ed8; }
.col-no { width: 5%; text-align: center; }

/* Charts Layout */
.charts-wrapper {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.chart-card {
  background: #ffffff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
  border: 1px solid #e2e8f0;
}

.chart-card-header h2 {
  font-size: 18px;
  font-weight: 800;
  color: #0d1b7a;
  margin: 0 0 16px 0;
}

.empty-data {
  text-align: center;
  padding: 32px;
  color: #94a3b8;
}

.status-msg, .status-error {
  text-align: center;
  padding: 32px;
  background: #ffffff;
  border-radius: 8px;
  color: #64748b;
}

.status-error { color: #ef4444; }

@media screen and (max-width: 768px) {
  .navbar {
    padding: 12px 16px;
    flex-direction: column;
    gap: 10px;
  }

  .nav-menu {
    flex-wrap: wrap;
    justify-content: center;
  }

  .top-controls {
    flex-direction: column;
    align-items: stretch;
  }

  .view-type-toggle {
    flex-direction: column;
  }
}
</style>