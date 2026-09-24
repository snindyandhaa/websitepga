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
      <img src="/pabrik.jpeg" alt="Pabrik Header" class="hero-bg" />
    </section>

    <!-- Search Box Overlay & Switch Mode (Data / Diagram) -->
    <div class="search-section">
      <div class="search-bar-row">
        <!-- Input & Filter Box -->
        <div class="search-box-container">
          <select v-model="selectedWilayah" class="select-wilayah">
            <option value="Semua">Semua Wilayah</option>
            <option value="MP - CPI Wilayah Timur">MP - CPI Wilayah Timur</option>
            <option value="MP - CPI Sulawesi Banumapa">MP - CPI Sulawesi Banumapa</option>
            <option value="MP - CPI Kalimantan">MP - CPI Kalimantan</option>
          </select>
          <div class="input-wrapper">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Kata Kunci: [asset] [description]" 
              class="input-search"
            />
            <button class="btn-search">🔍</button>
          </div>
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
      <div v-if="loading" class="loading-state">
        Memuat data Asset PGA dari Laravel Backend...
      </div>

      <div v-else-if="errorMessage" class="error-state">
        {{ errorMessage }}
      </div>

      <template v-else>
        <!-- TAMPILAN 1: TABEL DATA -->
        <div v-if="viewMode === 'data'" class="table-card">
          <table class="custom-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Asset</th>
                <th>Cap.date</th>
                <th>Asset Description</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in filteredAssets" :key="index">
                <td>{{ getVal(item, ['No', 'NO']) || (index + 1) }}</td>
                <td><strong>{{ getVal(item, ['Asset', 'ASSET']) }}</strong></td>
                <td>{{ formatDate(getVal(item, ['Cap.date', 'CAP.DATE', 'Cap Date'])) }}</td>
                <td>{{ getVal(item, ['Asset description', 'ASSET DESCRIPTION', 'Description']) }}</td>
                <td class="text-center">
                  <button class="btn-detail" @click="openModal(item)">Lihat detail</button>
                </td>
              </tr>
              <tr v-if="filteredAssets.length === 0">
                <td colspan="5" class="text-center no-data">Data aset PGA tidak ditemukan.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- TAMPILAN 2: DIAGRAM LINGKARAN (PIE CHART) -->
        <div v-if="viewMode === 'diagram'" class="charts-grid">
          <div class="chart-card">
            <h3>Distribusi Kategori Asset PGA</h3>
            <div class="chart-box">
              <Pie :data="getPieChartData(['Asset description', 'ASSET DESCRIPTION', 'Description'])" :options="pieOptions" />
            </div>
          </div>
        </div>
      </template>
    </main>

    <!-- Modal Popup Detail Asset PGA -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-card">
        <div class="modal-header">
          <h3>Detail Asset PGA</h3>
          <button class="btn-close" @click="closeModal">✕</button>
        </div>

        <div class="modal-body" v-if="selectedAsset">
          <div class="detail-grid">
            <div class="detail-item">
              <label>No</label>
              <span>{{ getVal(selectedAsset, ['No', 'NO']) }}</span>
            </div>
            <div class="detail-item">
              <label>Asset Code</label>
              <span>{{ getVal(selectedAsset, ['Asset', 'ASSET']) }}</span>
            </div>
            <div class="detail-item">
              <label>Cap.date</label>
              <span>{{ formatDate(getVal(selectedAsset, ['Cap.date', 'CAP.DATE', 'Cap Date'])) }}</span>
            </div>
            <div class="detail-item">
              <label>Asset Description</label>
              <span>{{ getVal(selectedAsset, ['Asset description', 'ASSET DESCRIPTION']) }}</span>
            </div>
            <div class="detail-item">
              <label>Acquis.val. (Nilai Perolehan)</label>
              <span>{{ formatCurrency(getVal(selectedAsset, ['Acquis.val.', 'ACQUIS.VAL.', 'Acquis val'])) }}</span>
            </div>
            <div class="detail-item">
              <label>Book val. (Nilai Buku)</label>
              <span>{{ formatCurrency(getVal(selectedAsset, ['Book val.', 'BOOK VAL.', 'Book val'])) }}</span>
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
const selectedWilayah = ref('Semua')
const searchQuery = ref('')
const viewMode = ref('data')

const showModal = ref(false)
const selectedAsset = ref(null)

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

// Fetch dari Laravel Backend Endpoint
const loadExcelData = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const res = await $fetch('http://127.0.0.1:8000/api/sheet/asset-car')

    if (!res || res.status !== 'success' || !Array.isArray(res.data)) {
      throw new Error('Gagal memuat data dari backend Laravel.')
    }

    assetList.value = res.data
  } catch (err) {
    console.error('Gagal mengambil data dari Laravel:', err)
    errorMessage.value = err.message || 'Terjadi kesalahan saat memuat data.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadExcelData()
})

// Helper fleksibel pencarian field
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

const filteredAssets = computed(() => {
  return assetList.value.filter(item => {
    const wilayahVal = getVal(item, ['Wilayah', 'WILAYAH', 'Location', 'Lokasi'])
    const matchesWilayah = selectedWilayah.value === 'Semua' || wilayahVal === selectedWilayah.value
    
    const query = searchQuery.value.toLowerCase()
    const assetCode = String(getVal(item, ['Asset', 'ASSET'])).toLowerCase()
    const desc = String(getVal(item, ['Asset description', 'ASSET DESCRIPTION', 'Description'])).toLowerCase()

    const matchesSearch = assetCode.includes(query) || desc.includes(query)

    return matchesWilayah && matchesSearch
  })
})

const getPieChartData = (keys) => {
  const counts = {}
  filteredAssets.value.forEach(item => {
    const rawVal = getVal(item, keys)
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

const formatDate = (val) => {
  if (!val || val === '-') return '-'
  if (typeof val === 'number') {
    const date = new Date((val - (25567 + 2)) * 86400 * 1000)
    return date.toLocaleDateString('id-ID')
  }
  return val
}

const formatCurrency = (val) => {
  if (!val || val === '-' || isNaN(val)) return '-'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
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
.asset-container {
  background-color: #f8fafc;
  min-height: 100vh;
}

/* Navbar Header */
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
.dropdown { position: relative; }
.dropdown button { background: none; border: none; font-size: 13px; cursor: pointer; color: #333; font-weight: 500; }
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #ffffff;
  min-width: 170px;
  box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
  z-index: 10;
  border-radius: 6px;
  overflow: hidden;
}
.dropdown-content a { color: #333; padding: 10px 14px; text-decoration: none; display: block; font-size: 12px; }
.dropdown-content a:hover { background-color: #f1f5f9; color: #0d1b7a; }
.dropdown:hover .dropdown-content { display: block; }
.btn-logout { background: #ef4444; color: white; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; }

/* Hero Banner */
.hero-section {
  width: 100%;
  height: 250px;
  overflow: hidden;
}
.hero-bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Search Bar & Switch View Row */
.search-section {
  max-width: 1000px;
  margin: -35px auto 25px auto;
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

.search-box-container {
  display: flex;
  background: white;
  border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  overflow: hidden;
  flex: 1;
  min-width: 320px;
}

.select-wilayah {
  background-color: #0d1b7a;
  color: white;
  border: none;
  padding: 12px 16px;
  font-size: 13px;
  outline: none;
  cursor: pointer;
}

.input-wrapper {
  display: flex;
  flex: 1;
}

.input-search {
  flex: 1;
  border: none;
  padding: 12px 16px;
  font-size: 13px;
  outline: none;
}

.btn-search {
  background: white;
  border: none;
  padding: 0 16px;
  cursor: pointer;
  color: #0d1b7a;
}

/* Toggle Switch Data / Diagram */
.view-toggle {
  display: flex;
  background: #ffffff;
  border-radius: 6px;
  padding: 3px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.toggle-btn {
  border: none;
  background: transparent;
  padding: 8px 18px;
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  border-radius: 4px;
}

.toggle-btn.active {
  background: #0d1b7a;
  color: #ffffff;
}

/* Content Wrapper */
.content-wrapper {
  max-width: 1000px;
  margin: 0 auto 50px auto;
  padding: 0 20px;
}

/* Table Card */
.table-card {
  background: #0d1b7a;
  border-radius: 12px;
  padding: 24px;
  color: white;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
  color: white;
}

.custom-table th {
  text-align: left;
  font-size: 18px;
  font-weight: bold;
  padding: 16px;
  border-bottom: 2px solid rgba(255,255,255,0.2);
}

.custom-table td {
  padding: 16px;
  font-size: 14px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.text-center { text-align: center !important; }

.btn-detail {
  background: none;
  border: none;
  color: #93c5fd;
  text-decoration: underline;
  cursor: pointer;
  font-size: 14px;
}

.btn-detail:hover { color: #ffffff; }

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
  font-size: 18px;
  font-weight: 700;
  color: #0d1b7a;
}

.chart-box {
  height: 320px;
  position: relative;
}

.loading-state, .error-state, .no-data {
  padding: 30px;
  text-align: center;
  color: #64748b;
}

.error-state { color: #ef4444; }

/* Modal Popup Styling */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
}

.modal-card {
  background: white;
  width: 90%;
  max-width: 600px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
}

.modal-header {
  background: #0d1b7a;
  color: white;
  padding: 16px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 { margin: 0; font-size: 18px; }

.btn-close {
  background: none;
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
}

.modal-body {
  padding: 24px;
  max-height: 75vh;
  overflow-y: auto;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.detail-item {
  display: flex;
  flex-direction: column;
}

.detail-item label {
  font-size: 11px;
  color: #64748b;
  text-transform: uppercase;
  font-weight: 700;
  margin-bottom: 2px;
}

.detail-item span {
  font-size: 14px;
  color: #1e293b;
  font-weight: 500;
}

@media (max-width: 600px) {
  .detail-grid { grid-template-columns: 1fr; }
}
</style>