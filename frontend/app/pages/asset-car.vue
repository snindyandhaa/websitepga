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

    <!-- Banner Header -->
    <div class="hero-banner-container">
      <img src="/foto-karyawan.png" alt="Pabrik Header" class="hero-banner-img" />
    </div>

    <!-- Main Content Area -->
    <main class="content-container">
      <div class="filter-card">
        <div class="search-bar-row">
          <div class="search-input-group">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Kata Kunci: [Location] [Department] [License No] [Merek]" 
              class="input-field"
            />
            <button class="btn-search-icon" aria-label="Cari">🔍</button>
          </div>
          <div class="view-toggle">
            <button 
              :class="['toggle-btn', { active: viewMode === 'data' }]" 
              @click="viewMode = 'data'">
              📋 Data Tabel
            </button>
            <button 
              :class="['toggle-btn', { active: viewMode === 'diagram' }]" 
              @click="viewMode = 'diagram'">
              📊 Diagram (Pie)
            </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="table-card">
        <div class="status-msg">
          Memuat data Asset Car dari Google Sheets...
        </div>
      </div>
      <div v-else-if="errorMessage" class="table-card">
        <div class="status-error">
          {{ errorMessage }}
        </div>
      </div>

      <template v-else>
        <!-- TAMPILAN 1: DATA TABEL -->
        <div v-if="viewMode === 'data'" class="table-card">
          <table class="main-table">
            <thead>
              <tr>
                <th class="col-no center">No</th>
                <th class="col-location">Location</th>
                <th class="col-department">Department</th>
                <th class="col-license">License No</th>
                <th class="col-aksi center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in filteredAssets" :key="index" class="table-row">
                <td class="text-data center">{{ index + 1 }}</td>
                <td class="text-data">{{ getVal(item, ['LOCATION', 'Location', 'Lokasi']) }}</td>
                <td class="text-data">{{ getVal(item, ['DEPARTMENT', 'Department', 'Departemen']) }}</td>
                <td class="text-data"><strong>{{ getVal(item, ['LICENSE NO', 'License No', 'LICENSE', 'No Polisi']) }}</strong></td>
                <td class="col-aksi center">
                  <button class="btn-action" @click="openModal(item)">
                    Lihat Detail
                  </button>
                </td>
              </tr>
              <tr v-if="filteredAssets.length === 0">
                <td colspan="5" class="empty-data">
                  Data aset mobil tidak ditemukan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- TAMPILAN 2: DIAGRAM (PIE CHART) -->
        <div v-if="viewMode === 'diagram'" class="charts-grid">
          <div v-for="cat in chartCategories" :key="cat.title" class="chart-card">
            <div class="chart-card-header">
              <h3>{{ cat.title }}</h3>
              <div class="chart-meta-info">
                <span>Total: <strong>{{ getSummaryStats(cat.keys).total }} Unit</strong></span>
                <span class="dot">•</span>
                <span>Terbanyak: <strong>{{ getSummaryStats(cat.keys).topCategory }} ({{ getSummaryStats(cat.keys).topCount }})</strong></span>
              </div>
            </div>

            <div class="chart-box">
              <Pie :data="getPieChartData(cat.keys)" :options="pieOptions" />
            </div>
          </div>
        </div>
      </template>
    </main>

    <!-- Modal Popup Detail Asset Car -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-box">
        <div class="modal-header-blue">
          <h2>Detail Asset Car</h2>
          <button class="btn-close-x" @click="closeModal">&times;</button>
        </div>

        <div v-if="selectedAsset" class="modal-body-grid">
          <div class="info-card">
            <span class="info-label">LICENSE NO</span>
            <span class="info-value">{{ getVal(selectedAsset, ['LICENSE NO', 'License No', 'LICENSE', 'No Polisi']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">YEAR MADE</span>
            <span class="info-value">{{ getVal(selectedAsset, ['YEAR MADE', 'Year Made', 'Tahun']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">COMPANY / STATUS</span>
            <span class="info-value">{{ getVal(selectedAsset, ['COMPANY', 'Company', 'Perusahaan']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">DEPARTMENT</span>
            <span class="info-value">{{ getVal(selectedAsset, ['DEPARTMENT', 'Department', 'Departemen']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">LOCATION</span>
            <span class="info-value">{{ getVal(selectedAsset, ['LOCATION', 'Location', 'Lokasi']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">FUEL</span>
            <span class="info-value">{{ getVal(selectedAsset, ['FUEL', 'Fuel', 'Bahan Bakar']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">MEREK</span>
            <span class="info-value">{{ getVal(selectedAsset, ['MEREK', 'Merek', 'Brand']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">TYPE-1</span>
            <span class="info-value">{{ getVal(selectedAsset, ['TYPE-1', 'Type-1', 'Tipe']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">WARNA</span>
            <span class="info-value">{{ getVal(selectedAsset, ['WARNA', 'Warna', 'Color']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">NAME OF USER</span>
            <span class="info-value">{{ getVal(selectedAsset, ['NAME OF THE USER', 'NAME OF USER', 'Name of User', 'User']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">MASA BERLAKU PAJAK STNK</span>
            <span class="info-value">{{ formatDate(getVal(selectedAsset, ['Masa Berlaku PAJAK STNK', 'MASA BERLAKU PAJAK STNK', 'Pajak STNK'])) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">NAMA STNK</span>
            <span class="info-value">{{ getVal(selectedAsset, ['Nama STNK', 'NAMA STNK']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">NOMOR MESIN</span>
            <span class="info-value">{{ getVal(selectedAsset, ['Nomor Mesin', 'NOMOR MESIN']) }}</span>
          </div>
          <div class="info-card">
            <span class="info-label">NOMOR RANGKA</span>
            <span class="info-value">{{ getVal(selectedAsset, ['Nomor Rangka', 'NOMOR RANGKA']) }}</span>
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

const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gidAssetCar = '117909656'

const chartColors = [
  '#2563eb', '#10b981', '#f59e0b', '#ef4444', 
  '#8b5cf6', '#06b6d4', '#ec4899', '#6366f1', 
  '#14b8a6', '#f97316', '#84cc16', '#a855f7',
  '#64748b', '#0284c7', '#d97706', '#dc2626'
]

const chartCategories = [
  { title: 'Department', keys: ['DEPARTMENT', 'Department', 'Departemen'] },
  { title: 'Location', keys: ['LOCATION', 'Location', 'Lokasi'] },
  { title: 'Year Made', keys: ['YEAR MADE', 'Year Made', 'Tahun'] },
  { title: 'Merek', keys: ['MEREK', 'Merek', 'Brand'] },
  { title: 'Type-1', keys: ['TYPE-1', 'Type-1', 'Tipe'] },
  { title: 'Fuel', keys: ['FUEL', 'Fuel', 'Bahan Bakar'] }
]

const pieOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'right',
      labels: {
        generateLabels: (chart) => {
          const data = chart.data
          if (data.labels?.length && data.datasets?.length) {
            return data.labels.map((label, i) => {
              const value = data.datasets[0].data[i]
              const meta = chart.getDatasetMeta(0)
              const style = meta.controller.getStyle(i)
              return {
                text: `${label} (${value})`,
                fillStyle: style.backgroundColor,
                strokeStyle: style.borderColor,
                lineWidth: style.borderWidth,
                hidden: !chart.isDatasetVisible(0) || meta.data[i]?.hidden,
                index: i
              }
            })
          }
          return []
        }
      }
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
      result.push(entry.replace(/^"|"$/g, '').trim())
      entry = ''
    } else {
      entry += char
    }
  }
  result.push(entry.replace(/^"|"$/g, '').trim())
  return result
}

const loadExcelData = async () => {
  loading.value = true
  errorMessage.value = ''

  const csvUrl = `https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gidAssetCar}`

  try {
    const csvText = await $fetch(csvUrl, { responseType: 'text' })

    if (!csvText || csvText.includes('<!DOCTYPE html>')) {
      throw new Error('Gagal mengambil data dari Google Sheets. Pastikan akses Google Sheets publik.')
    }

    let cleanCsv = csvText
    if (cleanCsv.startsWith('\uFEFF')) {
      cleanCsv = cleanCsv.slice(1)
    }

    const lines = cleanCsv.split(/\r?\n/).filter(l => l.trim() !== '')
    if (lines.length < 2) {
      assetList.value = []
      return
    }

    const rawHeaders = lines.shift() || ''
    const headers = parseCsvLine(rawHeaders).map(h => h.replace(/^\uFEFF/, '').trim())

    const parsedData = []

    lines.forEach(line => {
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

const filteredAssets = computed(() => {
  if (!searchQuery.value) return assetList.value

  const q = searchQuery.value.toLowerCase().trim()
  return assetList.value.filter(item => {
    const location = String(getVal(item, ['LOCATION', 'Location', 'Lokasi'])).toLowerCase()
    const dept = String(getVal(item, ['DEPARTMENT', 'Department', 'Departemen'])).toLowerCase()
    const licenseNo = String(getVal(item, ['LICENSE NO', 'License No', 'LICENSE', 'No Polisi'])).toLowerCase()
    const brand = String(getVal(item, ['MEREK', 'Merek', 'Brand'])).toLowerCase()

    return location.includes(q) || dept.includes(q) || licenseNo.includes(q) || brand.includes(q)
  })
})

const getPieChartData = (columnKeys) => {
  const counts = {}
  filteredAssets.value.forEach(item => {
    const rawVal = getVal(item, columnKeys)
    const val = rawVal !== '-' ? String(rawVal).trim() : 'Lainnya'
    counts[val] = (counts[val] || 0) + 1
  })

  const isYear = columnKeys.some(k => k.toLowerCase().includes('year') || k.toLowerCase().includes('tahun'))

  const sortedKeys = Object.keys(counts).sort((a, b) => {
    if (isYear) {
      const matchA = a.match(/\d+/)
      const matchB = b.match(/\d+/)
      if (matchA && matchB) {
        return parseInt(matchA[0], 10) - parseInt(matchB[0], 10)
      }
    }
    return (counts[b] || 0) - (counts[a] || 0)
  })

  return {
    labels: sortedKeys,
    datasets: [{
      label: 'Jumlah Unit',
      data: sortedKeys.map(key => counts[key]),
      backgroundColor: chartColors.slice(0, sortedKeys.length)
    }]
  }
}

const getSummaryStats = (columnKeys) => {
  const counts = {}
  let total = 0

  filteredAssets.value.forEach(item => {
    const rawVal = getVal(item, columnKeys)
    const val = rawVal !== '-' ? String(rawVal).trim() : 'Lainnya'
    counts[val] = (counts[val] || 0) + 1
    total++
  })

  const sortedByCount = Object.keys(counts).sort((a, b) => (counts[b] || 0) - (counts[a] || 0))

  return {
    total,
    topCategory: sortedByCount[0] || '-',
    topCount: counts[sortedByCount[0] || ''] || 0
  }
}

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
.page-wrapper {
  min-height: 100vh;
  background-color: #f1f5f9;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  width: 100%;
  box-sizing: border-box;
}

/* Navbar */
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
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
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

/* Banner */
.hero-banner-container {
  width: 100%;
  height: 320px;
  overflow: hidden;
  position: relative;
}

.hero-banner-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

/* Content Wrapper */
.content-container {
  max-width: 1200px;
  margin: -45px auto 40px auto;
  padding: 0 16px;
  position: relative;
  z-index: 20;
}

/* Filter & View Toggle Card */
.filter-card {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  margin-bottom: 24px;
  padding: 16px;
}

.search-bar-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.search-input-group {
  display: flex;
  flex: 1;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  overflow: hidden;
  min-width: 280px;
}

.input-field {
  width: 100%;
  padding: 12px 18px;
  border: none;
  background: transparent;
  outline: none;
  font-size: 14px;
  color: #374151;
}

.btn-search-icon {
  background: transparent;
  border: none;
  padding: 12px 18px;
  color: #0d1b7a;
  cursor: pointer;
  font-size: 16px;
}

/* Toggle Mode Data / Diagram */
.view-toggle {
  display: flex;
  background: #f1f5f9;
  border-radius: 8px;
  padding: 4px;
  border: 1px solid #cbd5e1;
}

.toggle-btn {
  border: none;
  background: transparent;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.toggle-btn.active {
  background: #0d1b7a;
  color: #ffffff;
}

/* Table Card */
.table-card {
  background: #ffffff;
  border-radius: 8px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.main-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px;
}

.main-table thead {
  background-color: #0d1b7a;
  color: #ffffff;
}

.main-table th {
  padding: 14px 16px;
  font-size: 14px;
  font-weight: 600;
  text-align: left;
}

.table-row {
  background-color: #ffffff;
  border-bottom: 1px solid #e2e8f0;
}

.table-row:hover {
  background-color: #f8fafc;
}

.main-table td {
  padding: 12px 16px;
}

.text-data {
  color: #1e293b;
  font-size: 14px;
  font-weight: 500;
}

.center {
  text-align: center;
}

.col-no { width: 5%; text-align: center; }
.col-location { width: 28%; }
.col-department { width: 32%; }
.col-license { width: 25%; }
.col-aksi { width: 10%; text-align: center; }

/* Tombol Aksi "Lihat Detail" */
.btn-action {
  background-color: #ffffff;
  color: #1e293b;
  border: 1px solid #cbd5e1;
  padding: 6px 12px;
  border-radius: 4px;
  font-weight: 600;
  font-size: 12px;
  cursor: pointer;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  transition: background 0.2s;
}

.btn-action:hover {
  background-color: #f1f5f9;
}

.empty-data {
  text-align: center;
  padding: 32px;
  color: #94a3b8;
  background-color: #ffffff;
}

/* Charts Grid */
.charts-grid {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.chart-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  border: 2px solid #e2e8f0;
}

.chart-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
  gap: 8px;
}

.chart-card-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 800;
  color: #0d1b7a;
}

.chart-meta-info {
  font-size: 13px;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 8px;
}

.chart-meta-info strong {
  color: #0f172a;
}

.dot { color: #cbd5e1; }

.chart-box {
  position: relative;
  width: 100%;
  height: 320px;
}

/* Modal Styling */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 300;
  padding: 16px;
}

.modal-box {
  background: #ffffff;
  width: 100%;
  max-width: 800px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
}

.modal-header-blue {
  background-color: #0d1b7a;
  padding: 18px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #ffffff;
}

.modal-header-blue h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
}

.btn-close-x {
  background: transparent;
  border: none;
  color: #ffffff;
  font-size: 24px;
  cursor: pointer;
  line-height: 1;
  opacity: 0.8;
  transition: opacity 0.2s;
}

.btn-close-x:hover {
  opacity: 1;
}

.modal-body-grid {
  padding: 24px;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  max-height: 75vh;
  overflow-y: auto;
  background-color: #ffffff;
}

.info-card {
  background-color: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  letter-spacing: 0.5px;
}

.info-value {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  word-break: break-word;
}

.status-msg, .status-error {
  text-align: center;
  padding: 32px;
  color: #64748b;
  background: #ffffff;
  border-radius: 8px;
}
.status-error { color: #ef4444; }

/* Responsive HP (< 768px) */
@media (max-width: 768px) {
  .navbar {
    padding: 12px 16px;
    flex-direction: column;
    gap: 10px;
  }

  .nav-menu {
    flex-wrap: wrap;
    justify-content: center;
  }

  .hero-banner-container {
    height: 180px;
  }

  .search-bar-row {
    flex-direction: column;
    align-items: stretch;
  }

  .modal-body-grid {
    grid-template-columns: 1fr;
  }

  .chart-card-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>