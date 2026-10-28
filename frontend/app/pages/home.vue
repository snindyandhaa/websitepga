<template>
  <div class="home-container">
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
          <button class="dropdown-btn">More ▾</button>
          <div class="dropdown-content">
            <NuxtLink to="/data-summary">Data Summary</NuxtLink>
            <NuxtLink to="/bpjs">BPJS</NuxtLink>
          </div>
        </div>

        <button @click="handleLogout" class="btn-logout">Logout</button>
      </nav>
    </header>

    <main class="hero-section">
      <img 
        src="/pabrik.png" 
        alt="Pabrik Charoen Pokphand" 
        class="hero-bg"
      />
      <div class="hero-overlay"></div>
      
      <div class="hero-greeting-box">
        <div class="greeting-chip">
          <span class="greeting-dot"></span>
          <span class="greeting-text">{{ greetingTime }}, <strong>{{ userName }}</strong>! 👋</span>
        </div>
        <h1 class="company-title">{{ companyName }}</h1>
        <p class="company-subtitle">Personalia & General Affairs Portal</p>
      </div>
    </main>

    <div v-if="showNotificationModal" class="notification-overlay" @click.self="closeNotification">
      <div class="notification-card">
        <div class="notification-header">
          <div class="notification-header-title">
            <div class="bell-badge">
              <span>🔔</span>
            </div>
            <span class="notification-title-text">{{ stnkNotifications.length }} Pengingat Masa Pajak STNK</span>
          </div>
          <button @click="closeNotification" class="close-btn" title="Tutup">&times;</button>
        </div>

        <div class="notification-body">
          <div v-if="loadingStats" class="notification-loading">
            <div class="pulse-spinner"></div>
            <p>Memeriksa jadwal perpanjangan STNK dari Google Sheets...</p>
          </div>
          
          <div v-else-if="stnkNotifications.length === 0" class="notification-empty">
            <div class="empty-icon">🎉</div>
            <p>Tidak ada kendaraan yang mendekati jatuh tempo STNK.</p>
          </div>
          
          <div v-else class="notification-list">
            <div v-for="(item, index) in stnkNotifications" :key="index" class="notification-item">
              <div class="item-icon">🚗</div>
              <div class="item-details">
                <div class="item-title">
                  <strong>LICENSE NO: {{ item.licenseNo }}</strong> 
                  <span class="item-badge" :class="getDaysRemainingClass(item.daysRemaining)">
                    {{ getRemainingText(item.daysRemaining) }}
                  </span>
                </div>
                <div class="item-desc">
                  <span>Merek/Tipe: {{ item.merek }} {{ item.type }}</span>
                  <span>Pengguna: {{ item.nama }}</span>
                </div>
                <div class="item-date">
                  Jatuh Tempo Pajak: <strong>{{ item.pajakDateFormatted }}</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="notification-footer">
          <NuxtLink class="view-all-link" to="/asset-car" @click="closeNotification">
            Lihat Semua Asset Car <span class="arrow-icon">→</span>
          </NuxtLink>
        </div>
      </div>
    </div>

    <section class="dashboard-content">
      <div class="section-header">
        <div>
          <h2 class="section-title">Ringkasan Modul & Data Utama</h2>
          <p class="section-sub">Pantau jumlah data karyawan dan aset internal secara real-time</p>
        </div>
        <div class="live-status-pill">
          <span class="status-dot"></span>
          <span>Sistem Terhubung</span>
        </div>
      </div>

      <div class="stats-grid">
        <NuxtLink class="stat-card" to="/data-karyawan">
          <div class="stat-card-inner">
            <div class="stat-icon bg-blue">👥</div>
            <div class="stat-info">
              <span class="stat-title">Total Data MP</span>
              <span v-if="loadingStats" class="stat-value text-sub">Memuat...</span>
              <span v-else class="stat-value">{{ totalKaryawan }} <small>Karyawan</small></span>
            </div>
          </div>
          <div class="card-footer-link">
            <span>Buka Data Karyawan</span>
            <span class="hover-arrow">→</span>
          </div>
        </NuxtLink>

        <NuxtLink class="stat-card" to="/asset-car">
          <div class="stat-card-inner">
            <div class="stat-icon bg-green">🚗</div>
            <div class="stat-info">
              <span class="stat-title">Total Aset GA</span>
              <span v-if="loadingStats" class="stat-value text-sub">Memuat...</span>
              <span v-else class="stat-value">{{ totalAssetCar }} <small>Unit Mobil</small></span>
            </div>
          </div>
          <div class="card-footer-link">
            <span>Kelola Asset Car</span>
            <span class="hover-arrow">→</span>
          </div>
        </NuxtLink>

        <NuxtLink class="stat-card" to="/asset-pga">
          <div class="stat-card-inner">
            <div class="stat-icon bg-purple">🏢</div>
            <div class="stat-info">
              <span class="stat-title">Total Fasilitas</span>
              <span v-if="loadingStats" class="stat-value text-sub">Memuat...</span>
              <span v-else class="stat-value">{{ totalAssetPga }} <small>Asset PGA</small></span>
            </div>
          </div>
          <div class="card-footer-link">
            <span>Cek Fasilitas PGA</span>
            <span class="hover-arrow">→</span>
          </div>
        </NuxtLink>
      </div>

      <div class="quick-section">
        <div class="quick-header">
          <h3>⚡ Akses Cepat Modul Utama</h3>
          <span class="quick-badge">Modul Pilihan</span>
        </div>

        <div class="quick-grid">
          <NuxtLink class="quick-card" to="/data-karyawan">
            <div class="quick-icon-wrapper">📂</div>
            <div class="quick-text">
              <span class="quick-label">Data Karyawan</span>
              <span class="quick-desc">Database Manpower</span>
            </div>
          </NuxtLink>

          <NuxtLink class="quick-card" to="/visualisasi-data">
            <div class="quick-icon-wrapper">📊</div>
            <div class="quick-text">
              <span class="quick-label">Visualisasi Data</span>
              <span class="quick-desc">Grafik & Statistik</span>
            </div>
          </NuxtLink>

          <NuxtLink class="quick-card" to="/employee-rotation">
            <div class="quick-icon-wrapper">🔄</div>
            <div class="quick-text">
              <span class="quick-label">Employee Rotation</span>
              <span class="quick-desc">Rotasi & Mutasi</span>
            </div>
          </NuxtLink>

          <NuxtLink class="quick-card" to="/asset-car">
            <div class="quick-icon-wrapper">🚘</div>
            <div class="quick-text">
              <span class="quick-label">Asset Car</span>
              <span class="quick-desc">Armada & Kendaraan</span>
            </div>
          </NuxtLink>

          <NuxtLink class="quick-card" to="/asset-pga">
            <div class="quick-icon-wrapper">🏢</div>
            <div class="quick-text">
              <span class="quick-label">Asset PGA</span>
              <span class="quick-desc">Fasilitas & Inventaris</span>
            </div>
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const userName = ref('Maulana Siddiq')
const companyName = ref('PT Charoen Pokphand Eastern Indonesia Tbk')
const greetingTime = ref('Selamat Datang')

const totalKaryawan = ref(0)
const totalAssetCar = ref(0)
const totalAssetPga = ref(0)
const loadingStats = ref(true)

const showNotificationModal = ref(true)
const stnkNotifications = ref([])

const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gidAssetCar = '117909656'
const gidKaryawan = '0'

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

const parseDate = (dateStr) => {
  if (!dateStr || dateStr === '-') return null
  const cleanStr = dateStr.toString().trim()
  
  const parts = cleanStr.split(/[\/\-]/)
  if (parts.length === 3) {
    const day = parseInt(parts[0], 10)
    const month = parseInt(parts[1], 10) - 1
    const year = parseInt(parts[2], 10)
    if (!isNaN(day) && !isNaN(month) && !isNaN(year)) {
      return new Date(year, month, day)
    }
  }
  
  const dateObj = new Date(cleanStr)
  return isNaN(dateObj.getTime()) ? null : dateObj
}

const getRemainingText = (days) => {
  if (days < 0) return `Expired (${Math.abs(days)} hari lalu)`
  if (days === 0) return 'Jatuh tempo hari ini!'
  
  const weeks = Math.floor(days / 7)
  const remainingDays = days % 7

  if (weeks > 0 && remainingDays > 0) {
    return `Sisa ${weeks} mgg ${remainingDays} hri`
  } else if (weeks > 0 && remainingDays === 0) {
    return `Sisa ${weeks} minggu`
  } else {
    return `Sisa ${days} hari`
  }
}

const getDaysRemainingClass = (days) => {
  if (days <= 0) return 'badge-danger'
  if (days <= 7) return 'badge-danger'
  if (days <= 14) return 'badge-warning'
  return 'badge-info'
}

const closeNotification = () => {
  showNotificationModal.value = false
}

const fetchDashboardStats = async () => {
  loadingStats.value = true
  try {
    const [csvKaryawan, csvCar] = await Promise.allSettled([
      $fetch(`https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gidKaryawan}`, { responseType: 'text' }),
      $fetch(`https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv&gid=${gidAssetCar}`, { responseType: 'text' })
    ])

    if (csvKaryawan.status === 'fulfilled' && csvKaryawan.value) {
      const lines = csvKaryawan.value.split('\n').filter(l => l.trim() !== '')
      totalKaryawan.value = Math.max(0, lines.length - 1)
    }

    if (csvCar.status === 'fulfilled' && csvCar.value) {
      const lines = csvCar.value.split('\n').map(l => l.replace('\r', ''))
      if (lines.length >= 2) {
        const rawHeaders = lines.shift() || ''
        const headers = parseCsvLine(rawHeaders).map(h => h.trim())
        
        const idxPajak = headers.findIndex(h => h.toLowerCase().includes('pajak'))
        const idxLicense = headers.findIndex(h => h.toLowerCase().includes('license'))
        const idxMerek = headers.findIndex(h => h.toLowerCase().includes('merek'))
        const idxType = headers.findIndex(h => h.toLowerCase().includes('type'))
        const idxUser = headers.findIndex(h => h.toLowerCase().includes('user'))

        const carCount = lines.filter(l => l.trim() !== '').length
        totalAssetCar.value = carCount
        totalAssetPga.value = carCount

        const today = new Date()
        today.setHours(0, 0, 0, 0)

        const upcomingNotifs = []

        lines.forEach(line => {
          if (line.trim() === '') return
          const row = parseCsvLine(line)

          const rawPajak = idxPajak !== -1 ? row[idxPajak] : ''
          const licenseNo = idxLicense !== -1 && row[idxLicense] ? row[idxLicense] : 'Tanpa Plat'
          const merek = idxMerek !== -1 && row[idxMerek] ? row[idxMerek] : ''
          const type = idxType !== -1 && row[idxType] ? row[idxType] : ''
          const nama = idxUser !== -1 && row[idxUser] ? row[idxUser] : 'N/A'

          if (rawPajak) {
            const pajakDate = parseDate(rawPajak)
            if (pajakDate) {
              const diffTime = pajakDate.getTime() - today.getTime()
              const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

              if (diffDays <= 30) {
                upcomingNotifs.push({
                  licenseNo,
                  merek,
                  type,
                  nama,
                  pajakDateFormatted: pajakDate.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                  }),
                  daysRemaining: diffDays
                })
              }
            }
          }
        })

        upcomingNotifs.sort((a, b) => a.daysRemaining - b.daysRemaining)
        stnkNotifications.value = upcomingNotifs
      }
    }
  } catch (err) {
    console.error('Error fetching dashboard stats:', err)
  } finally {
    loadingStats.value = false
  }
}

onMounted(() => {
  showNotificationModal.value = true

  const savedName = localStorage.getItem('user_name') || localStorage.getItem('username') || 'Maulana Siddiq'
  const savedCompany = localStorage.getItem('company_name') || 'PT Charoen Pokphand Eastern Indonesia Tbk'
  
  userName.value = savedName
  companyName.value = savedCompany

  const hour = new Date().getHours()
  if (hour >= 4 && hour < 11) {
    greetingTime.value = 'Selamat Pagi'
  } else if (hour >= 11 && hour < 15) {
    greetingTime.value = 'Selamat Siang'
  } else if (hour >= 15 && hour < 18) {
    greetingTime.value = 'Selamat Sore'
  } else {
    greetingTime.value = 'Selamat Malam'
  }

  fetchDashboardStats()
})

const handleLogout = () => {
  localStorage.removeItem('user_name')
  localStorage.removeItem('username')
  localStorage.removeItem('company_name')
  navigateTo('/')
}
</script>

<style scoped>
.home-container {
  background-color: #f1f5f9;
  min-height: 100vh;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  position: relative;
}

/* Navbar */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 48px;
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
  position: sticky;
  top: 0;
  z-index: 50;
}

.nav-logo {
  height: 42px;
  object-fit: contain;
  transition: transform 0.2s;
}

.nav-logo:hover {
  transform: scale(1.02);
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
  padding: 8px 14px;
  font-weight: 600;
  border-radius: 8px;
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
  min-width: 200px;
  box-shadow: 0 12px 30px -5px rgba(0, 0, 0, 0.12);
  z-index: 20;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  padding: 8px;
  animation: fadeIn 0.15s ease-out;
}

.dropdown-content a {
  color: #475569;
  padding: 10px 14px;
  text-decoration: none;
  display: block;
  font-size: 13px;
  font-weight: 500;
  border-radius: 6px;
  transition: all 0.15s;
}

.dropdown-content a:hover {
  background-color: #eff6ff;
  color: #0d1b7a;
  transform: translateX(3px);
}

.dropdown:hover .dropdown-content {
  display: block;
}

.btn-logout {
  background: #ef4444;
  color: white;
  padding: 8px 18px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  margin-left: 10px;
  box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);
  transition: all 0.2s;
}

.btn-logout:hover {
  background: #dc2626;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(220, 38, 38, 0.35);
}

/* Hero Section Banner */
.hero-section {
  position: relative;
  width: 100%;
  height: 320px;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(13, 27, 122, 0.2) 0%, rgba(13, 27, 122, 0.8) 100%);
  z-index: 1;
}

.hero-greeting-box {
  position: absolute;
  bottom: 24px;
  left: 24px;
  z-index: 10;
  background: rgba(13, 27, 122, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  padding: 16px 22px;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
  max-width: 80%;
}

.greeting-chip {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.greeting-dot {
  width: 8px;
  height: 8px;
  background-color: #fbbf24;
  border-radius: 50%;
  box-shadow: 0 0 8px #fbbf24;
}

.greeting-text {
  font-size: 15px;
  font-weight: 600;
  color: #fbbf24;
}

.greeting-text strong {
  color: #ffffff;
  font-weight: 800;
}

.company-title {
  font-size: 20px;
  font-weight: 800;
  color: #ffffff;
  margin: 4px 0 0 0;
  line-height: 1.2;
}

.company-subtitle {
  margin: 2px 0 0 0;
  font-size: 12px;
  color: #cbd5e1;
  font-weight: 500;
}

/* Dashboard Section */
.dashboard-content {
  width: 100%;
  max-width: 1100px;
  margin: 32px auto 50px auto;
  padding: 0 24px;
  box-sizing: border-box;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 20px;
}

.section-title {
  font-size: 18px;
  font-weight: 800;
  color: #0d1b7a;
  margin: 0 0 4px 0;
}

.section-sub {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.live-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ffffff;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  color: #10b981;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.status-dot {
  width: 7px;
  height: 7px;
  background-color: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 6px #10b981;
}

/* Stats Grid Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 28px;
}

.stat-card {
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  border: 1px solid #e2e8f0;
  text-decoration: none;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -6px rgba(13, 27, 122, 0.12);
  border-color: #cbd5e1;
}

.stat-card-inner {
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.bg-blue { background: #eff6ff; color: #1d4ed8; }
.bg-green { background: #f0fdf4; color: #15803d; }
.bg-purple { background: #faf5ff; color: #7e22ce; }

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-title {
  font-size: 11px;
  color: #64748b;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
  margin-top: 2px;
  line-height: 1.2;
}

.stat-value small {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
}

.text-sub {
  color: #94a3b8;
  font-size: 13px;
  font-weight: 500;
}

.card-footer-link {
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
  padding: 10px 20px;
  font-size: 12px;
  font-weight: 600;
  color: #0d1b7a;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: background 0.2s;
}

.stat-card:hover .card-footer-link {
  background: #eff6ff;
}

.hover-arrow {
  transition: transform 0.2s ease;
}

.stat-card:hover .hover-arrow {
  transform: translateX(4px);
}

/* Quick Access Section */
.quick-section {
  background: #ffffff;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  border: 1px solid #e2e8f0;
}

.quick-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.quick-header h3 {
  margin: 0;
  font-size: 16px;
  color: #0d1b7a;
  font-weight: 800;
}

.quick-badge {
  font-size: 11px;
  background: #f1f5f9;
  color: #475569;
  padding: 4px 10px;
  border-radius: 12px;
  font-weight: 600;
}

.quick-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  gap: 14px;
}

.quick-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.quick-card:hover {
  background: #0d1b7a;
  border-color: #0d1b7a;
  transform: translateY(-3px);
  box-shadow: 0 8px 16px -4px rgba(13, 27, 122, 0.25);
}

.quick-icon-wrapper {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  flex-shrink: 0;
}

.quick-text {
  display: flex;
  flex-direction: column;
}

.quick-label {
  font-weight: 700;
  font-size: 13px;
  color: #1e293b;
  transition: color 0.2s;
}

.quick-desc {
  font-size: 11px;
  color: #64748b;
  margin-top: 1px;
  transition: color 0.2s;
}

.quick-card:hover .quick-label {
  color: #ffffff;
}

.quick-card:hover .quick-desc {
  color: #93c5fd;
}

/* Modal Popup Notifikasi STNK */
.notification-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 16px;
  animation: fadeIn 0.25s ease-out;
}

.notification-card {
  background: #ffffff;
  width: 100%;
  max-width: 520px;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: 85vh;
  border: 1px solid #e2e8f0;
}

.notification-header {
  background: #0284c7;
  color: #ffffff;
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-header-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 15px;
  font-weight: 700;
}

.bell-badge {
  background: rgba(255, 255, 255, 0.2);
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}

.close-btn {
  background: transparent;
  border: none;
  color: #ffffff;
  font-size: 24px;
  cursor: pointer;
  line-height: 1;
  opacity: 0.8;
  transition: opacity 0.2s;
}

.close-btn:hover {
  opacity: 1;
}

.notification-body {
  padding: 18px;
  overflow-y: auto;
  flex: 1;
}

.notification-loading, .notification-empty {
  text-align: center;
  padding: 32px 16px;
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
}

.pulse-spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #e0f2fe;
  border-top-color: #0284c7;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 12px auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 32px;
  margin-bottom: 8px;
}

.notification-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.notification-item {
  display: flex;
  gap: 12px;
  padding: 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  align-items: flex-start;
  transition: border-color 0.2s;
}

.notification-item:hover {
  border-color: #cbd5e1;
}

.item-icon {
  font-size: 20px;
  background: #e0f2fe;
  padding: 8px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.item-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  color: #0f172a;
  flex-wrap: wrap;
  gap: 6px;
}

.item-badge {
  font-size: 11px;
  padding: 3px 8px;
  border-radius: 12px;
  font-weight: 700;
}

.badge-danger { background-color: #fee2e2; color: #dc2626; }
.badge-warning { background-color: #fef3c7; color: #d97706; }
.badge-info { background-color: #e0f2fe; color: #0284c7; }

.item-desc {
  font-size: 12px;
  color: #475569;
}

.item-date {
  font-size: 11px;
  color: #64748b;
  margin-top: 2px;
}

.notification-footer {
  padding: 14px 20px;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
  text-align: center;
}

.view-all-link {
  font-size: 13px;
  font-weight: 700;
  color: #0284c7;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.view-all-link:hover .arrow-icon {
  transform: translateX(4px);
}

.arrow-icon {
  transition: transform 0.2s;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
}

/* Responsive Screen Handling */
@media screen and (max-width: 768px) {
  .navbar { padding: 12px 16px; flex-direction: column; gap: 10px; }
  .nav-menu { flex-wrap: wrap; justify-content: center; }
  .hero-section { height: 280px; }
  .hero-greeting-box { left: 16px; bottom: 16px; max-width: 90%; padding: 12px 16px; }
  .company-title { font-size: 16px; }
  .section-header { flex-direction: column; align-items: flex-start; gap: 8px; }

  .dashboard-content {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    margin: 16px auto 24px;
    padding: 0 12px;
  }

  .stats-grid {
    grid-template-columns: minmax(0, 1fr);
    gap: 12px;
  }

  .stat-card {
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
  }
}
</style>