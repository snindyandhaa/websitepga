<template>
  <div class="asset-container">
    <!-- Navbar Header -->
    <header class="navbar">
      <div class="brand">
        <NuxtLink to="/home">
          <img src="/logo.png" alt="Logo" class="nav-logo" />
        </NuxtLink>
      </div>

      <nav class="nav-menu">
        <div class="dropdown">
          <button>Data MP ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/data-karyawan">Data Karyawan</NuxtLink>
            <NuxtLink to="/visualisasi-data">Visualisasi Data</NuxtLink>
            <NuxtLink to="/employee-rotation">Employee Rotation</NuxtLink>
          </div>
        </div>

        <div class="dropdown">
          <button>General Affair Asset ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/asset-car">Asset Car</NuxtLink>
            <NuxtLink to="/asset-pga">Asset PGA</NuxtLink>
          </div>
        </div>

        <div class="dropdown">
          <button>Asuransi ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/reliance">Reliance</NuxtLink>
            <NuxtLink to="/bpjs">BPJS</NuxtLink>
          </div>
        </div>

        <div class="dropdown">
          <button>Recruitment ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/data-summary">Data Summary</NuxtLink>
          </div>
        </div>

        <div class="dropdown">
          <button>More ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/wip-hcga">Work In Progress HC & GA</NuxtLink>
            <NuxtLink to="/budget-department">Budget Department</NuxtLink>
          </div>
        </div>

        <NuxtLink to="/" class="btn-logout">Logout</NuxtLink>
      </nav>
    </header>

    <!-- Banner Hero Section -->
    <section class="hero-section">
      <img src="/foto-karyawan.png" alt="Pabrik Header" class="hero-bg" />
    </section>

    <!-- Search Section & Toggle Mode (Data / Diagram) -->
    <div class="search-section">
      <div class="search-bar-row">
        <div class="search-box">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Kata Kunci: [Location] [Department] [License No] [Merek]" 
            class="input-search"
          />
          <button class="btn-search">🔍</button>
        </div>

        <!-- Tombol Switch Data / Diagram -->
        <div class="view-toggle">
          <button 
            :class="['toggle-btn', { active: viewMode === 'data' }]" 
            @click="viewMode = 'data'">
            📋 Data
          </button>
          <button 
            :class="['toggle-btn', { active: viewMode === 'diagram' }]" 
            @click="viewMode = 'diagram'">
            📊 Diagram
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <main class="content-wrapper">
      <!-- State Loading -->
      <div v-if="loading" class="table-card">
        <div class="loading-text">
          Memuat data Asset Car dari Google Sheets...
        </div>
      </div>

      <!-- State Error -->
      <div v-else-if="errorMessage" class="table-card">
        <div class="error-text">
          {{ errorMessage }}
        </div>
      </div>

      <template v-else>
        <!-- TAMPILAN 1: TABEL DATA -->
        <div v-if="viewMode === 'data'" class="table-card">
          <table class="custom-table">
            <thead>
              <tr>
                <th>Location</th>
                <th>Department</th>
                <th>License No</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in filteredAssets" :key="index">
                <td>{{ getVal(item, ['LOCATION', 'Location', 'Lokasi']) }}</td>
                <td>{{ getVal(item, ['DEPARTMENT', 'Department', 'Departemen']) }}</td>
                <td><strong>{{ getVal(item, ['LICENSE NO', 'License No', 'LICENSE', 'No Polisi']) }}</strong></td>
                <td class="text-center">
                  <button class="btn-detail" @click="openModal(item)">Lihat detail</button>
                </td>
              </tr>
              <tr v-if="filteredAssets.length === 0">
                <td colspan="4" class="text-center no-data">
                  Data aset tidak ditemukan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- TAMPILAN 2: DIAGRAM LINGKARAN / PIE CHART -->
        <div v-if="viewMode === 'diagram'" class="charts-grid">
          <div class="chart-card">
            <h3>Department</h3>
            <div class="chart-box">
              <Pie :data="getPieChartData(['DEPARTMENT', 'Department', 'Departemen'])" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h3>Location</h3>
            <div class="chart-box">
              <Pie :data="getPieChartData(['LOCATION', 'Location', 'Lokasi'])" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h3>Year Made</h3>
            <div class="chart-box">
              <Pie :data="getPieChartData(['YEAR MADE', 'Year Made', 'Tahun'])" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h3>Merek</h3>
            <div class="chart-box">
              <Pie :data="getPieChartData(['MEREK', 'Merek', 'Brand'])" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h3>Type-1</h3>
            <div class="chart-box">
              <Pie :data="getPieChartData(['TYPE-1', 'Type-1', 'Tipe'])" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h3>Fuel</h3>
            <div class="chart-box">
              <Pie :data="getPieChartData(['FUEL', 'Fuel', 'Bahan Bakar'])" :options="pieOptions" />
            </div>
          </div>
        </div>
      </template>
    </main>

    <!-- Modal Popup Detail Asset Car -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-card">
        <div class="modal-header">
          <h3>Detail Asset Car</h3>
          <button class="btn-close" @click="closeModal">✕</button>
        </div>

        <div class="modal-body" v-if="selectedAsset">
          <div class="detail-grid">
            <div class="detail-item">
              <label>License No</label>
              <span>{{ getVal(selectedAsset, ['LICENSE NO', 'License No', 'LICENSE', 'No Polisi']) }}</span>
            </div>
            <div class="detail-item">
              <label>Year Made</label>
              <span>{{ getVal(selectedAsset, ['YEAR MADE', 'Year Made', 'Tahun']) }}</span>
            </div>
            <div class="detail-item">
              <label>Company / Status</label>
              <span>{{ getVal(selectedAsset, ['COMPANY', 'Company', 'Perusahaan']) }}</span>
            </div>
            <div class="detail-item">
              <label>Department</label>
              <span>{{ getVal(selectedAsset, ['DEPARTMENT', 'Department', 'Departemen']) }}</span>
            </div>
            <div class="detail-item">
              <label>Location</label>
              <span>{{ getVal(selectedAsset, ['LOCATION', 'Location', 'Lokasi']) }}</span>
            </div>
            <div class="detail-item">
              <label>Fuel</label>
              <span>{{ getVal(selectedAsset, ['FUEL', 'Fuel', 'Bahan Bakar']) }}</span>
            </div>
            <div class="detail-item">
              <label>Merek</label>
              <span>{{ getVal(selectedAsset, ['MEREK', 'Merek', 'Brand']) }}</span>
            </div>
            <div class="detail-item">
              <label>Type-1</label>
              <span>{{ getVal(selectedAsset, ['TYPE-1', 'Type-1', 'Tipe']) }}</span>
            </div>
            <div class="detail-item">
              <label>Warna</label>
              <span>{{ getVal(selectedAsset, ['WARNA', 'Warna', 'Color']) }}</span>
            </div>
            <div class="detail-item">
              <label>Name of User</label>
              <span>{{ getVal(selectedAsset, ['NAME OF THE USER', 'NAME OF USER', 'Name of User', 'User']) }}</span>
            </div>
            <div class="detail-item">
              <label>Masa Berlaku Pajak STNK</label>
              <span>{{ formatDate(getVal(selectedAsset, ['Masa Berlaku PAJAK STNK', 'MASA BERLAKU PAJAK STNK', 'Pajak STNK'])) }}</span>
            </div>
            <div class="detail-item">
              <label>Nama STNK</label>
              <span>{{ getVal(selectedAsset, ['Nama STNK', 'NAMA STNK']) }}</span>
            </div>
            <div class="detail-item">
              <label>Nomor Mesin</label>
              <span>{{ getVal(selectedAsset, ['Nomor Mesin', 'NOMOR MESIN']) }}</span>
            </div>
            <div class="detail-item">
              <label>Nomor Rangka</label>
              <span>{{ getVal(selectedAsset, ['Nomor Rangka', 'NOMOR RANGKA']) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const assetList = ref([])
const loading = ref(true)
const errorMessage = ref('')
const searchQuery = ref('')
const viewMode = ref('data')

const showModal = ref(false)
const selectedAsset = ref(null)

// Spreadsheet ID & GID untuk Tab Asset Car
const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gidAssetCar = '117909656'

const chartColors = [
  '#0d1b7a', '#2563eb', '#3b82f6', '#60a5fa', 
  '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
  '#ec4899', '#06b6d4', '#64748b', '#a855f7'
]

const pieOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'right'
    }
  }
}

const parseCsvLine = (text) => {
  const result = []
  let entry = ''
  let inQuotes = false

  for (let i = 0; i < text.length; i++) {
    const char = text[i]
    if (char === '"') {
      inQuotes = !inQuotes
    } else if (char === ',' && !inQuotes) {
      result.push(entry.replace(/^"|"$/g, ''))
      entry = ''
    } else {
      entry += char
    }
  }
  result.push(entry.replace(/^"|"$/g, ''))
  return result
}

// Fungsi Load Data langsung dari Google Sheets CSV
const loadExcelData = async () => {
  loading.value = true
  errorMessage.value = ''

  const csvUrl = `https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gidAssetCar}`

  try {
    const csvText = await $fetch(csvUrl, { responseType: 'text' })

    if (!csvText || csvText.includes('<!DOCTYPE html>')) {
      throw new Error('Gagal mengambil data dari Google Sheets. Pastikan akses Google Sheets publik.')
    }

    const lines = csvText.split('\n').map(l => l.replace('\r', ''))
    if (lines.length < 2) {
      assetList.value = []
      return
    }

    const rawHeaders = lines.shift() || ''
    const headers = parseCsvLine(rawHeaders).map(h => h.trim())

    const parsedData = []

    lines.forEach(line => {
      if (line.trim() === '') return
      const rowValues = parseCsvLine(line)
      const item = {}
      
      headers.forEach((header, idx) => {
        if (header) {
          item[header] = rowValues[idx] ? rowValues[idx].trim() : ''
        }
      })

      if (Object.values(item).some(v => v !== '')) {
        parsedData.push(item)
      }
    })

    assetList.value = parsedData

  } catch (err) {
    console.error('Error fetching asset data:', err)
    errorMessage.value = err.message || 'Gagal memuat data dari Google Sheets.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadExcelData()
})

// Helper fleksibel untuk mengambil nilai field
const getVal = (item, keys) => {
  if (!item) return '-'
  
  const normalize = (str) => String(str).toLowerCase().replace(/[^a-z0-9]/g, '')

  for (const searchKey of keys) {
    const targetNorm = normalize(searchKey)
    for (const actualKey in item) {
      if (normalize(actualKey) === targetNorm) {
        const val = item[actualKey]
        if (val !== undefined && val !== null && String(val).trim() !== '') {
          return val
        }
      }
    }
  }
  return '-'
}

// Helper Format Tanggal Pajak STNK
const formatDate = (val) => {
  if (!val || val === '-' || val === 'undefined') return '-'
  let d = null

  if (val instanceof Date) {
    d = val
  } else if (typeof val === 'number') {
    d = new Date(Math.round((val - 25569) * 86400 * 1000))
  } else if (typeof val === 'string') {
    const cleanStr = val.trim()
    const ddmmyyyyPattern = /^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/
    const match = cleanStr.match(ddmmyyyyPattern)
    
    if (match) {
      const day = match[1].padStart(2, '0')
      const month = match[2].padStart(2, '0')
      const year = match[3]
      return `${day}/${month}/${year}`
    }

    const parsed = new Date(cleanStr)
    if (!isNaN(parsed.getTime())) {
      d = parsed
    }
  }

  if (d && !isNaN(d.getTime())) {
    const day = String(d.getDate()).padStart(2, '0')
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const year = d.getFullYear()
    return `${day}/${month}/${year}`
  }

  return String(val)
}

// Computed Filter Pencarian
const filteredAssets = computed(() => {
  if (!searchQuery.value) return assetList.value

  const q = searchQuery.value.toLowerCase()
  return assetList.value.filter(item => {
    const location = String(getVal(item, ['LOCATION', 'Location', 'Lokasi'])).toLowerCase()
    const dept = String(getVal(item, ['DEPARTMENT', 'Department', 'Departemen'])).toLowerCase()
    const licenseNo = String(getVal(item, ['LICENSE NO', 'License No', 'LICENSE', 'No Polisi'])).toLowerCase()
    const brand = String(getVal(item, ['MEREK', 'Merek', 'Brand'])).toLowerCase()

    return location.includes(q) || dept.includes(q) || licenseNo.includes(q) || brand.includes(q)
  })
})

// Generator Data Pie Chart
const getPieChartData = (columnKeys) => {
  const counts = {}
  filteredAssets.value.forEach(item => {
    const rawVal = getVal(item, columnKeys)
    const val = rawVal !== '-' ? String(rawVal).trim() : 'Lainnya'
    counts[val] = (counts[val] || 0) + 1
  })

  const labels = Object.keys(counts)
  return {
    labels,
    datasets: [{
      label: 'Jumlah Unit',
      data: labels.map(key => counts[key]),
      backgroundColor: chartColors.slice(0, labels.length)
    }]
  }
}

// Handler Modal
const openModal = (item) => {
  selectedAsset.value = item
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedAsset.value = null
}
</script>

<style scoped>
.asset-container { 
  background-color: #f8fafc; 
  min-height: 100vh; 
}

/* Navbar Style */
.navbar { 
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
  padding: 10px 40px; 
  background-color: #ffffff; 
  border-bottom: 1px solid #ddd; 
}
.nav-logo { height: 45px; }
.nav-menu { display: flex; align-items: center; gap: 15px; }
.dropdown { position: relative; display: inline-block; }
.dropdown button { background: none; border: none; font-size: 13px; cursor: pointer; color: #333; padding: 4px; font-weight: 500; }
.dropdown-content { display: none; position: absolute; background-color: #ffffff; min-width: 170px; box-shadow: 0px 4px 12px rgba(0,0,0,0.15); z-index: 10; border-radius: 6px; overflow: hidden; }
.dropdown-content a { color: #333; padding: 10px 14px; text-decoration: none; display: block; font-size: 12px; }
.dropdown-content a:hover { background-color: #f1f5f9; color: #0d1b7a; }
.dropdown:hover .dropdown-content { display: block; }
.btn-logout { background: #ef4444; color: white; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; }

/* Hero Section */
.hero-section { width: 100%; height: 250px; overflow: hidden; }
.hero-bg { width: 100%; height: 100%; object-fit: cover; }

/* Search Section & View Toggle */
.search-section { 
  max-width: 1000px; 
  margin: -30px auto 20px auto; 
  padding: 0 20px; 
  position: relative; 
  z-index: 5; 
}

.search-bar-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  flex-wrap: wrap;
}

.search-box { 
  display: flex; 
  background: white; 
  border-radius: 6px; 
  box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
  overflow: hidden; 
  flex: 1;
  min-width: 300px;
}
.input-search { flex: 1; border: none; padding: 14px 18px; font-size: 13px; outline: none; }
.btn-search { background: white; border: none; padding: 0 20px; cursor: pointer; font-size: 16px; }

/* Toggle Button Data / Diagram */
.view-toggle {
  display: flex;
  background: #ffffff;
  border-radius: 6px;
  padding: 3px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.toggle-btn {
  border: none;
  background: transparent;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  border-radius: 4px;
}

.toggle-btn.active {
  background: #0d1b7a;
  color: #ffffff;
}

/* Content Area */
.content-wrapper { max-width: 1000px; margin: 0 auto 50px auto; padding: 0 20px; }

/* Table Component */
.table-card { background: #0d1b7a; border-radius: 12px; padding: 24px; color: white; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
.custom-table { width: 100%; border-collapse: collapse; color: white; }
.custom-table th { text-align: left; font-size: 18px; font-weight: bold; padding: 16px; border-bottom: 2px solid rgba(255,255,255,0.2); }
.custom-table td { padding: 16px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.1); }

/* Alignments */
.text-center { text-align: center !important; }

.btn-detail { 
  background: none; 
  border: none; 
  color: #93c5fd; 
  text-decoration: underline; 
  cursor: pointer; 
  font-size: 14px; 
  padding: 0;
  display: inline-block;
}
.btn-detail:hover { color: #ffffff; }
.no-data { padding: 30px; }
.loading-text, .error-text { text-align: center; padding: 40px; color: white; font-size: 14px; }
.error-text { color: #fca5a5; }

/* Diagram Layout */
.charts-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.chart-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  border: 1px solid #e2e8f0;
}

.chart-card h3 {
  margin: 0 0 15px 0;
  font-size: 16px;
  font-weight: 700;
  color: #0d1b7a;
}

.chart-box {
  height: 280px;
  position: relative;
}

/* Modal Styling */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal-card { background: white; width: 92%; max-width: 850px; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3); }
.modal-header { background: #0d1b7a; color: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; }
.btn-close { background: none; border: none; color: white; font-size: 20px; cursor: pointer; }
.modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }

/* Modal Detail Grid */
.detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.detail-item { display: flex; flex-direction: column; background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0; }
.detail-item label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 3px; }
.detail-item span { font-size: 13px; color: #0f172a; font-weight: 600; word-break: break-word; }

@media (max-width: 768px) {
  .detail-grid { grid-template-columns: repeat(1, 1fr); }
}
</style>