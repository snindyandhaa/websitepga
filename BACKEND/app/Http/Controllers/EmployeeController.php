<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller
{
    private $spreadsheetId = '1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ';

    public function index(Request $request)
    {
        $sheet = $request->query('sheet', 'all');

        $gid = '0';
        if ($sheet === 'sulawesi') {
            $gid = '1627431459';
        } else if ($sheet === 'kalimantan') {
            $gid = '936973334';
        }

        $targetUrl = "https://docs.google.com/spreadsheets/d/{$this->spreadsheetId}/export?format=csv&gid={$gid}";

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
            ])->get($targetUrl);

            if (!$response->successful()) {
                return response()->json(['message' => 'Gagal mengambil data dari Google Sheets'], 500);
            }

            $csvText = $response->body();
            $csvText = preg_replace('/^\xEF\xBB\xBF/', '', $csvText); // Hapus BOM

            $lines = array_filter(explode("\n", str_replace("\r", "", $csvText)));
            if (count($lines) < 2) return response()->json([]);

            $rawHeaders = array_shift($lines);
            $headers = array_map('trim', str_getcsv($rawHeaders));

            $dataList = [];
            foreach ($lines as $line) {
                if (trim($line) === '') continue;
                $rowValues = str_getcsv($line);
                $rowObj = [];
                $hasData = false;

                foreach ($headers as $idx => $header) {
                    if ($header) {
                        $val = isset($rowValues[$idx]) ? trim($rowValues[$idx]) : '';
                        $rowObj[$header] = $val;
                        if ($val !== '') $hasData = true;
                    }
                }

                if ($hasData) {
                    $dataList[] = $rowObj;
                }
            }

            return response()->json($dataList);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}