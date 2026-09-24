<template>
  <div class="home-container">
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

        <button @click="handleLogout" class="btn-logout">Logout</button>
      </nav>
    </header>

    <!-- Hero Section -->
    <main class="hero-section">
      <img src="/pabrik.jpeg" alt="Pabrik Charoen Pokphand" class="hero-bg" />
      <div class="hero-overlay"></div>
      <div class="company-badge">
        <div class="greeting-box">
          <h2>{{ greetingMessage }}</h2>
        </div>
        <h1>PT Charoen Pokphand Eastern Indonesia Tbk</h1>
        <p>Personalia & General Affairs Portal</p>
      </div>
    </main>

    <!-- Modal Notification STNK Pop-up -->
    <div v-if="showNotificationModal" class="notification-overlay" @click.self="closeNotification">
      <div class="notification-card">
        <div class="notification-header">
          <div class="notification-header-title">
            <span>🔔</span>
            <span class="notification-title-text">{{ stnkNotifications.length }} Pengingat Masa Pajak STNK</span>
          </div>
          <button @click="closeNotification" class="close-btn" title="Tutup">&times;</button>
        </div>

        <div class="notification-body">
          <div v-if="loadingStats" class="notification-loading">
            Memeriksa jadwal perpanjangan STNK dari Google Sheets...
          </div>
          <div v-else-if="stnkNotifications.length === 0" class="notification-empty">
            🎉 Tidak ada kendaraan yang mendekati jatuh tempo STNK.
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
                  <span>Merek/Tipe: {{ item.merek }} {{ item.type }}</span> • 
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
          <NuxtLink class="view-all-link" to="/asset-car" @click="closeNotification">Lihat Semua Asset Car →</NuxtLink>
        </div>
      </div>
    </div>

    <!-- Dashboard Content -->
    <section class="dashboard-content">
      <div class="stats-grid">
        <NuxtLink class="stat-card" to="/data-karyawan">
          <div class="stat-icon bg-blue">👥</div>
          <div class="stat-info">
            <span class="stat-title">Total Data MP</span>
            <span v-if="loadingStats" class="stat-value text-sub">Memuat...</span>
            <span v-else class="stat-value">{{ totalKaryawan }} Karyawan</span>
          </div>
        </NuxtLink>

        <NuxtLink class="stat-card" to="/asset-car">
          <div class="stat-icon bg-green">🚗</div>
          <div class="stat-info">
            <span class="stat-title">Total Aset GA</span>
            <span v-if="loadingStats" class="stat-value text-sub">Memuat...</span>
            <span v-else class="stat-value">{{ totalAssetCar }} Unit Mobil</span>
          </div>
        </NuxtLink>

        <NuxtLink class="stat-card" to="/asset-pga">
          <div class="stat-icon bg-purple">🏢</div>
          <div class="stat-info">
            <span class="stat-title">Total Fasilitas</span>
            <span v-if="loadingStats" class="stat-value text-sub">Memuat...</span>
            <span v-else class="stat-value">{{ totalAssetPga }} Asset PGA</span>
          </div>
        </NuxtLink>
      </div>

      <!-- Quick Access Section -->
      <div class="quick-section">
        <h3>Akses Cepat Modul Internal</h3>
        <div class="quick-grid">
          <NuxtLink class="quick-card" to="/data-karyawan">
            <span class="quick-icon">📂</span>
            <span class="quick-label">Data Karyawan</span>
          </NuxtLink>

          <NuxtLink class="quick-card" to="/visualisasi-data">
            <span class="quick-icon">📊</span>
            <span class="quick-label">Visualisasi Data</span>
          </NuxtLink>

          <NuxtLink class="quick-card" to="/asset-car">
            <span class="quick-icon">🚘</span>
            <span class="quick-label">Asset Car</span>
          </NuxtLink>

          <NuxtLink class="quick-card" to="/asset-pga">
            <span class="quick-icon">🏢</span>
            <span class="quick-label">Asset PGA</span>
          </NuxtLink>

          <NuxtLink class="quick-card" to="/WIPHRA">
            <span class="quick-icon">📋</span>
            <span class="quick-label">Work In Progress</span>
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const userName = ref('')
const greetingMessage = ref('')

const totalKaryawan = ref(0)
const totalAssetCar = ref(0)
const totalAssetPga = ref(0)
const loadingStats = ref(true)

const showNotificationModal = ref(true)
const stnkNotifications = ref([])

const spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'
const gidAssetCar = '117909656'
const gidKaryawan = '0'

// Parser CSV Handal (Mengabaikan Koma di dalam Tanda Kutip Teks)
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

// Parser Tanggal Format DD/MM/YYYY
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

// Format Teks Sisa Waktu
const getRemainingText = (days) => {
  if (days < 0) return `Sudah expired (${Math.abs(days)} hari lalu)`
  if (days === 0) return 'Jatuh tempo hari ini!'
  
  const weeks = Math.floor(days / 7)
  const remainingDays = days % 7

  if (weeks > 0 && remainingDays > 0) {
    return `Sisa ${weeks} minggu ${remainingDays} hari`
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

// Direct Fetch Langsung dari Google Sheets CSV (Bypass Backend / CORS)
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
        
        // Cari Posisi Indeks Kolom Secara Presisi Berdasarkan Header
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

          // Ambil Data Berdasarkan Indeks Kolom
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

              // Tampilkan seluruh kendaraan yang kadaluarsa (diffDays < 0) dan mendekati jatuh tempo (<= 30 hari)
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

  const savedName = localStorage.getItem('user_name') || 'Pengguna'
  userName.value = savedName

  const hour = new Date().getHours()
  let timeGreeting = 'Selamat Pagi'

  if (hour >= 11 && hour < 15) {
    timeGreeting = 'Selamat Siang'
  } else if (hour >= 15 && hour < 18) {
    timeGreeting = 'Selamat Sore'
  } else if (hour >= 18 || hour < 4) {
    timeGreeting = 'Selamat Malam'
  }

  greetingMessage.value = `${timeGreeting}, ${savedName}!`

  fetchDashboardStats()
})

const handleLogout = () => {
  localStorage.removeItem('user_name')
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

/* Modal Popup Notifikasi STNK */
.notification-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(15, 23, 42, 0.55);
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
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: 85vh;
}

.notification-header {
  background: #0284c7;
  color: #ffffff;
  padding: 14px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-header-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 15px;
  font-weight: 700;
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
  padding: 16px 20px;
  overflow-y: auto;
  flex: 1;
}

.notification-loading, .notification-empty {
  text-align: center;
  padding: 24px 12px;
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
}

.notification-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.notification-item {
  display: flex;
  gap: 12px;
  padding: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  align-items: flex-start;
}

.item-icon {
  font-size: 22px;
  background: #e0f2fe;
  padding: 8px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
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
}

.item-badge {
  font-size: 11px;
  padding: 3px 8px;
  border-radius: 12px;
  font-weight: 700;
}

.badge-danger {
  background-color: #fee2e2;
  color: #dc2626;
}

.badge-warning {
  background-color: #fef3c7;
  color: #d97706;
}

.badge-info {
  background-color: #e0f2fe;
  color: #0284c7;
}

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
  padding: 12px 20px;
  background: #f1f5f9;
  border-top: 1px solid #e2e8f0;
  text-align: center;
}

.view-all-link {
  font-size: 13px;
  font-weight: 600;
  color: #0284c7;
  text-decoration: none;
}

.view-all-link:hover {
  text-decoration: underline;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
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

.btn-logout {
  background: #ef4444;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  margin-left: 10px;
  transition: background 0.2s;
}

.btn-logout:hover {
  background: #dc2626;
}

/* Hero Section */
.hero-section {
  position: relative;
  width: 100%;
  height: 280px;
  overflow: hidden;
}

.hero-bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(13, 27, 122, 0.3) 0%, rgba(13, 27, 122, 0.75) 100%);
}

.company-badge {
  position: absolute;
  bottom: 30px;
  left: 48px;
  color: white;
  z-index: 2;
}

.greeting-box h2 {
  font-size: 20px;
  font-weight: 700;
  color: #fbbf24;
  margin: 0 0 6px 0;
  text-shadow: 0 2px 4px rgba(0,0,0,0.4);
}

.company-badge h1 {
  font-size: 24px;
  font-weight: 800;
  margin: 0;
  letter-spacing: -0.5px;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.company-badge p {
  margin: 4px 0 0 0;
  font-size: 14px;
  opacity: 0.9;
  font-weight: 400;
}

/* Dashboard Section */
.dashboard-content {
  max-width: 1100px;
  margin: 32px auto 40px auto;
  padding: 0 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background: #ffffff;
  padding: 18px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  text-decoration: none;
  transition: all 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
  border-color: #cbd5e1;
}

.stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
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
}

.stat-value {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  margin-top: 2px;
}

.text-sub {
  color: #94a3b8;
  font-size: 13px;
  font-weight: 500;
}

.quick-section {
  background: #ffffff;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}

.quick-section h3 {
  margin: 0 0 16px 0;
  font-size: 15px;
  color: #0f172a;
  font-weight: 700;
}

.quick-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
}

.quick-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  text-decoration: none;
  color: #334155;
  font-weight: 600;
  font-size: 13px;
  transition: all 0.2s ease;
}

.quick-card:hover {
  background: #0d1b7a;
  color: #ffffff;
  border-color: #0d1b7a;
}

.quick-icon {
  font-size: 16px;
}

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: 1fr; }
}
</style>