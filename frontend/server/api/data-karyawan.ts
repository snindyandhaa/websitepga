import { defineEventHandler, getQuery, createError } from 'h3'

export default defineEventHandler(async (event) => {
  const query = getQuery(event)
  const sheet = (query.sheet as string) || 'all'

  // ID Spreadsheet Google (Sudah diperbaiki huruf J yang hilang)
  const sheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ'

  // Menentukan gid sheet berdasarkan query
  let gid = '0' 
  if (sheet === 'sulawesi') {
    gid = '1627431459'
  } else if (sheet === 'kalimantan') {
    gid = '936973334'
  }

  // URL Export CSV resmi dari Google Sheets
  const targetUrl = `https://docs.google.com/spreadsheets/d/${sheetId}/export?format=csv&gid=${gid}`

  try {
    const response = await fetch(targetUrl, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
      }
    })

    if (!response.ok) {
      throw new Error(`Google Sheets error status: ${response.status} ${response.statusText}`)
    }

    const csvText = await response.text()

    // Fungsi Parser CSV sederhana
    const parseCSVLine = (line: string): string[] => {
      const result: string[] = []
      let current = ''
      let inQuotes = false

      for (let i = 0; i < line.length; i++) {
        const char = line[i]
        if (char === '"') {
          inQuotes = !inQuotes
        } else if (char === ',' && !inQuotes) {
          result.push(current.trim().replace(/^"|"$/g, ''))
          current = ''
        } else {
          current += char
        }
      }
      result.push(current.trim().replace(/^"|"$/g, ''))
      return result
    }

    const lines: string[] = csvText.split(/\r?\n/).filter((line: string) => line.trim() !== '')
    if (lines.length < 2) return []

    const headers: string[] = parseCSVLine(lines[0] ?? '')
    const dataList: Record<string, string>[] = []

    for (let i = 1; i < lines.length; i++) {
      const values: string[] = parseCSVLine(lines[i] ?? '')
      const rowObj: Record<string, string> = {}
      let hasData = false

      headers.forEach((header: string, index: number) => {
        if (header) {
          const val = values[index] ?? ''
          rowObj[header.trim()] = val.trim()
          if (val.trim() !== '') hasData = true
        }
      })

      if (hasData) {
        dataList.push(rowObj)
      }
    }

    return dataList
  } catch (error: any) {
    throw createError({
      statusCode: 500,
      statusMessage: error.message
    })
  }
})