<template>
  <div class="visual-container">
    <!-- Navbar Header -->
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

    <!-- Controls Bar & Filter Section -->
    <div class="control-bar">
      <!-- Row Utama: Jenis Diagram & Filter Wilayah -->
      <div class="top-controls">
        <div class="chart-type-toggle">
          <button 
            :class="['type-btn', { active: chartType === 'bar' }]" 
            @click="chartType = 'bar'">
            Bar Chart Vertikal
          </button>
          <button 
            :class="['type-btn', { active: chartType === 'pie' }]" 
            @click="chartType = 'pie'">
            Diagram
          </button>
        </div>

        <div class="wilayah-selector">
          <label class="filter-label">Wilayah Data</label>
          <select v-model="selectedWilayah" @change="loadKaryawanData" class="select-wilayah">
            <option value="all-karyawan">All Karyawan</option>
            <option value="sulawesi">Sulawesi Banumapa</option>
            <option value="kalimantan">Kalimantan</option>
          </select>
        </div>
      </div>

      <!-- Card Panel Multi Filter Dropdown Checkbox -->
      <div class="filter-card" @click.stop>
        <div class="filter-card-header">
          <h3>🔍 Filter Visualisasi Data</h3>
          <button @click="resetFilters" class="btn-reset">Reset Filter</button>
        </div>

        <div class="filter-grid">
          <!-- Filter Company -->
          <div class="filter-item">
            <label class="filter-label">COMPANY</label>
            <div class="custom-dropdown">
              <button class="dropdown-trigger" @click.stop="toggleDropdown('company')">
                <span>{{ getLabelText(filterCompany, 'Semua Company') }}</span>
                <span class="arrow">▾</span>
              </button>
              <div v-if="openDropdown === 'company'" class="dropdown-popover">
                <label class="checkbox-option select-all" @click.stop>
                  <input type="checkbox" :checked="filterCompany.length === optionLists.company.length" @change="toggleSelectAll('company', optionLists.company)" />
                  <strong>Pilih Semua</strong>
                </label>
                <div class="popover-divider"></div>
                <label v-for="item in optionLists.company" :key="item" class="checkbox-option" @click.stop>
                  <input type="checkbox" :value="item" v-model="filterCompany" />
                  <span>{{ item }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Filter Divisi -->
          <div class="filter-item">
            <label class="filter-label">DIVISI / DIVISION</label>
            <div class="custom-dropdown">
              <button class="dropdown-trigger" @click.stop="toggleDropdown('divisi')">
                <span>{{ getLabelText(filterDivisi, 'Semua Divisi') }}</span>
                <span class="arrow">▾</span>
              </button>
              <div v-if="openDropdown === 'divisi'" class="dropdown-popover">
                <label class="checkbox-option select-all" @click.stop>
                  <input type="checkbox" :checked="filterDivisi.length === optionLists.divisi.length" @change="toggleSelectAll('divisi', optionLists.divisi)" />
                  <strong>Pilih Semua</strong>
                </label>
                <div class="popover-divider"></div>
                <label v-for="item in optionLists.divisi" :key="item" class="checkbox-option" @click.stop>
                  <input type="checkbox" :value="item" v-model="filterDivisi" />
                  <span>{{ item }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Filter Department -->
          <div class="filter-item">
            <label class="filter-label">DEPARTMENT</label>
            <div class="custom-dropdown">
              <button class="dropdown-trigger" @click.stop="toggleDropdown('dept')">
                <span>{{ getLabelText(filterDept, 'Semua Department') }}</span>
                <span class="arrow">▾</span>
              </button>
              <div v-if="openDropdown === 'dept'" class="dropdown-popover">
                <label class="checkbox-option select-all" @click.stop>
                  <input type="checkbox" :checked="filterDept.length === optionLists.dept.length" @change="toggleSelectAll('dept', optionLists.dept)" />
                  <strong>Pilih Semua</strong>
                </label>
                <div class="popover-divider"></div>
                <label v-for="item in optionLists.dept" :key="item" class="checkbox-option" @click.stop>
                  <input type="checkbox" :value="item" v-model="filterDept" />
                  <span>{{ item }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Filter Level -->
          <div class="filter-item">
            <label class="filter-label">LEVEL</label>
            <div class="custom-dropdown">
              <button class="dropdown-trigger" @click.stop="toggleDropdown('level')">
                <span>{{ getLabelText(filterLevel, 'Semua Level') }}</span>
                <span class="arrow">▾</span>
              </button>
              <div v-if="openDropdown === 'level'" class="dropdown-popover">
                <label class="checkbox-option select-all" @click.stop>
                  <input type="checkbox" :checked="filterLevel.length === optionLists.level.length" @change="toggleSelectAll('level', optionLists.level)" />
                  <strong>Pilih Semua</strong>
                </label>
                <div class="popover-divider"></div>
                <label v-for="item in optionLists.level" :key="item" class="checkbox-option" @click.stop>
                  <input type="checkbox" :value="item" v-model="filterLevel" />
                  <span>{{ item }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Filter Cost Center -->
          <div class="filter-item">
            <label class="filter-label">COST CENTER</label>
            <div class="custom-dropdown">
              <button class="dropdown-trigger" @click.stop="toggleDropdown('costCenter')">
                <span>{{ getLabelText(filterCostCenter, 'Semua Cost Center') }}</span>
                <span class="arrow">▾</span>
              </button>
              <div v-if="openDropdown === 'costCenter'" class="dropdown-popover">
                <label class="checkbox-option select-all" @click.stop>
                  <input type="checkbox" :checked="filterCostCenter.length === optionLists.costCenter.length" @change="toggleSelectAll('costCenter', optionLists.costCenter)" />
                  <strong>Pilih Semua</strong>
                </label>
                <div class="popover-divider"></div>
                <label v-for="item in optionLists.costCenter" :key="item" class="checkbox-option" @click.stop>
                  <input type="checkbox" :value="item" v-model="filterCostCenter" />
                  <span>{{ item }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Filter Bulan Join -->
          <div class="filter-item">
            <label class="filter-label">BULAN JOIN</label>
            <div class="custom-dropdown">
              <button class="dropdown-trigger" @click.stop="toggleDropdown('bulan')">
                <span>{{ getLabelText(filterBulan, 'Semua Bulan') }}</span>
                <span class="arrow">▾</span>
              </button>
              <div v-if="openDropdown === 'bulan'" class="dropdown-popover">
                <label class="checkbox-option select-all" @click.stop>
                  <input type="checkbox" :checked="filterBulan.length === monthNames.length" @change="toggleSelectAll('bulan', monthNames)" />
                  <strong>Pilih Semua</strong>
                </label>
                <div class="popover-divider"></div>
                <label v-for="bln in monthNames" :key="bln" class="checkbox-option" @click.stop>
                  <input type="checkbox" :value="bln" v-model="filterBulan" />
                  <span>{{ bln }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Filter Tahun Join -->
          <div class="filter-item">
            <label class="filter-label">TAHUN JOIN</label>
            <div class="custom-dropdown">
              <button class="dropdown-trigger" @click.stop="toggleDropdown('tahun')">
                <span>{{ getLabelText(filterTahun, 'Semua Tahun') }}</span>
                <span class="arrow">▾</span>
              </button>
              <div v-if="openDropdown === 'tahun'" class="dropdown-popover">
                <label class="checkbox-option select-all" @click.stop>
                  <input type="checkbox" :checked="filterTahun.length === optionLists.tahun.length" @change="toggleSelectAll('tahun', optionLists.tahun)" />
                  <strong>Pilih Semua</strong>
                </label>
                <div class="popover-divider"></div>
                <label v-for="yr in optionLists.tahun" :key="yr" class="checkbox-option" @click.stop>
                  <input type="checkbox" :value="yr" v-model="filterTahun" />
                  <span>{{ yr }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart List Section -->
    <main class="charts-wrapper">
      <div v-if="loading" class="loading-state">
        <p>Memuat dan mengolah data dari Google Sheets...</p>
      </div>

      <div v-else-if="errorMessage" class="error-state">
        <p>{{ errorMessage }}</p>
      </div>

      <template v-else>
        <div class="chart-group">
          <div 
            v-for="col in allChartColumns" 
            :key="col.id" 
            class="chart-card"
          >
            <div class="chart-card-header">
              <h2>{{ col.label }}</h2>
              <div class="chart-meta-info">
                <span>Total: <strong>{{ getSummaryStats(col.id).total }} Orang</strong></span>
                <span class="dot">•</span>
                <span>Terbanyak: <strong>{{ getSummaryStats(col.id).topCategory }} ({{ getSummaryStats(col.id).topCount }})</strong></span>
              </div>
            </div>

            <div class="chart-box" :style="{ height: '400px' }">
              <Bar v-if="chartType === 'bar'" :data="getChartData(col.id)" :options="getBarOptions(col.id)" />
              <Pie v-else :data="getChartData(col.id)" :options="pieOptions" />
            </div>
          </div>
        </div>
      </template>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Bar, Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  CategoryScale,
  LinearScale,
  type ChartData,
  type ChartOptions
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, ArcElement, CategoryScale, LinearScale)

export type KaryawanRow = Record<string, string>

const chartType = ref<'bar' | 'pie'>('bar')
const selectedWilayah = ref<string>('all-karyawan')
const loading = ref<boolean>(true)
const errorMessage = ref<string>('')

// State Multi-Select Filters
const filterCompany = ref<string[]>([])
const filterDivisi = ref<string[]>([])
const filterDept = ref<string[]>([])
const filterLevel = ref<string[]>([])
const filterCostCenter = ref<string[]>([])
const filterBulan = ref<string[]>([])
const filterTahun = ref<string[]>([])

const openDropdown = ref<string | null>(null)

const toggleDropdown = (key: string) => {
  openDropdown.value = openDropdown.value === key ? null : key
}

const closeAllDropdowns = () => {
  openDropdown.value = null
}

const getLabelText = (selectedArr: string[], defaultText: string) => {
  if (!selectedArr || selectedArr.length === 0) return defaultText
  if (selectedArr.length === 1) return selectedArr[0]
  return `${selectedArr.length} Terpilih`
}

const toggleSelectAll = (filterKey: string, options: string[]) => {
  let targetRef: any
  if (filterKey === 'company') targetRef = filterCompany
  else if (filterKey === 'divisi') targetRef = filterDivisi
  else if (filterKey === 'dept') targetRef = filterDept
  else if (filterKey === 'level') targetRef = filterLevel
  else if (filterKey === 'costCenter') targetRef = filterCostCenter
  else if (filterKey === 'bulan') targetRef = filterBulan
  else if (filterKey === 'tahun') targetRef = filterTahun

  if (targetRef.value.length === options.length) {
    targetRef.value = []
  } else {
    targetRef.value = [...options]
  }
}

const rawKaryawanList = ref<KaryawanRow[]>([])

const monthNames = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
]

const allChartColumns = [
  { id: 'Company', label: 'Company' },
  { id: 'Divisi', label: 'Division / Divisi' },
  { id: 'Level', label: 'Level Karyawan' },
  { id: 'Umur', label: 'Kategori Usia Karyawan' },
  { id: 'Generation', label: 'Generation' },
  { id: 'Masa Bakti', label: 'Kategori Masa Bakti' },
  { id: 'Education', label: 'Education / Pendidikan' }
]

const chartColors: string[] = [
  '#2563eb', '#10b981', '#f59e0b', '#ef4444', 
  '#8b5cf6', '#06b6d4', '#ec4899', '#6366f1', 
  '#14b8a6', '#f97316', '#84cc16', '#a855f7',
  '#64748b', '#0284c7', '#d97706', '#dc2626'
]

const eduOrder = [
  'STRATA 3', 'S3', 'DOCTOR',
  'STRATA 2', 'S2', 'MAGISTER',
  'STRATA 1', 'S1', 'SARJANA',
  'DIPLOMA 4', 'D4',
  'DIPLOMA 3', 'D3',
  'DIPLOMA 2', 'D2',
  'DIPLOMA 1', 'D1',
  'SMA', 'SMK', 'SMK / STM', 'STM', 'SLTA',
  'SMP', 'SLTP',
  'SD'
]

const getEduRank = (label: string): number => {
  const norm = label.toUpperCase().trim()
  const idx = eduOrder.findIndex(item => norm.includes(item))
  return idx !== -1 ? idx : 999
}

const getBarOptions = (columnName: string): ChartOptions<'bar'> => {
  return {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: 'x',
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { 
        beginAtZero: true,
        ticks: { stepSize: 1, precision: 0 }
      },
      x: { 
        grid: { display: false },
        ticks: { 
          autoSkip: false,
          maxRotation: 45,
          minRotation: 0
        }
      }
    }
  }
}

const pieOptions: ChartOptions<'pie'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'right',
      labels: {
        generateLabels: (chart: any) => {
          const data = chart.data
          if (data.labels?.length && data.datasets?.length) {
            return data.labels.map((label: string, i: number) => {
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

const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gids: Record<string, string> = {
  'all-karyawan': '0',
  'sulawesi': '1627431459',
  'kalimantan': '936973334'
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
      result.push(entry.replace(/^"|"$/g, '').trim())
      entry = ''
    } else {
      entry += char
    }
  }
  result.push(entry.replace(/^"|"$/g, '').trim())
  return result
}

const getPropertyValueByAliases = (item: KaryawanRow, aliases: string[]): string => {
  if (!item) return 'Tidak Terdata'

  const normalize = (str: string) => str.toLowerCase().replace(/[^a-z0-9]/g, '')
  const itemKeys = Object.keys(item)

  for (const alias of aliases) {
    const targetNorm = normalize(alias)
    const foundKey = itemKeys.find(k => normalize(k) === targetNorm)
    if (foundKey && item[foundKey] !== undefined && item[foundKey] !== null && String(item[foundKey]).trim() !== '') {
      return String(item[foundKey]).trim()
    }
  }

  return 'Tidak Terdata'
}

const getPropertyValue = (item: KaryawanRow, columnName: string): string => {
  return getPropertyValueByAliases(item, [columnName])
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

const getUsiaRangeGroup = (item: KaryawanRow): string => {
  const rawAgeVal = getPropertyValueByAliases(item, ['Age', 'Umur', 'Usia', 'AGE', 'UMUR'])
  const match = rawAgeVal.match(/\d+/)
  if (!match) return 'Tidak Terdata'
  
  const age = parseInt(match[0], 10)
  if (isNaN(age)) return 'Tidak Terdata'

  if (age >= 15 && age <= 24) return '15-24 Tahun'
  if (age >= 25 && age <= 40) return '25-40 Tahun'
  if (age >= 41 && age <= 59) return '41-59 Tahun'
  if (age >= 60 && age <= 64) return '60-64 Tahun'
  return '> 64 Tahun'
}

const getMasaBaktiRangeGroup = (item: KaryawanRow): string => {
  let rawVal = getPropertyValueByAliases(item, ['Masa Bakti', 'MASA BAKTI', 'Masa Kerja', 'MasaBakti'])

  if (rawVal === 'Tidak Terdata' || rawVal === '-' || rawVal === '' || rawVal.includes('#')) {
    const joinDateStr = getPropertyValueByAliases(item, ['Join Date', 'JOIN DATE', 'Tanggal Masuk', 'Tgl Masuk'])
    const d = parseDateObj(joinDateStr)
    if (!d) return '< 1-2 Tahun'
    
    const now = new Date()
    let diffYears = now.getFullYear() - d.getFullYear()
    if (now.getMonth() < d.getMonth() || (now.getMonth() === d.getMonth() && now.getDate() < d.getDate())) {
      diffYears--
    }
    rawVal = `${Math.max(0, diffYears)}`
  }

  const match = rawVal.match(/\d+/)
  const years = match ? parseInt(match[0], 10) : 0

  if (years <= 2) return '< 1-2 Tahun'
  if (years >= 3 && years <= 5) return '3-5 Tahun'
  return '6-10< Tahun'
}

const loadKaryawanData = async (): Promise<void> => {
  loading.value = true
  errorMessage.value = ''

  const gid = gids[selectedWilayah.value] || '0'
  const csvUrl = `https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gid}`

  try {
    const csvText = await $fetch<string>(csvUrl, { responseType: 'text' })

    if (!csvText || csvText.includes('<!DOCTYPE html>')) {
      throw new Error('Gagal mengambil data dari Google Sheets. Pastikan Spreadsheet disetel Publik.')
    }

    let cleanCsv = csvText
    if (cleanCsv.startsWith('\uFEFF')) {
      cleanCsv = cleanCsv.slice(1)
    }

    const lines = cleanCsv.split(/\r?\n/).filter(l => l.trim() !== '')
    if (lines.length < 2) {
      rawKaryawanList.value = []
      return
    }

    const rawHeaders = lines.shift() || ''
    const headers = parseCsvLine(rawHeaders).map(h => h.replace(/^\uFEFF/, '').trim())

    const parsedData: KaryawanRow[] = []

    lines.forEach(line => {
      const rowValues = parseCsvLine(line)
      const item: KaryawanRow = {}
      
      headers.forEach((header, idx) => {
        if (header) {
          item[header] = rowValues[idx] ? rowValues[idx].trim() : ''
        }
      })

      if (Object.values(item).some(v => v !== '')) {
        parsedData.push(item)
      }
    })

    rawKaryawanList.value = parsedData

  } catch (err: any) {
    console.error('Error fetching visualisasi data:', err)
    errorMessage.value = err.message || 'Gagal terhubung ke Google Sheets.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadKaryawanData()
  window.addEventListener('click', closeAllDropdowns)
})

onUnmounted(() => {
  window.removeEventListener('click', closeAllDropdowns)
})

const optionLists = computed(() => {
  const getUniques = (keys: string[]) => {
    const set = new Set<string>()
    rawKaryawanList.value.forEach(item => {
      keys.forEach(k => {
        const val = getPropertyValue(item, k)
        if (val && val !== 'Tidak Terdata') set.add(val)
      })
    })
    return Array.from(set).sort()
  }

  const yearsSet = new Set<string>()
  rawKaryawanList.value.forEach(item => {
    const joinDateStr = getPropertyValueByAliases(item, ['Join Date', 'JOIN DATE', 'Tanggal Masuk'])
    const d = parseDateObj(joinDateStr)
    if (d) yearsSet.add(String(d.getFullYear()))
  })

  return {
    company: getUniques(['Company', 'COMPANY', 'Perusahaan']),
    divisi: getUniques(['Divisi', 'DIVISI', 'Division']),
    dept: getUniques(['DEPARTMENT', 'Department', 'Departement', 'DEPARTEMENT', 'Dept']),
    level: getUniques(['Level', 'LEVEL']),
    costCenter: getUniques(['Cost Center', 'COST CENTER', 'CostCenter']),
    tahun: Array.from(yearsSet).sort((a, b) => Number(b) - Number(a))
  }
})

// LOGIKA REAKTIF MULTI-SELECT FILTER
const filteredKaryawanList = computed(() => {
  return rawKaryawanList.value.filter(item => {
    // Multi-Select Company
    if (filterCompany.value.length > 0) {
      const c = getPropertyValueByAliases(item, ['Company', 'COMPANY', 'Perusahaan'])
      if (!filterCompany.value.some(sel => sel.toLowerCase() === c.toLowerCase())) return false
    }

    // Multi-Select Divisi
    if (filterDivisi.value.length > 0) {
      const d = getPropertyValueByAliases(item, ['Divisi', 'DIVISI', 'Division'])
      if (!filterDivisi.value.some(sel => sel.toLowerCase() === d.toLowerCase())) return false
    }

    // Multi-Select Department
    if (filterDept.value.length > 0) {
      const dept = getPropertyValueByAliases(item, ['DEPARTMENT', 'Department', 'Departement', 'DEPARTEMENT', 'Dept'])
      if (!filterDept.value.some(sel => sel.toLowerCase() === dept.toLowerCase())) return false
    }

    // Multi-Select Level
    if (filterLevel.value.length > 0) {
      const lvl = getPropertyValueByAliases(item, ['Level', 'LEVEL'])
      if (!filterLevel.value.some(sel => sel.toLowerCase() === lvl.toLowerCase())) return false
    }

    // Multi-Select Cost Center
    if (filterCostCenter.value.length > 0) {
      const cc = getPropertyValueByAliases(item, ['Cost Center', 'COST CENTER', 'CostCenter'])
      if (!filterCostCenter.value.some(sel => sel.toLowerCase() === cc.toLowerCase())) return false
    }

    const joinDateStr = getPropertyValueByAliases(item, ['Join Date', 'JOIN DATE', 'Tanggal Masuk'])
    const dateObj = parseDateObj(joinDateStr)

    // Multi-Select Bulan
    if (filterBulan.value.length > 0) {
      if (!dateObj) return false
      const blnName = monthNames[dateObj.getMonth()]
      if (!filterBulan.value.includes(blnName)) return false
    }

    // Multi-Select Tahun
    if (filterTahun.value.length > 0) {
      if (!dateObj) return false
      if (!filterTahun.value.includes(String(dateObj.getFullYear()))) return false
    }

    return true
  })
})

const resetFilters = () => {
  filterCompany.value = []
  filterDivisi.value = []
  filterDept.value = []
  filterLevel.value = []
  filterCostCenter.value = []
  filterBulan.value = []
  filterTahun.value = []
}

const getChartData = (columnName: string): ChartData<'bar' | 'pie'> => {
  const counts: Record<string, number> = {}

  filteredKaryawanList.value.forEach(item => {
    let rawVal = ''

    if (columnName.toLowerCase().includes('masabakti') || columnName.toLowerCase().includes('masa bakti')) {
      rawVal = getMasaBaktiRangeGroup(item)
    } else if (columnName.toLowerCase().includes('umur') || columnName.toLowerCase().includes('usia')) {
      rawVal = getUsiaRangeGroup(item)
    } else {
      rawVal = getPropertyValue(item, columnName)
    }

    counts[rawVal] = (counts[rawVal] ?? 0) + 1
  })

  const colLower = columnName.toLowerCase()
  const isEducation = colLower.includes('education') || colLower.includes('pendidikan')
  const isUsiaRange = colLower.includes('umur') || colLower.includes('usia')
  const isMasaBaktiRange = colLower.includes('masabakti') || colLower.includes('masa bakti')

  const ageOrder = ['15-24 Tahun', '25-40 Tahun', '41-59 Tahun', '60-64 Tahun', '> 64 Tahun', 'Tidak Terdata']
  const masaBaktiOrder = ['< 1-2 Tahun', '3-5 Tahun', '6-10< Tahun', 'Tidak Terdata']

  const sortedKeys = Object.keys(counts).sort((a, b) => {
    if (isEducation) {
      return getEduRank(a) - getEduRank(b)
    }
    if (isUsiaRange) {
      return ageOrder.indexOf(a) - ageOrder.indexOf(b)
    }
    if (isMasaBaktiRange) {
      return masaBaktiOrder.indexOf(a) - masaBaktiOrder.indexOf(b)
    }
    return (counts[b] ?? 0) - (counts[a] ?? 0)
  })

  return {
    labels: sortedKeys,
    datasets: [
      {
        label: 'Jumlah Karyawan',
        data: sortedKeys.map(key => counts[key] ?? 0),
        backgroundColor: chartColors.slice(0, sortedKeys.length),
        borderRadius: chartType.value === 'bar' ? 6 : 0
      }
    ]
  }
}

const getSummaryStats = (columnName: string) => {
  const counts: Record<string, number> = {}
  let total = 0

  filteredKaryawanList.value.forEach(item => {
    let rawVal = ''

    if (columnName.toLowerCase().includes('masabakti') || columnName.toLowerCase().includes('masa bakti')) {
      rawVal = getMasaBaktiRangeGroup(item)
    } else if (columnName.toLowerCase().includes('umur') || columnName.toLowerCase().includes('usia')) {
      rawVal = getUsiaRangeGroup(item)
    } else {
      rawVal = getPropertyValue(item, columnName)
    }

    counts[rawVal] = (counts[rawVal] ?? 0) + 1
    total++
  })

  const sortedByCount = Object.keys(counts).sort((a, b) => (counts[b] ?? 0) - (counts[a] ?? 0))

  return {
    total,
    topCategory: sortedByCount[0] || '-',
    topCount: counts[sortedByCount[0] || ''] || 0
  }
}
</script>

<style scoped>
.visual-container {
  background-color: #f8fafc;
  min-height: 100vh;
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
  background-color: #ffffff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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

/* Control & Filter Section */
.control-bar {
  max-width: 1100px;
  margin: 24px auto;
  padding: 0 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.top-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.chart-type-toggle {
  display: flex;
  background: #e2e8f0;
  padding: 4px;
  border-radius: 8px;
}

.type-btn {
  background: transparent;
  border: none;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.type-btn.active {
  background: #ffffff;
  color: #0d1b7a;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.wilayah-selector {
  display: flex;
  align-items: center;
  gap: 8px;
}

.select-wilayah {
  background-color: #0d1b7a;
  color: white;
  border: none;
  padding: 9px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  outline: none;
  cursor: pointer;
}

.select-wilayah option {
  background-color: #ffffff;
  color: #1e293b;
}

/* Filter Card Panel */
.filter-card {
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
  border: 1px solid #e2e8f0;
}

.filter-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.filter-card-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #0d1b7a;
}

.btn-reset {
  background-color: #f1f5f9;
  color: #dc2626;
  border: 1px solid #fca5a5;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-reset:hover {
  background-color: #fee2e2;
}

.filter-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 12px;
}

.filter-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.filter-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Custom Dropdown Styling (Single Line dengan Popover Centang) */
.custom-dropdown {
  position: relative;
  width: 100%;
}

.dropdown-trigger {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background-color: #ffffff;
  color: #1e293b;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  text-align: left;
}

.dropdown-trigger:focus {
  border-color: #0d1b7a;
}

.dropdown-popover {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
  max-height: 200px;
  overflow-y: auto;
  z-index: 100;
  padding: 6px;
}

.checkbox-option {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 8px;
  font-size: 12px;
  color: #334155;
  cursor: pointer;
  border-radius: 4px;
}

.checkbox-option:hover {
  background-color: #f1f5f9;
}

.popover-divider {
  height: 1px;
  background-color: #e2e8f0;
  margin: 4px 0;
}

/* Chart Container Cards */
.charts-wrapper {
  max-width: 1100px;
  margin: 0 auto 50px auto;
  padding: 0 20px;
  box-sizing: border-box;
}

.chart-group {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.chart-card {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.chart-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
  gap: 8px;
}

.chart-card-header h2 {
  font-size: 20px;
  font-weight: 800;
  color: #0d1b7a;
  margin: 0;
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

.dot {
  color: #cbd5e1;
}

.chart-box {
  position: relative;
  width: 100%;
}

.loading-state, .error-state {
  text-align: center;
  padding: 60px;
  color: #64748b;
  font-weight: 500;
}

.error-state {
  color: #ef4444;
}

/* Responsif Layar HP/Tablet (< 768px) */
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

  .control-bar {
    padding: 0 16px;
  }

  .filter-grid {
    grid-template-columns: 1fr;
  }

  .charts-wrapper {
    padding: 0 16px;
  }

  .chart-card {
    padding: 16px;
  }

  .chart-card-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>