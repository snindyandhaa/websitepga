<template>
  <div class="page-wrapper">
    <!-- Navbar -->
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
      <!-- Search & Multi-Filter Card -->
      <div class="filter-card">
        <!-- Input Search Keyword Utama -->
        <div class="search-input-group">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Kata Kunci: [Nama] [NIK] [Keyword Spesifik]" 
            class="input-field"
          />
          <button class="btn-search-icon" aria-label="Cari">🔍</button>
        </div>

        <!-- Multi Filter Dropdown Row -->
        <div class="dropdown-filter-row">
          <!-- Filter Tahun -->
          <div class="filter-item">
            <label class="filter-label">Tahun</label>
            <select v-model="selectedTahun" class="select-filter">
              <option value="Semua">Semua Tahun</option>
              <option v-for="tahun in availableYears" :key="tahun" :value="tahun">
                {{ tahun }}
              </option>
            </select>
          </div>

          <!-- Filter Bulan -->
          <div class="filter-item">
            <label class="filter-label">Bulan</label>
            <select v-model="selectedBulan" class="select-filter">
              <option value="Semua">Semua Bulan</option>
              <option v-for="(bln, idx) in monthNames" :key="idx" :value="bln">
                {{ bln }}
              </option>
            </select>
          </div>

          <!-- Filter Jenis Rotasi / Status -->
          <div class="filter-item">
            <label class="filter-label">Jenis Rotasi / Status</label>
            <select v-model="selectedRotasi" class="select-filter">
              <option value="Semua">Semua Status / Rotasi</option>
              <option v-for="status in availableStatuses" :key="status" :value="status">
                {{ status }}
              </option>
            </select>
          </div>

          <!-- Tombol Reset Filter -->
          <div class="filter-item reset-item">
            <button @click="resetFilters" class="btn-reset">
              Reset Filter
            </button>
          </div>
        </div>
      </div>

      <!-- Loading / Error States -->
      <div v-if="loading" class="status-msg">Memuat data employee rotation dari Google Sheets...</div>
      <div v-else-if="errorMessage" class="status-error">
        {{ errorMessage }}
      </div>

      <!-- Data Table -->
      <div v-else class="table-card">
        <table class="main-table">
          <thead>
            <tr>
              <th class="col-no">No</th>
              <th class="col-bulan">Bulan</th>
              <th class="col-tahun">Tahun</th>
              <th class="col-nama">Nama</th>
              <th class="col-nik">NIK</th>
              <th class="col-rotasi">Jenis Rotasi</th>
              <th class="col-date">Mutation Date</th>
              <th class="col-aksi">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in filteredEmployees" :key="index" class="table-row">
              <td class="col-no text-data center">{{ index + 1 }}</td>
              <td class="col-bulan text-data">{{ getMonthName(getVal(item, ['Mutation Date', 'MUTATION DATE', 'Tgl Mutasi'])) }}</td>
              <td class="col-tahun text-data center">{{ getYearNumber(getVal(item, ['Mutation Date', 'MUTATION DATE', 'Tgl Mutasi'])) }}</td>
              <td class="col-nama text-data">
                {{ getVal(item, ['Name', 'NAMA', 'Nama Karyawan', 'Nama']) }}
              </td>
              <td class="col-nik text-data">
                {{ getVal(item, ['NIK', 'Pers No', 'Pers.No', 'PersNo']) }}
              </td>
              <td class="col-rotasi text-data">
                <span class="status-badge" :class="getBadgeClass(getVal(item, ['Status', 'STATUS', 'Jenis Rotasi']))">
                  {{ getVal(item, ['Status', 'STATUS', 'Jenis Rotasi', 'Jenis Mutasi']) }}
                </span>
              </td>
              <td class="col-date text-data">
                {{ getVal(item, ['Mutation Date', 'MUTATION DATE', 'Tgl Mutasi']) }}
              </td>
              <td class="col-aksi center">
                <button @click="openModal(item)" class="btn-action">
                  Lihat Detail
                </button>
              </td>
            </tr>
            <tr v-if="filteredEmployees.length === 0">
              <td colspan="8" class="empty-data">Data rotasi/mutasi sesuai filter tidak ditemukan.</td>
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
          <h2>Detail Employee Rotation</h2>
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

const searchQuery = ref('')
const selectedTahun = ref('Semua')
const selectedBulan = ref('Semua')
const selectedRotasi = ref('Semua')

const showModal = ref(false)
const selectedKaryawan = ref<Record<string, any> | null>(null)
const employeeList = ref<Record<string, any>[]>([])
const loading = ref(true)
const errorMessage = ref('')

const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gidMutasi = '484819291'

const monthNames = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
]

const detailKeys = [
  'Name', 'NIK', 'Join Date', 'Mutation Date', 
  'Mutation To / Mutation From', 'KTP', 'Level', 
  'Status', 'Division', 'Cost Center', 'Department', 
  'Position', 'Last Promotion', 'Birth Date', 'Gender'
]

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

const parseDateObj = (dateStr: string): Date | null => {
  if (!dateStr || dateStr === '-') return null
  const parts = dateStr.trim().split(/[\/\-]/)
  if (parts.length === 3) {
    const day = parseInt(parts[0] || '0', 10)
    const month = parseInt(parts[1] || '0', 10) - 1
    const year = parseInt(parts[2] || '0', 10)
    if (!isNaN(day) && !isNaN(month) && !isNaN(year)) {
      return new Date(year, month, day)
    }
  }
  const date = new Date(dateStr)
  return isNaN(date.getTime()) ? null : date
}

const getMonthName = (dateStr: string): string => {
  const d = parseDateObj(dateStr)
  if (!d) return '-'
  return monthNames[d.getMonth()] || '-'
}

const getYearNumber = (dateStr: string): string => {
  const d = parseDateObj(dateStr)
  if (!d) return '-'
  return String(d.getFullYear())
}

const getVal = (item: Record<string, any> | null, keys: string[]) => {
  if (!item) return '-'
  const itemKeys = Object.keys(item)
  
  for (const k of keys) {
    const foundKey = itemKeys.find(ik => ik.toLowerCase().replace(/[^a-z0-9]/g, '') === k.toLowerCase().replace(/[^a-z0-9]/g, ''))
    if (foundKey && item[foundKey] !== undefined && item[foundKey] !== null && String(item[foundKey]).trim() !== '') {
      return item[foundKey]
    }
  }
  return '-'
}

// Opsi Tahun Otomatis Muncul Berdasarkan Data Google Sheets
const availableYears = computed(() => {
  const yearsSet = new Set<string>()
  employeeList.value.forEach(item => {
    const yr = getYearNumber(String(getVal(item, ['Mutation Date', 'MUTATION DATE', 'Tgl Mutasi'])))
    if (yr && yr !== '-') yearsSet.add(yr)
  })
  return Array.from(yearsSet).sort((a, b) => Number(b) - Number(a))
})

// Opsi Status/Jenis Rotasi Otomatis Muncul Berdasarkan Data
const availableStatuses = computed(() => {
  const statusSet = new Set<string>()
  employeeList.value.forEach(item => {
    const st = String(getVal(item, ['Status', 'STATUS', 'Jenis Rotasi'])).trim()
    if (st && st !== '-') statusSet.add(st)
  })
  return Array.from(statusSet).sort()
})

const fetchEmployeeRotation = async () => {
  loading.value = true
  errorMessage.value = ''

  const csvUrl = `https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gidMutasi}`

  try {
    const csvText = await $fetch<string>(csvUrl, { responseType: 'text' })

    if (!csvText || csvText.includes('<!DOCTYPE html>')) {
      throw new Error('Gagal mengambil data dari Google Sheets. Pastikan spreadsheet publik.')
    }

    const lines = csvText.split('\n').map(l => l.replace('\r', ''))
    if (lines.length < 2) {
      employeeList.value = []
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

    employeeList.value = parsedData

  } catch (err: any) {
    console.error('Error fetching employee rotation data:', err)
    errorMessage.value = err.message || 'Gagal terhubung ke Google Sheets.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchEmployeeRotation()
})

// LOGIKA DUAL FILTER: SEARCH BAR + DROP-DOWN MULTI-FILTER (Tahun, Bulan, Status/Resign)
const filteredEmployees = computed(() => {
  return employeeList.value.filter(item => {
    const rawDate = String(getVal(item, ['Mutation Date', 'MUTATION DATE', 'Tgl Mutasi']))
    const blnVal = getMonthName(rawDate)
    const thnVal = getYearNumber(rawDate)
    const rotasiVal = String(getVal(item, ['Status', 'STATUS', 'Jenis Rotasi'])).trim()

    // 1. Filter Tahun Dropdown
    if (selectedTahun.value !== 'Semua' && thnVal !== selectedTahun.value) {
      return false
    }

    // 2. Filter Bulan Dropdown
    if (selectedBulan.value !== 'Semua' && blnVal !== selectedBulan.value) {
      return false
    }

    // 3. Filter Jenis Rotasi / Status Dropdown (Resign, Mutasi, Promosi, dll)
    if (selectedRotasi.value !== 'Semua' && rotasiVal.toLowerCase() !== selectedRotasi.value.toLowerCase()) {
      return false
    }

    // 4. Input Search Query Keyword (Mencari Nama/NIK/dll)
    const q = searchQuery.value.toLowerCase().trim()
    if (q) {
      const nama = String(getVal(item, ['Name', 'NAMA', 'Nama Karyawan', 'Nama'])).toLowerCase()
      const nik = String(getVal(item, ['NIK', 'Pers No', 'Pers.No', 'PersNo'])).toLowerCase()
      const statusText = rotasiVal.toLowerCase()
      
      const matchQuery = nama.includes(q) || nik.includes(q) || statusText.includes(q) || rawDate.toLowerCase().includes(q)
      if (!matchQuery) return false
    }

    return true
  })
})

const resetFilters = () => {
  searchQuery.value = ''
  selectedTahun.value = 'Semua'
  selectedBulan.value = 'Semua'
  selectedRotasi.value = 'Semua'
}

const getBadgeClass = (status: string) => {
  const s = status.toLowerCase()
  if (s.includes('resign') || s.includes('keluar')) return 'badge-danger'
  if (s.includes('promosi') || s.includes('promote')) return 'badge-success'
  if (s.includes('mutasi') || s.includes('rotasi')) return 'badge-info'
  return 'badge-default'
}

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

/* Content */
.content-container {
  max-width: 1200px;
  margin: -45px auto 40px auto;
  padding: 0 16px;
  position: relative;
  z-index: 20;
}

/* Multi Filter Card */
.filter-card {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  margin-bottom: 24px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.search-input-group {
  display: flex;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  overflow: hidden;
}

.input-field {
  width: 100%;
  padding: 12px 16px;
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

/* Dropdown Filters Row */
.dropdown-filter-row {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  align-items: flex-end;
}

.filter-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
  min-width: 160px;
}

.filter-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.select-filter {
  padding: 10px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background-color: #ffffff;
  color: #1e293b;
  font-size: 13px;
  font-weight: 600;
  outline: none;
  cursor: pointer;
}

.select-filter:focus {
  border-color: #0d1b7a;
}

.reset-item {
  flex: 0 0 auto;
  min-width: auto;
}

.btn-reset {
  background-color: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
  padding: 10px 16px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-reset:hover {
  background-color: #fee2e2;
  color: #dc2626;
  border-color: #fca5a5;
}

/* Table */
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
.col-bulan { width: 12%; }
.col-tahun { width: 8%; text-align: center; }
.col-nama { width: 25%; }
.col-nik { width: 15%; }
.col-rotasi { width: 13%; }
.col-date { width: 12%; }
.col-aksi { width: 10%; text-align: center; }

/* Status Badges */
.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
}

.badge-danger { background: #fee2e2; color: #dc2626; }
.badge-success { background: #d1fae5; color: #059669; }
.badge-info { background: #e0f2fe; color: #0284c7; }
.badge-default { background: #f1f5f9; color: #475569; }

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

/* Modal */
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
  .dropdown-filter-row {
    flex-direction: column;
  }
  .filter-item {
    width: 100%;
  }
}
</style>