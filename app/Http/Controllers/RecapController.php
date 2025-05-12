<?php

namespace App\Http\Controllers;

use App\Models\Recap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RecapController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'Peserta' => 'required|string|max:255',
                'Penyimak' => 'required|string|max:255',
                'Kesimpulan' => 'required|string|in:Lancar,Tidak Lancar,Lulus,Tidak Lulus,Mumtaz,Dhoif',
                'Catatan' => 'nullable|string',
                'AwalSurat' => 'required|string|max:255',
                'AwalAyat' => 'required|integer|min:1',
                'AkhirSurat' => 'required|string|max:255',
                'AkhirAyat' => 'required|integer|min:1',
                'AwalHalaman' => 'required|string|max:50',
                'AkhirHalaman' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Simpan data ke tabel recap
            $recap = Recap::create([
                'nama_peserta' => $request->Peserta,
                'nama_penyimak' => $request->Penyimak,
                'kesimpulan_utama' => $request->Kesimpulan,
                'catatan_utama' => $request->Catatan,
                'awal_surat' => $request->AwalSurat,
                'awal_ayat' => $request->AwalAyat,
                'akhir_surat' => $request->AkhirSurat,
                'akhir_ayat' => $request->AkhirAyat,
                'awal_halaman' => $request->AwalHalaman,
                'akhir_halaman' => $request->AkhirHalaman,
            ]);

            return response()->json([
                'message' => 'Data rekap berhasil disimpan',
                'data' => $recap,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error menyimpan rekap: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

