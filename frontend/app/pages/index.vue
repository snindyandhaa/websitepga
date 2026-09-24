<template>
  <div class="login-container">
    <div class="login-wrapper">
      <!-- Logo Singa Utama -->
      <div class="logo-box">
        <img :src="'/logo.png'" alt="Logo Singa" class="lion-logo" />
      </div>

      <!-- Form Login -->
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label>NIK</label>
          <input 
            type="text" 
            v-model="nik" 
            maxlength="8"
            @input="validateNikInput"
            placeholder="8 digit NIK"
            required 
          />
        </div>

        <div class="form-group">
          <label>Password</label>
          <div class="input-password-wrapper">
            <input 
              :type="showPassword ? 'text' : 'password'" 
              v-model="password" 
              required 
            />
            <button 
              type="button" 
              class="btn-toggle-eye" 
              @click="showPassword = !showPassword"
              tabindex="-1"
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

        <button type="submit" class="btn-login">Login</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const nik = ref('')
const password = ref('')
const showPassword = ref(false)

// Data NIK Terdaftar
const usersDatabase = [
  { nik: '10000001', name: 'Pak Randy Utama' },
  { nik: '10000002', name: 'Pak Budi Santoso' },
  { nik: '10000003', name: 'Ibu Siti Aminah' },
]

const validateNikInput = (event) => {
  nik.value = event.target.value.replace(/\D/g, '')
}

const handleLogin = () => {
  if (nik.value.length !== 8) {
    alert('NIK harus terdiri dari tepat 8 digit angka!')
    return
  }

  const foundUser = usersDatabase.find(u => u.nik === nik.value)

  if (foundUser && password.value === 'admin123') {
    localStorage.setItem('user_name', foundUser.name)
    navigateTo('/home')
  } else {
    alert('NIK atau Password salah!\nGunakan NIK terdaftar (contoh: 10000001) dan Password: admin123')
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #ffffff;
}

.login-wrapper {
  width: 100%;
  max-width: 320px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.logo-box {
  margin-bottom: 24px;
  display: flex;
  justify-content: center;
}

.lion-logo {
  width: 110px;
  height: auto;
}

.login-form {
  width: 100%;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #333333;
  margin-bottom: 6px;
}

.form-group input {
  width: 100%;
  height: 42px;
  padding: 0 14px;
  border-radius: 8px;
  border: none;
  background-color: #0d1b7a;
  color: #ffffff;
  font-size: 14px;
  box-sizing: border-box;
  outline: none;
}

.input-password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-password-wrapper input {
  padding-right: 40px;
}

.btn-toggle-eye {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-toggle-eye:hover {
  color: #ffffff;
}

.btn-login {
  width: 100%;
  height: 42px;
  border-radius: 8px;
  border: none;
  background-color: #0d1b7a;
  color: #ffffff;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  margin-top: 8px;
  transition: background-color 0.2s ease;
}

.btn-login:hover {
  background-color: #1428a3;
}
</style>