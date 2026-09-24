<template>
  <div class="wiphra-container">
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
            <NuxtLink to="/WIPHRA">Work In Progress HC & GA</NuxtLink>
            <NuxtLink to="/budget-department">Budget Department</NuxtLink>
          </div>
        </div>

        <NuxtLink to="/" class="btn-logout">Logout</NuxtLink>
      </nav>
    </header>

    <main class="content-wrapper">
      <div class="table-container">
        <div class="table-header-banner">
          <h2>work in progress HC & GA</h2>
        </div>

        <div class="table-scroll">
          <table class="wip-table">
            <thead>
              <tr>
                <th class="col-kegiatan">Kegiatan</th>
                <th class="col-pic">PIC</th>
                <th class="col-duedate">Due Date</th>
                <th class="col-progress">Progress</th>
                <th class="col-estimasi">Estimasi</th>
                <th class="col-information">Information</th>
                <th class="col-kendala">Kendala</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in wipData" :key="'wip-' + index">
                <td>{{ item.kegiatan || '' }}</td>
                <td>{{ item.pic || '' }}</td>
                <td>{{ item.dueDate || '' }}</td>
                <td>{{ item.progress || '' }}</td>
                <td>{{ item.estimasi || '' }}</td>
                <td>{{ item.information || '' }}</td>
                <td>{{ item.kendala || '' }}</td>
              </tr>
              <template v-if="wipData.length === 0">
                <tr v-for="n in 6" :key="'empty-' + n">
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Data Reactive (Kosongkan array [] jika ingin baris bergaris polos saja)
const wipData = ref([
  // Contoh jika mau diisi:
  // { kegiatan: 'Pengadaan Seragam', pic: 'Budi', dueDate: '2026-09-10', progress: '50%', estimasi: '2 Minggu', information: 'Proses Vendor', kendala: 'Stok kain terbatas' }
])
</script>

<style scoped>
.wiphra-container {
  background-color: #ffffff;
  min-height: 100vh;
}

/* Navbar Style */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 40px;
  background-color: #ffffff;
  border-bottom: 1px solid #e2e8f0;
}

.nav-logo {
  height: 45px;
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: 15px;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown button {
  background: none;
  border: none;
  font-size: 13px;
  cursor: pointer;
  color: #333333;
  padding: 4px;
  font-weight: 500;
}

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

.dropdown-content a {
  color: #333;
  padding: 10px 14px;
  text-decoration: none;
  display: block;
  font-size: 12px;
}

.dropdown-content a:hover {
  background-color: #f1f5f9;
  color: #0d1b7a;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.btn-logout {
  background: #ef4444;
  color: white;
  padding: 6px 14px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 12px;
  font-weight: 600;
}

/* Layout Table Container */
.content-wrapper {
  max-width: 1100px;
  margin: 40px auto;
  padding: 0 20px;
}

.table-container {
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  overflow: hidden;
  background: #ffffff;
}

/* Banner Judul Biru Tua */
.table-header-banner {
  background-color: #0d1b7a;
  color: #ffffff;
  text-align: center;
  padding: 12px 16px;
}

.table-header-banner h2 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

/* Styling Tabel */
.table-scroll {
  overflow-x: auto;
}

.wip-table {
  width: 100%;
  border-collapse: collapse;
  background-color: #ffffff;
}

.wip-table th, 
.wip-table td {
  border: 1px solid #94a3b8;
  padding: 8px 12px;
  font-size: 13px;
  color: #0f172a;
}

.wip-table th {
  background-color: #ffffff;
  font-weight: 700;
  text-align: left;
}

/* Lebar Kolom */
.col-kegiatan { width: 18%; }
.col-pic { width: 22%; }
.col-duedate { width: 10%; }
.col-progress { width: 12%; }
.col-estimasi { width: 14%; }
.col-information { width: 12%; }
.col-kendala { width: 12%; }

.wip-table tbody tr {
  height: 38px;
}
</style>
