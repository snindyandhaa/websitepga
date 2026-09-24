export default defineEventHandler(async (event) => {
  const targetUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSX9T0vaAOvFxAKJW5kxRuYzeTkcHO1bo7AhWN4sx4G0Ysp6NktjoCvUa_tHPo1ZtY2R3vf9qX8trcg/pub?gid=117909656&single=true&output=csv'

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
      statusMessage: `Gagal mengambil data: ${error.message}`
    })
  }
})