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
            <NuxtLink to="/WIPHRA">Work In Progress HR Area</NuxtLink>
            <NuxtLink to="/budget-department">Budget Department</NuxtLink>
          </div>
        </div>
      </nav>
    </header>

    <!-- Controls & Tab Selection -->
    <div class="control-bar">
      <!-- Tab Kategori Data -->
      <div class="tabs">
        <button 
          :class="['tab-btn', { active: activeTab === 'company' }]" 
          @click="activeTab = 'company'">
          Data Company
        </button>
        <button 
          :class="['tab-btn', { active: activeTab === 'masabakti' }]" 
          @click="activeTab = 'masabakti'">
          Data Masa Bakti
        </button>
        <button 
          :class="['tab-btn', { active: activeTab === 'education' }]" 
          @click="activeTab = 'education'">
          Data Education
        </button>
      </div>

      <!-- Fitur Pilihan Jenis Diagram & Filter Wilayah -->
      <div class="filter-group">
        <div class="chart-type-toggle">
          <button 
            :class="['type-btn', { active: chartType === 'bar' }]" 
            @click="chartType = 'bar'">
            Bar Chart Horizontal
          </button>
          <button 
            :class="['type-btn', { active: chartType === 'pie' }]" 
            @click="chartType = 'pie'">
            Diagram (Pie)
          </button>
        </div>

        <select v-model="selectedWilayah" @change="loadKaryawanData" class="select-wilayah">
          <option value="all-karyawan">All Karyawan</option>
          <option value="sulawesi">Sulawesi Banumapa</option>
          <option value="kalimantan">Kalimantan</option>
        </select>
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
        <!-- TAB 1: DATA COMPANY -->
        <div v-if="activeTab === 'company'" class="chart-group">
          <div class="chart-card">
            <h2>Company</h2>
            <div class="chart-box">
              <Bar v-if="chartType === 'bar'" :data="getChartData('Company')" :options="barOptions" />
              <Pie v-else :data="getChartData('Company')" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h2>Division</h2>
            <div class="chart-box">
              <Bar v-if="chartType === 'bar'" :data="getChartData('Divisi')" :options="barOptions" />
              <Pie v-else :data="getChartData('Divisi')" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h2>Generation</h2>
            <div class="chart-box">
              <Bar v-if="chartType === 'bar'" :data="getChartData('Generation')" :options="barOptions" />
              <Pie v-else :data="getChartData('Generation')" :options="pieOptions" />
            </div>
          </div>
        </div>

        <!-- TAB 2: DATA MASA BAKTI -->
        <div v-if="activeTab === 'masabakti'" class="chart-group">
          <div class="chart-card">
            <h2>Masa Bakti</h2>
            <div class="chart-box">
              <Bar v-if="chartType === 'bar'" :data="getChartData('Masa Bakti')" :options="barOptions" />
              <Pie v-else :data="getChartData('Masa Bakti')" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h2>Level</h2>
            <div class="chart-box">
              <Bar v-if="chartType === 'bar'" :data="getChartData('Level')" :options="barOptions" />
              <Pie v-else :data="getChartData('Level')" :options="pieOptions" />
            </div>
          </div>
        </div>

        <!-- TAB 3: DATA EDUCATION -->
        <div v-if="activeTab === 'education'" class="chart-group">
          <div class="chart-card">
            <h2>Education</h2>
            <div class="chart-box">
              <Bar v-if="chartType === 'bar'" :data="getChartData('Education')" :options="barOptions" />
              <Pie v-else :data="getChartData('Education')" :options="pieOptions" />
            </div>
          </div>

          <div class="chart-card">
            <h2>Major</h2>
            <div class="chart-box">
              <Bar v-if="chartType === 'bar'" :data="getChartData('Major')" :options="barOptions" />
              <Pie v-else :data="getChartData('Major')" :options="pieOptions" />
            </div>
          </div>
        </div>
      </template>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Bar, Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, ArcElement, CategoryScale, LinearScale)

type KaryawanRow = Record<string, any>

const activeTab = ref<'company' | 'masabakti' | 'education'>('company')
const chartType = ref<'bar' | 'pie'>('bar')
const selectedWilayah = ref<string>('all-karyawan')
const loading = ref<boolean>(true)
const errorMessage = ref<string>('')

const karyawanList = ref<KaryawanRow[]>([])

const chartColors: string[] = [
  '#0d1b7a', '#2563eb', '#3b82f6', '#60a5fa', 
  '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
  '#ec4899', '#06b6d4', '#64748b', '#a855f7'
]

const barOptions: any = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y' as const,
  plugins: {
    legend: { display: false },
    datalabels: { display: false }
  },
  scales: {
    x: { beginAtZero: true },
    y: { grid: { display: false } }
  }
}

const pieOptions: any = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'right' as const,
      labels: {
        generateLabels: (chart: any) => {
          const data = chart.data
          if (data.labels.length && data.datasets.length) {
            return data.labels.map((label: string, i: number) => {
              const value = data.datasets[0].data[i]
              const meta = chart.getDatasetMeta(0)
              const style = meta.controller.getStyle(i)
              return {
                text: `${label} (${value})`,
                fillStyle: style.backgroundColor,
                strokeStyle: style.borderColor,
                lineWidth: style.borderWidth,
                hidden: !chart.isDatasetVisible(0) || meta.data[i].hidden,
                index: i
              }
            })
          }
          return []
        }
      }
    },
    datalabels: { display: false }
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
      result.push(entry.replace(/^"|"$/g, ''))
      entry = ''
    } else {
      entry += char
    }
  }
  result.push(entry.replace(/^"|"$/g, ''))
  return result
}

const getPropertyValue = (item: KaryawanRow, columnName: string): string => {
  if (!item) return 'Tidak Terdata'

  const normalize = (str: string) => str.toLowerCase().replace(/[^a-z0-9]/g, '')
  const targetNorm = normalize(columnName)

  for (const key in item) {
    if (normalize(key) === targetNorm) {
      const val = item[key]
      if (val !== undefined && val !== null && String(val).trim() !== '') {
        return String(val).trim()
      }
    }
  }

  return 'Tidak Terdata'
}

const loadKaryawanData = async (): Promise<void> => {
  loading.value = true
  errorMessage.value = ''

  const gid = gids[selectedWilayah.value] || '0'
  const csvUrl = `https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gid}`

  try {
    const csvText = await $fetch<string>(csvUrl, { responseType: 'text' })

    if (!csvText || csvText.includes('<!DOCTYPE html>')) {
      throw new Error('Gagal mengambil data dari Google Sheets. Pastikan spreadsheet publik.')
    }

    const lines = csvText.split('\n').map(l => l.replace('\r', ''))
    if (lines.length < 2) {
      karyawanList.value = []
      return
    }

    const rawHeaders = lines.shift() || ''
    const headers = parseCsvLine(rawHeaders).map(h => h.trim())

    const parsedData: KaryawanRow[] = []

    lines.forEach(line => {
      if (line.trim() === '') return
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

    karyawanList.value = parsedData

  } catch (err: any) {
    console.error('Error fetching visualisasi data:', err)
    errorMessage.value = err.message || 'Gagal terhubung ke Google Sheets.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadKaryawanData()
})

const getChartData = (columnName: string): any => {
  const counts: Record<string, number> = {}

  karyawanList.value.forEach(item => {
    let rawVal = getPropertyValue(item, columnName)

    if (columnName.toLowerCase().includes('masabakti') || columnName.toLowerCase().includes('masa bakti')) {
      if (rawVal !== 'Tidak Terdata' && !rawVal.toLowerCase().includes('tahun')) {
        rawVal = `${rawVal} Tahun`
      }
    }

    counts[rawVal] = (counts[rawVal] ?? 0) + 1
  })

  const sortedKeys = Object.keys(counts).sort((a, b) => (counts[b] || 0) - (counts[a] || 0))

  return {
    labels: sortedKeys,
    datasets: [
      {
        label: 'Jumlah Karyawan',
        data: sortedKeys.map(key => counts[key] || 0),
        backgroundColor: chartType.value === 'bar' ? '#0d1b7a' : chartColors.slice(0, sortedKeys.length),
        borderRadius: chartType.value === 'bar' ? 6 : 0
      }
    ]
  }
}
</script>

<style scoped>
.visual-container {
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
.nav-menu { display: flex; gap: 15px; }
.dropdown { position: relative; }
.dropdown button { background: none; border: none; font-size: 13px; cursor: pointer; color: #333; }
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #ffffff;
  min-width: 160px;
  box-shadow: 0px 4px 8px rgba(0,0,0,0.15);
  z-index: 10;
  border-radius: 4px;
}
.dropdown-content a { color: #333; padding: 8px 12px; text-decoration: none; display: block; font-size: 11px; }
.dropdown-content a:hover { background-color: #f1f5f9; color: #0d1b7a; }
.dropdown:hover .dropdown-content { display: block; }

/* Control & Tabs Bar */
.control-bar {
  max-width: 1000px;
  margin: 30px auto 20px auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
}

.tabs {
  display: flex;
  gap: 8px;
}

.tab-btn {
  background-color: #f1f5f9;
  color: #334155;
  border: 1px solid #cbd5e1;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.tab-btn.active {
  background-color: #0d1b7a;
  color: #ffffff;
  border-color: #0d1b7a;
}

/* Filter Bar & Toggle Diagram Buttons */
.filter-group {
  display: flex;
  align-items: center;
  gap: 12px;
}

.chart-type-toggle {
  display: flex;
  background: #e2e8f0;
  padding: 3px;
  border-radius: 6px;
}

.type-btn {
  background: transparent;
  border: none;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
  border-radius: 4px;
}

.type-btn.active {
  background: #ffffff;
  color: #0d1b7a;
  font-weight: 700;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.select-wilayah {
  background-color: #0d1b7a;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  font-size: 12px;
  outline: none;
  cursor: pointer;
}

/* Chart Container Cards */
.charts-wrapper {
  max-width: 1000px;
  margin: 0 auto 50px auto;
  padding: 0 20px;
}

.chart-group {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.chart-card {
  background: white;
  border: 4px solid #3b82f6;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}

.chart-card h2 {
  font-size: 22px;
  font-weight: 800;
  color: #1e293b;
  margin: 0 0 16px 0;
}

.chart-box {
  position: relative;
  height: 320px;
  width: 100%;
}

.loading-state, .error-state {
  text-align: center;
  padding: 60px;
  color: #64748b;
}

.error-state {
  color: #ef4444;
}
</style>