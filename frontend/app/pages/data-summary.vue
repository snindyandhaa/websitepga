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

    <!-- Controls Bar (Toggle Switch Data & Search) -->
    <div class="control-bar">
      <div class="top-controls">
        <!-- Toggle Switch Antara 2 Sheet -->
        <div class="view-type-toggle">
          <button 
            :class="['type-btn', { active: activeTab === 'posisi' }]" 
            @click="activeTab = 'posisi'">
            📋 Data Posisi Recruitment
          </button>
          <button 
            :class="['type-btn', { active: activeTab === 'calon' }]" 
            @click="activeTab = 'calon'">
            👥 Data Calon Karyawan CPI Group
          </button>
        </div>

        <!-- Input Pencarian -->
        <div class="search-box">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Cari kata kunci..." 
            class="search-input"
          />
          <span class="search-icon">🔍</span>
        </div>
      </div>

      <!-- Card Filter khusus Tab Data Calon Karyawan -->
      <div v-if="activeTab === 'calon'" class="filter-card" @click.stop>
        <div class="filter-card-header">
          <h3>🔍 Filter Data Calon Karyawan</h3>
          <button @click="resetCalonFilters" class="btn-reset">Reset Filter</button>
        </div>

        <div class="filter-grid">
          <!-- Filter Company -->
          <div class="filter-item">
            <label class="filter-label">COMPANY</label>
            <select v-model="filterCalonCompany" class="filter-select">
              <option value="">Semua Company</option>
              <option v-for="opt in calonOptions.company" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Status -->
          <div class="filter-item">
            <label class="filter-label">STATUS</label>
            <select v-model="filterCalonStatus" class="filter-select">
              <option value="">Semua Status</option>
              <option v-for="opt in calonOptions.status" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Area -->
          <div class="filter-item">
            <label class="filter-label">AREA</label>
            <select v-model="filterCalonArea" class="filter-select">
              <option value="">Semua Area</option>
              <option v-for="opt in calonOptions.area" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Posisi -->
          <div class="filter-item">
            <label class="filter-label">POSISI</label>
            <select v-model="filterCalonPosisi" class="filter-select">
              <option value="">Semua Posisi</option>
              <option v-for="opt in calonOptions.posisi" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Department -->
          <div class="filter-item">
            <label class="filter-label">DEPARTMENT</label>
            <select v-model="filterCalonDept" class="filter-select">
              <option value="">Semua Department</option>
              <option v-for="opt in calonOptions.dept" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Instansi -->
          <div class="filter-item">
            <label class="filter-label">INSTANSI</label>
            <select v-model="filterCalonInstansi" class="filter-select">
              <option value="">Semua Instansi</option>
              <option v-for="opt in calonOptions.instansi" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Gender -->
          <div class="filter-item">
            <label class="filter-label">GENDER</label>
            <select v-model="filterCalonGender" class="filter-select">
              <option value="">Semua Gender</option>
              <option v-for="opt in calonOptions.gender" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Pendidikan -->
          <div class="filter-item">
            <label class="filter-label">PENDIDIKAN</label>
            <select v-model="filterCalonPendidikan" class="filter-select">
              <option value="">Semua Pendidikan</option>
              <option v-for="opt in calonOptions.pendidikan" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Hasil -->
          <div class="filter-item">
            <label class="filter-label">HASIL</label>
            <select v-model="filterCalonHasil" class="filter-select">
              <option value="">Semua Hasil</option>
              <option v-for="opt in calonOptions.hasil" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Bulan (Januari - Desember) -->
          <div class="filter-item">
            <label class="filter-label">BULAN</label>
            <select v-model="filterCalonBulan" class="filter-select">
              <option value="">Semua Bulan</option>
              <option v-for="opt in monthNames" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>

          <!-- Filter Tahun -->
          <div class="filter-item">
            <label class="filter-label">TAHUN</label>
            <select v-model="filterCalonTahun" class="filter-select">
              <option value="">Semua Tahun</option>
              <option v-for="opt in calonOptions.tahun" :key="opt" :value="opt">{{ opt }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <main class="content-container">
      <div v-if="loading" class="status-msg">Memuat data dari Google Sheets...</div>
      <div v-else-if="errorMessage" class="status-error">{{ errorMessage }}</div>

      <template v-else>
        <!-- TABLE 1: DATA POSISI RECRUITMENT -->
        <div v-if="activeTab === 'posisi'" class="table-card">
          <div class="table-title-header">
            <h2>Data Posisi Recruitment</h2>
            <span class="total-badge">Total: {{ filteredPosisiData.length }} Data</span>
          </div>

          <table class="main-table">
            <thead>
              <tr>
                <th class="col-no center">No</th>
                <th>Status</th>
                <th>Posisi</th>
                <th>Company</th>
                <th>Departement</th>
                <th>Area</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in filteredPosisiData" :key="index" class="table-row">
                <td class="col-no text-data center">{{ index + 1 }}</td>
                <td class="text-data center"><span class="status-pill">{{ getVal(item, ['Status', 'STATUS']) }}</span></td>
                <td class="text-data bold">{{ getVal(item, ['Posisi', 'POSISI', 'Position']) }}</td>
                <td class="text-data">{{ getVal(item, ['Company', 'COMPANY', 'Perusahaan']) }}</td>
                <td class="text-data">{{ getVal(item, ['Departement', 'Department', 'DEPARTEMENT', 'Dept']) }}</td>
                <td class="text-data">{{ getVal(item, ['Area', 'AREA', 'Wilayah']) }}</td>
              </tr>
              <tr v-if="filteredPosisiData.length === 0">
                <td colspan="6" class="empty-data">Data posisi recruitment tidak ditemukan.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- TABLE 2: DATA CALON KARYAWAN CPI GROUP -->
        <div v-else-if="activeTab === 'calon'" class="table-card">
          <div class="table-title-header">
            <h2>Data Calon Karyawan CPI Group</h2>
            <span class="total-badge">Total: {{ filteredCalonData.length }} Data</span>
          </div>

          <div class="table-scroll-wrapper">
            <table class="main-table">
              <thead>
                <tr>
                  <th class="col-no center">No</th>
                  <th>CV Masuk</th>
                  <th class="center">Tahun</th>
                  <th class="center">Status</th>
                  <th>Company</th>
                  <th>Area</th>
                  <th>Posisi</th>
                  <th>Departement</th>
                  <th class="center">LVL</th>
                  <th>Nama</th>
                  <th class="center">Usia</th>
                  <th class="center">Gender</th>
                  <th>Pendidikan</th>
                  <th>Instansi</th>
                  <th>Jurusan</th>
                  <th class="center">Lulus Tahun</th>
                  <th class="center">Interview HR</th>
                  <th class="center">Interview User</th>
                  <th class="center">Psikotest</th>
                  <th class="center">Hasil</th>
                  <th>Source</th>
                  <th class="center">Tanggal Hasil</th>
                  <th class="center">Join Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in filteredCalonData" :key="index" class="table-row">
                  <td class="col-no text-data center">{{ index + 1 }}</td>
                  <td class="text-data center">{{ getVal(item, ['CV Masuk', 'Cv Masuk', 'CV_Masuk', 'Tgl CV']) }}</td>
                  <td class="text-data center">{{ getVal(item, ['Tahun', 'TAHUN', 'Year']) }}</td>
                  <td class="text-data center"><span class="status-pill">{{ getVal(item, ['Status', 'STATUS']) }}</span></td>
                  <td class="text-data">{{ getVal(item, ['Company', 'COMPANY', 'Perusahaan']) }}</td>
                  <td class="text-data">{{ getVal(item, ['Area', 'AREA']) }}</td>
                  <td class="text-data bold">{{ getVal(item, ['Posisi', 'POSISI', 'Position']) }}</td>
                  <td class="text-data">{{ getVal(item, ['Departement', 'Department', 'DEPARTEMENT', 'Dept']) }}</td>
                  <td class="text-data center">{{ getVal(item, ['LVL', 'Lvl', 'Level', 'LEVEL']) }}</td>
                  <td class="text-data bold">{{ getVal(item, ['Nama', 'NAMA', 'Name']) }}</td>
                  <td class="text-data center">{{ getVal(item, ['Usia', 'USIA', 'Umur', 'Age']) }}</td>
                  <td class="text-data center">{{ getVal(item, ['Gender', 'GENDER', 'Jenis Kelamin']) }}</td>
                  <td class="text-data">{{ getVal(item, ['Pendidikan', 'PENDIDIKAN', 'Education']) }}</td>
                  <td class="text-data">{{ getVal(item, ['Instansi', 'INSTANSI', 'Universitas', 'Kampus']) }}</td>
                  <td class="text-data">{{ getVal(item, ['Jurusan', 'JURUSAN', 'Major']) }}</td>
                  <td class="text-data center">{{ getVal(item, ['Lulus Tahun', 'LULUS TAHUN', 'Tahun Lulus']) }}</td>
                  <td class="text-data center">
                    <span :class="['boolean-tag', getVal(item, ['Interview HR', 'INTERVIEW HR']).toUpperCase() === 'TRUE' ? 'tag-true' : 'tag-false']">
                      {{ getVal(item, ['Interview HR', 'INTERVIEW HR']) }}
                    </span>
                  </td>
                  <td class="text-data center">
                    <span :class="['boolean-tag', getVal(item, ['Interview User', 'INTERVIEW USER']).toUpperCase() === 'TRUE' ? 'tag-true' : 'tag-false']">
                      {{ getVal(item, ['Interview User', 'INTERVIEW USER']) }}
                    </span>
                  </td>
                  <td class="text-data center">
                    <span :class="['boolean-tag', getVal(item, ['Psikotest', 'PSIKOTEST']).toUpperCase() === 'TRUE' ? 'tag-true' : 'tag-false']">
                      {{ getVal(item, ['Psikotest', 'PSIKOTEST']) }}
                    </span>
                  </td>
                  <td class="text-data center">
                    <span :class="['hasil-badge', getHasilClass(getVal(item, ['Hasil', 'HASIL']))]">
                      {{ getVal(item, ['Hasil', 'HASIL']) }}
                    </span>
                  </td>
                  <td class="text-data">{{ getVal(item, ['Source', 'SOURCE', 'Sumber']) }}</td>
                  <td class="text-data center">{{ getVal(item, ['Tanggal Hasil', 'TANGGAL HASIL', 'Tgl Hasil']) }}</td>
                  <td class="text-data center">{{ getVal(item, ['Join Date', 'JOIN DATE', 'Tanggal Masuk', 'Tgl Masuk']) }}</td>
                </tr>
                <tr v-if="filteredCalonData.length === 0">
                  <td colspan="23" class="empty-data">Data calon karyawan tidak ditemukan.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const activeTab = ref<'posisi' | 'calon'>('posisi')
const searchQuery = ref<string>('')
const loading = ref<boolean>(true)
const errorMessage = ref<string>('')

// Filters khusus Data Calon Karyawan
const filterCalonCompany = ref<string>('')
const filterCalonStatus = ref<string>('')
const filterCalonArea = ref<string>('')
const filterCalonPosisi = ref<string>('')
const filterCalonDept = ref<string>('')
const filterCalonInstansi = ref<string>('')
const filterCalonGender = ref<string>('')
const filterCalonPendidikan = ref<string>('')
const filterCalonHasil = ref<string>('')
const filterCalonBulan = ref<string>('')
const filterCalonTahun = ref<string>('')

const monthNames = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
]

const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gidPosisi = '1537886777' // Sheet Data Posisi Recruitment
const gidCalon = '1410850157'  // Sheet Data Calon Karyawan CPI Group

const posisiList = ref<Record<string, any>[]>([])
const calonList = ref<Record<string, any>[]>([])

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

const getVal = (item: Record<string, any> | null, keys: string[]): string => {
  if (!item) return '-'
  const itemKeys = Object.keys(item)

  for (const k of keys) {
    const foundKey = itemKeys.find(ik => ik.toLowerCase().trim() === k.toLowerCase().trim())
    if (foundKey && item[foundKey] !== undefined && item[foundKey] !== null && String(item[foundKey]).trim() !== '') {
      return String(item[foundKey]).trim()
    }
  }
  return '-'
}

const parseSheetCsv = (csvText: string): Record<string, any>[] => {
  let cleanCsv = csvText
  if (cleanCsv.startsWith('\uFEFF')) {
    cleanCsv = cleanCsv.slice(1)
  }

  const lines = cleanCsv.split(/\r?\n/).filter(l => l.trim() !== '')
  if (lines.length < 2) return []

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

    if (Object.values(item).some(v => v !== '')) {
      parsedData.push(item)
    }
  })

  return parsedData
}

const loadAllData = async (): Promise<void> => {
  loading.value = true
  errorMessage.value = ''

  try {
    const [resPosisi, resCalon] = await Promise.allSettled([
      $fetch<string>(`https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gidPosisi}`, { responseType: 'text' }),
      $fetch<string>(`https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gidCalon}`, { responseType: 'text' })
    ])

    posisiList.value = resPosisi.status === 'fulfilled' ? parseSheetCsv(resPosisi.value) : []
    calonList.value = resCalon.status === 'fulfilled' ? parseSheetCsv(resCalon.value) : []

  } catch (err: any) {
    console.error('Error fetching Data Summary:', err)
    errorMessage.value = 'Gagal terhubung ke Google Sheets.'
  } finally {
    loading.value = false
  }
}

// Extract Bulan dari String Tanggal
const extractBulan = (dateStr: string): string => {
  if (!dateStr || dateStr === '-') return ''
  
  for (const m of monthNames) {
    if (dateStr.toLowerCase().includes(m.toLowerCase())) return m
  }

  const parts = dateStr.split(/[\/\-\.]/)
  if (parts.length >= 2) {
    let monthNum = 0
    if (parts[0].length === 4) {
      monthNum = parseInt(parts[1], 10)
    } else {
      monthNum = parseInt(parts[1], 10)
    }
    if (!isNaN(monthNum) && monthNum >= 1 && monthNum <= 12) {
      return monthNames[monthNum - 1]
    }
  }

  return ''
}

// Opsi Dropdown unik untuk Filter Calon Karyawan
const calonOptions = computed(() => {
  const getUniques = (keys: string[]) => {
    const set = new Set<string>()
    calonList.value.forEach(item => {
      const val = getVal(item, keys)
      if (val && val !== '-') set.add(val)
    })
    return Array.from(set).sort()
  }

  return {
    company: getUniques(['Company', 'COMPANY', 'Perusahaan']),
    status: getUniques(['Status', 'STATUS']),
    area: getUniques(['Area', 'AREA']),
    posisi: getUniques(['Posisi', 'POSISI', 'Position']),
    dept: getUniques(['Departement', 'Department', 'DEPARTEMENT', 'Dept']),
    instansi: getUniques(['Instansi', 'INSTANSI', 'Universitas', 'Kampus']),
    gender: getUniques(['Gender', 'GENDER']),
    pendidikan: getUniques(['Pendidikan', 'PENDIDIKAN', 'Education']),
    hasil: getUniques(['Hasil', 'HASIL']),
    tahun: getUniques(['Tahun', 'TAHUN', 'Year'])
  }
})

const resetCalonFilters = () => {
  filterCalonCompany.value = ''
  filterCalonStatus.value = ''
  filterCalonArea.value = ''
  filterCalonPosisi.value = ''
  filterCalonDept.value = ''
  filterCalonInstansi.value = ''
  filterCalonGender.value = ''
  filterCalonPendidikan.value = ''
  filterCalonHasil.value = ''
  filterCalonBulan.value = ''
  filterCalonTahun.value = ''
}

const filteredPosisiData = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  if (!q) return posisiList.value

  return posisiList.value.filter(item => {
    return Object.values(item).some(val => String(val).toLowerCase().includes(q))
  })
})

const filteredCalonData = computed(() => {
  return calonList.value.filter(item => {
    // 1. Check Search Input
    const q = searchQuery.value.toLowerCase().trim()
    if (q) {
      const matchesQuery = Object.values(item).some(val => String(val).toLowerCase().includes(q))
      if (!matchesQuery) return false
    }

    // 2. Check Individual Filter Dropdowns
    if (filterCalonCompany.value && getVal(item, ['Company', 'COMPANY']) !== filterCalonCompany.value) return false
    if (filterCalonStatus.value && getVal(item, ['Status', 'STATUS']) !== filterCalonStatus.value) return false
    if (filterCalonArea.value && getVal(item, ['Area', 'AREA']) !== filterCalonArea.value) return false
    if (filterCalonPosisi.value && getVal(item, ['Posisi', 'POSISI']) !== filterCalonPosisi.value) return false
    if (filterCalonDept.value && getVal(item, ['Departement', 'Department', 'Dept']) !== filterCalonDept.value) return false
    if (filterCalonInstansi.value && getVal(item, ['Instansi', 'INSTANSI', 'Universitas']) !== filterCalonInstansi.value) return false
    if (filterCalonGender.value && getVal(item, ['Gender', 'GENDER']) !== filterCalonGender.value) return false
    if (filterCalonPendidikan.value && getVal(item, ['Pendidikan', 'PENDIDIKAN']) !== filterCalonPendidikan.value) return false
    if (filterCalonHasil.value && getVal(item, ['Hasil', 'HASIL']) !== filterCalonHasil.value) return false
    
    if (filterCalonBulan.value) {
      const cvDate = getVal(item, ['CV Masuk', 'Cv Masuk', 'Tanggal Hasil', 'Join Date'])
      const bulanItem = extractBulan(cvDate)
      if (bulanItem !== filterCalonBulan.value) return false
    }

    if (filterCalonTahun.value && getVal(item, ['Tahun', 'TAHUN']) !== filterCalonTahun.value) return false

    return true
  })
})

const getHasilClass = (hasil: string): string => {
  if (!hasil || hasil === '-') return 'hasil-default'
  const h = hasil.toLowerCase()
  if (h.includes('diterima') || h.includes('lulus')) return 'hasil-diterima'
  if (h.includes('ditolak') || h.includes('gagal')) return 'hasil-ditolak'
  if (h.includes('proses')) return 'hasil-proses'
  if (h.includes('pending')) return 'hasil-pending'
  return 'hasil-default'
}

onMounted(() => {
  loadAllData()
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

/* Control Bar */
.control-bar {
  max-width: 1200px;
  margin: 24px auto 0 auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
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

/* Filter Card Panel */
.filter-card {
  background: #ffffff;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}

.filter-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.filter-card-header h3 {
  margin: 0;
  font-size: 14px;
  font-weight: 700;
  color: #0d1b7a;
}

.btn-reset {
  background-color: #f1f5f9;
  color: #dc2626;
  border: 1px solid #fca5a5;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
}

.btn-reset:hover {
  background-color: #fee2e2;
}

.filter-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 10px;
}

.filter-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.filter-label {
  font-size: 10px;
  font-weight: 700;
  color: #64748b;
  letter-spacing: 0.5px;
}

.filter-select {
  padding: 6px 8px;
  border-radius: 6px;
  border: 1px solid #cbd5e1;
  font-size: 12px;
  background: #ffffff;
  color: #1e293b;
  outline: none;
}

/* Main Content */
.content-container {
  width: 100%;
  max-width: 1200px;
  margin: 20px auto 40px auto;
  padding: 0 16px;
  box-sizing: border-box;
}

/* Table Design & Scroll */
.table-card {
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
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

.table-scroll-wrapper {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
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

.center { text-align: center; }
.bold { font-weight: 700; }
.col-no { width: 4%; text-align: center; }

.status-pill {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  padding: 2px 8px;
  border-radius: 4px;
  font-weight: 600;
  font-size: 12px;
}

.boolean-tag {
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 800;
}

.tag-true { background-color: #dcfce7; color: #15803d; }
.tag-false { background-color: #fee2e2; color: #b91c1c; }

.hasil-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 800;
}

.hasil-diterima { background-color: #dcfce7; color: #15803d; }
.hasil-ditolak { background-color: #fee2e2; color: #b91c1c; }
.hasil-proses { background-color: #fef3c7; color: #b45309; }
.hasil-pending { background-color: #e0e7ff; color: #4338ca; }
.hasil-default { background-color: #f1f5f9; color: #475569; }

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