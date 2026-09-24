<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\GoogleSheetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route API Dinamis Google Sheets CSV (Bisa membaca: all-karyawan, sulawesi, kalimantan, mutasi, asset-car, dll.)
Route::get('/sheet/{tabKey}', [GoogleSheetController::class, 'getSheetData']);

// Route API Login Karyawan
Route::post('/login', function (Request $request) {
    $request->validate([
        'nik'      => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::where('nik', $request->nik)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'NIK atau Password salah!'
        ], 401);
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Login berhasil',
        'user'    => [
            'name' => $user->name,
            'nik'  => $user->nik,
        ]
    ]);
});