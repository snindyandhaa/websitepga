<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoogleSheetController extends Controller
{
    public function getSheetData($tabKey)
    {
        $spreadsheetId = "1YQSq9tFrGZmm7Z1x_8zn5yY7qeM2EmHNxSjJT4vj5UQ";

        $gids = [
            'all-karyawan'   => '0',
            'sulawesi'       => '1627431459',
            'kalimantan'     => '936973334',
            'mutasi'         => '484819291',
            'asset-car'      => '117909656',
            'asset-pga'      => '117909656',
        ];

        if (!array_key_exists($tabKey, $gids)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tab key tidak ditemukan.'
            ], 404);
        }

        $gid = $gids[$tabKey];
        $csvUrl = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$gid}";

        try {
            $options = [
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
                    "follow_location" => true
                ],
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false
                ]
            ];

            $context = stream_context_create($options);
            $csvData = @file_get_contents($csvUrl, false, $context);

            if ($csvData === false) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal mengunduh CSV dari Google Sheets.'
                ], 500);
            }

            // Gunakan php://temp & fgetcsv agar parsing koma & kutip 100% presisi
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $csvData);
            rewind($stream);

            $headers = [];
            $result = [];
            $isHeader = true;

            while (($row = fgetcsv($stream, 0, ",")) !== false) {
                if ($isHeader) {
                    $headers = array_map(function($h) {
                        return trim(preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', $h));
                    }, $row);
                    $isHeader = false;
                    continue;
                }

                if (empty(array_filter($row))) continue;

                $item = [];
                foreach ($headers as $index => $headerName) {
                    if (empty($headerName)) continue;
                    $item[$headerName] = isset($row[$index]) ? trim($row[$index]) : '';
                }

                if (!empty(array_filter($item))) {
                    $result[] = $item;
                }
            }

            fclose($stream);

            return response()->json([
                'status' => 'success',
                'total'  => count($result),
                'data'   => $result
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}