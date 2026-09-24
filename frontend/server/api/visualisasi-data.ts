import { defineEventHandler, getQuery, createError } from 'h3'

export default defineEventHandler(async (event) => {
  const query = getQuery(event)
  const sheet = (query.sheet as string) || 'all'

  // ID Google Sheets Resmi
  const sheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'

  // Mapping GID Google Sheets sesuai tab
  let gid = '0'
  if (sheet === 'sulawesi') {
    gid = '1627431459'
  } else if (sheet === 'kalimantan') {
    gid = '936973334'
  }

  const targetUrl = `https://docs.google.com/spreadsheets/d/${sheetId}/export?format=csv&gid=${gid}`

  try {
    const response = await fetch(targetUrl, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
      },
      redirect: 'follow'
    })

    if (!response.ok) {
      throw new Error(`Google Sheets merespon status: ${response.status}`)
    }

    const csvData = await response.text()
    return csvData
  } catch (error: any) {
    throw createError({
      statusCode: 500,
      statusMessage: `Gagal mengambil data visualisasi: ${error.message}`
    })
  }
})