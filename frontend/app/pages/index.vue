<template>
  <div class="login-container">
    <div class="login-card">
      <!-- Logo Utama -->
      <div class="logo-box">
        <img src="/logo.png" alt="Logo Singa" class="lion-logo" />
      </div>

      <!-- Judul Portal -->
      <div class="login-header">
        <h2>Selamat Datang</h2>
        <p>Silakan masuk menggunakan NIK dan Password Anda</p>
      </div>

      <!-- Form Login -->
      <form @submit.prevent="handleLogin" class="login-form">
        <!-- Input NIK -->
        <div class="form-group">
          <label>NIK Karyawan</label>
          <div class="input-wrapper">
            <span class="input-icon">👤</span>
            <input 
              type="text" 
              v-model="nik" 
              maxlength="8"
              @input="validateNikInput"
              placeholder="Masukkan 8 digit NIK"
              required 
            />
          </div>
        </div>

        <!-- Input Password -->
        <div class="form-group">
          <label>Password</label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input 
              :type="showPassword ? 'text' : 'password'" 
              v-model="password" 
              placeholder="Masukkan password"
              required 
            />
            <button 
              type="button" 
              class="btn-toggle-eye" 
              @click="showPassword = !showPassword"
              tabindex="-1"
              aria-label="Toggle password visibility"
            >
              <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                <line x1="2" y1="2" x2="22" y2="22"></line>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <!-- Alert Pesan Error Login -->
        <div v-if="errorMessage" class="error-banner">
          <span class="error-icon">⚠️</span>
          <span>{{ errorMessage }}</span>
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="btn-login" :disabled="isSubmitting">
          <span v-if="!isSubmitting">Login Portal</span>
          <span v-else class="loading-state">Memproses...</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const nik = ref('')
const password = ref('')
const showPassword = ref(false)
const errorMessage = ref('')
const isSubmitting = ref(false)

// Data NIK Terdaftar
const usersDatabase = [
  { nik: '21801089', name: 'Randy Utama' },
  { nik: '21500432', name: 'Maulana Siddiq' },
  { nik: '22209297', name: 'Dinul Afdhal' },
  { nik: '21700959', name: 'Baso Alim Bahri' },
]

const validateNikInput = (event) => {
  nik.value = event.target.value.replace(/\D/g, '')
  if (errorMessage.value) errorMessage.value = ''
}

const handleLogin = async () => {
  errorMessage.value = ''

  if (nik.value.length !== 8) {
    errorMessage.value = 'NIK harus terdiri dari 8 digit angka.'
    return
  }

  isSubmitting.value = true

  // Simulasi delay login
  setTimeout(() => {
    const foundUser = usersDatabase.find(u => u.nik === nik.value)

    if (foundUser && password.value === 'admin123') {
      // Simpan Data User dan Perusahaan ke LocalStorage
      localStorage.setItem('user_name', foundUser.name)
      localStorage.setItem('company_name', 'PT Charoen Pokphand Eastern Indonesia Tbk')
      
      navigateTo('/home')
    } else {
      errorMessage.value = 'NIK atau Password yang Anda masukkan salah.'
      isSubmitting.value = false
    }
  }, 400)
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  padding: 20px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  box-sizing: border-box;
}

.login-card {
  width: 100%;
  max-width: 380px;
  background: #ffffff;
  border-radius: 16px;
  padding: 36px 28px;
  box-shadow: 0 10px 25px -5px rgba(13, 27, 122, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: transform 0.2s;
}

.logo-box {
  margin-bottom: 16px;
  display: flex;
  justify-content: center;
}

.lion-logo {
  width: 100px;
  height: auto;
  object-fit: contain;
}

.login-header {
  text-align: center;
  margin-bottom: 24px;
}

.login-header h2 {
  font-size: 20px;
  font-weight: 800;
  color: #0d1b7a;
  margin: 0 0 6px 0;
}

.login-header p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.login-form {
  width: 100%;
}

.form-group {
  margin-bottom: 18px;
}

.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 6px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  background-color: #0d1b7a;
  border-radius: 10px;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  overflow: hidden;
}

.input-wrapper:focus-within {
  border-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2);
}

.input-icon {
  padding-left: 14px;
  font-size: 14px;
  opacity: 0.8;
}

.input-wrapper input {
  width: 100%;
  height: 44px;
  padding: 0 14px 0 10px;
  border: none;
  background: transparent;
  color: #ffffff;
  font-size: 14px;
  font-weight: 500;
  outline: none;
  box-sizing: border-box;
}

.input-wrapper input::placeholder {
  color: rgba(255, 255, 255, 0.5);
}

.btn-toggle-eye {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}

.btn-toggle-eye:hover {
  color: #ffffff;
}

.error-banner {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 16px;
  animation: fadeIn 0.2s ease-in-out;
}

.error-icon {
  font-size: 14px;
}

.btn-login {
  width: 100%;
  height: 46px;
  border-radius: 10px;
  border: none;
  background-color: #0d1b7a;
  color: #ffffff;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  margin-top: 6px;
  box-shadow: 0 4px 10px rgba(13, 27, 122, 0.25);
  transition: all 0.2s ease;
}

.btn-login:hover:not(:disabled) {
  background-color: #1428a3;
  transform: translateY(-1px);
  box-shadow: 0 6px 14px rgba(13, 27, 122, 0.35);
}

.btn-login:active:not(:disabled) {
  transform: translateY(0);
}

.btn-login:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.loading-state {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}

@media screen and (max-width: 480px) {
  .login-card {
    padding: 28px 20px;
    border-radius: 14px;
  }
  
  .lion-logo {
    width: 85px;
  }

  .login-header h2 {
    font-size: 18px;
  }
}
</style>