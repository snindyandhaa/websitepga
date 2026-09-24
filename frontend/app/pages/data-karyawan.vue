<template>
  <div class="page-wrapper">
    <!-- Navbar (Persis Seperti Home, Tanpa Logout) -->
    <header class="navbar">
      <div class="brand">
        <NuxtLink to="/home">
          <img src="/logo.png" alt="Logo" class="nav-logo" />
        </NuxtLink>
      </div>

      <nav class="nav-menu">
        <div class="dropdown">
          <button class="dropdown-btn">Data MP ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/data-karyawan">Data Karyawan</NuxtLink>
            <NuxtLink to="/visualisasi-data">Visualisasi Data</NuxtLink>
            <NuxtLink to="/employee-rotation">Employee Rotation</NuxtLink>
          </div>
        </div>

        <div class="dropdown">
          <button class="dropdown-btn">General Affair Asset ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/asset-car">Asset Car</NuxtLink>
            <NuxtLink to="/asset-pga">Asset PGA</NuxtLink>
          </div>
        </div>

        <div class="dropdown">
          <button class="dropdown-btn">Asuransi ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/reliance">Reliance</NuxtLink>
            <NuxtLink to="/bpjs">BPJS</NuxtLink>
          </div>
        </div>

        <div class="dropdown">
          <button class="dropdown-btn">Recruitment ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/data-summary">Data Summary</NuxtLink>
          </div>
        </div>

        <div class="dropdown">
          <button class="dropdown-btn">More ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/WIPHRA">Work In Progress HC & GA</NuxtLink>
            <NuxtLink to="/budget-department">Budget Department</NuxtLink>
          </div>
        </div>
      </nav>
    </header>

    <!-- Banner Foto Karyawan -->
    <div class="hero-banner-container">
      <img src="/foto-karyawan.png" alt="Foto Karyawan" class="hero-banner-img" />
    </div>

    <!-- Main Content Area -->
    <main class="content-container">
      <!-- Search & Filter Card Overlay -->
      <div class="search-filter-card">
        <div class="filter-dropdown">
          <select v-model="selectedWilayah" class="select-field" @change="loadKaryawanData">
            <option value="Semua">Semua Wilayah</option>
            <option value="MP - CPI Sulawesi Banumapa">MP - CPI Sulawesi Banumapa</option>
            <option value="MP - CPI Kalimantan">MP - CPI Kalimantan</option>
          </select>
          <span class="arrow-icon">▼</span>
        </div>

        <div class="search-input-group">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Kata Kunci: [Nama] [NIK]" 
            class="input-field"
          />
          <button class="btn-search-icon" aria-label="Cari">
            🔍
          </button>
        </div>
      </div>

      <!-- Loading / Error States -->
      <div v-if="loading" class="status-msg">Memuat data karyawan dari Google Sheets...</div>
      <div v-else-if="errorMessage" class="status-error">
        {{ errorMessage }}
      </div>

      <!-- Data Table -->
      <div v-else class="table-card">
        <table class="main-table">
          <thead>
            <tr>
              <th class="col-nama">Nama</th>
              <th class="col-nik">NIK</th>
              <th class="col-aksi">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in filteredKaryawan" :key="index" class="table-row">
              <td class="col-nama text-data">
                {{ getVal(item, ['Nama', 'NAMA', 'Name']) }}
              </td>
              <td class="col-nik text-data">
                {{ getVal(item, ['NIK', 'NIP', 'Pers No', 'No ID']) }}
              </td>
              <td class="col-aksi">
                <button @click="openModal(item)" class="btn-action">
                  Lihat Detail
                </button>
              </td>
            </tr>
            <tr v-if="filteredKaryawan.length === 0">
              <td colspan="3" class="empty-data">Data karyawan tidak ditemukan.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- Modal Detail Karyawan -->
    <div v-if="showModal && selectedKaryawan" class="modal-overlay" @click.self="closeModal">
      <div class="modal-box">
        <!-- Header Modal Biru Tua -->
        <div class="modal-header-blue">
          <h2>Detail Data Karyawan</h2>
          <button @click="closeModal" class="btn-close-x">&times;</button>
        </div>

        <!-- Body Modal dengan Card Grid -->
        <div class="modal-body-grid">
          <div v-for="key in detailKeys" :key="key" class="info-card">
            <span class="info-label">{{ key.toUpperCase() }}</span>
            <span class="info-value">{{ getVal(selectedKaryawan, [key]) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const selectedWilayah = ref('Semua')
const searchQuery = ref('')
const showModal = ref(false)
const selectedKaryawan = ref<Record<string, any> | null>(null)
const karyawanList = ref<Record<string, any>[]>([])
const loading = ref(true)
const errorMessage = ref('')

const detailKeys = [
  'Nama', 'NIK', 'Company', 'Divisi', 'Cost Center', 
  'Join Date', 'Level', 'Birth Date', 'Last Promotion', 
  'Masa Bakti', 'Umur', 'Gender', 'Major', 'Generation', 
  'KTP', 'Education', 'DEPARTMENT'
]

// Spreadsheet ID & GID Map
const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gids: Record<string, string> = {
  'Semua': '0',
  'MP - CPI Sulawesi Banumapa': '1627431459',
  'MP - CPI Kalimantan': '936973334'
}

const parseCsvLine = (text: string): string[] => {
  const result: string[] = []
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

const loadKaryawanData = async () => {
  loading.value = true
  errorMessage.value = ''

  const gid = gids[selectedWilayah.value] || '0'
  const csvUrl = `https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gid}`

  try {
    const csvText = await $fetch<string>(csvUrl, { responseType: 'text' })

    if (!csvText || csvText.includes('<!DOCTYPE html>')) {
      throw new Error('Gagal mengambil data dari Google Sheets. Pastikan spreadsheet di-share "Anyone with the link".')
    }

    const lines = csvText.split('\n').map(l => l.replace('\r', ''))
    if (lines.length < 2) {
      karyawanList.value = []
      return
    }

    const rawHeaders = lines.shift() || ''
    const headers = parseCsvLine(rawHeaders).map(h => h.trim())

    const parsedData: Record<string, any>[] = []

    lines.forEach(line => {
      if (line.trim() === '') return
      const rowValues = parseCsvLine(line)
      const item: Record<string, any> = {}
      
      headers.forEach((header, idx) => {
        if (header) {
          item[header] = rowValues[idx] ? rowValues[idx].trim() : ''
        }
      })

      if (Object.values(item).some(v => v !== '')) {
        parsedData.push(item)
      }
    })

    karyawanList.value = parsedData

  } catch (err: any) {
    console.error('Error fetching data karyawan:', err)
    errorMessage.value = err.message || 'Gagal terhubung ke Google Sheets.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadKaryawanData()
})

const getVal = (item: Record<string, any> | null, keys: string[]) => {
  if (!item) return '-'
  const itemKeys = Object.keys(item)
  
  for (const k of keys) {
    const foundKey = itemKeys.find(ik => ik.toLowerCase().trim() === k.toLowerCase().trim())
    if (foundKey && item[foundKey] !== undefined && item[foundKey] !== null && String(item[foundKey]).trim() !== '') {
      return item[foundKey]
    }
  }
  return '-'
}

const filteredKaryawan = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()

  if (!q) return karyawanList.value

  return karyawanList.value.filter(item => {
    const nama = getVal(item, ['Nama', 'NAMA', 'Name']).toLowerCase()
    const nik = getVal(item, ['NIK', 'NIP', 'Pers No', 'No ID']).toLowerCase()
    return nama.includes(q) || nik.includes(q)
  })
})

const openModal = (item: Record<string, any>) => {
  selectedKaryawan.value = item
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedKaryawan.value = null
}
</script>

<style scoped>
.page-wrapper {
  min-height: 100vh;
  background-color: #f1f5f9;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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

/* Hero Banner Full-width */
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

/* Content Area & Search Card Overlay */
.content-container {
  max-width: 1200px;
  margin: -45px auto 40px auto;
  padding: 0 16px;
  position: relative;
  z-index: 20;
}

.search-filter-card {
  display: flex;
  align-items: stretch;
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  margin-bottom: 24px;
  overflow: hidden;
}

.filter-dropdown {
  position: relative;
  background-color: #0d1b7a;
  width: 300px;
  display: flex;
  align-items: center;
  align-self: stretch;
  border-top-left-radius: 8px;
  border-bottom-left-radius: 8px;
}

.select-field {
  width: 100%;
  height: 100%;
  padding: 14px 32px 14px 16px;
  background: transparent;
  color: #ffffff;
  font-weight: 600;
  font-size: 14px;
  border: none;
  outline: none;
  appearance: none;
  cursor: pointer;
}

.select-field option {
  background-color: #ffffff;
  color: #1f2937;
}

.arrow-icon {
  position: absolute;
  right: 12px;
  color: #ffffff;
  font-size: 10px;
  pointer-events: none;
}

.search-input-group {
  display: flex;
  flex: 1;
  align-items: center;
  background: #ffffff;
}

.input-field {
  width: 100%;
  padding: 14px 18px;
  border: none;
  outline: none;
  font-size: 14px;
  color: #374151;
}

.btn-search-icon {
  background: transparent;
  border: none;
  padding: 14px 20px;
  color: #0d1b7a;
  cursor: pointer;
  font-size: 18px;
}

/* Table Design */
.table-card {
  background: #ffffff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.main-table {
  width: 100%;
  border-collapse: collapse;
}

.main-table thead {
  background-color: #0d1b7a;
  color: #ffffff;
}

.main-table th {
  padding: 18px 24px;
  font-size: 18px;
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
  padding: 16px 24px;
}

.text-data {
  color: #1e293b;
  font-size: 15px;
  font-weight: 500;
}

.col-nama { width: 45%; }
.col-nik { width: 35%; }

th.col-aksi,
td.col-aksi {
  width: 20%;
  text-align: center;
}

.btn-action {
  background-color: #ffffff;
  color: #1e293b;
  border: 1px solid #cbd5e1;
  padding: 6px 16px;
  border-radius: 4px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  transition: background 0.2s;
  display: inline-block;
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

/* Custom Modal Design */
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
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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

@media (max-width: 640px) {
  .modal-body-grid {
    grid-template-columns: 1fr;
  }
}
</style>