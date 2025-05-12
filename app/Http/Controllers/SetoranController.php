<?php

namespace App\Http\Controllers;

use App\Models\juz;
use App\Models\Setoran;
use App\Models\User;
use App\Models\Chapter;
use App\Models\kota; // Tambahkan model untuk tabel kota
use App\Models\setoranRekap; // Model untuk qu_setoran_rekap
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class SetoranController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Peserta' => 'required|string|max:255',
            'Penyimak' => 'required|string|max:255',
            'Kesimpulan' => 'required|in:Lancar,Tidak Lancar,Lulus,Tidak Lulus,Mumtaz,Dhoif',
            'Catatan' => 'nullable|string|max:100',
            'AwalSurat' => 'required|string|max:20',
            'AwalAyat' => 'required|integer|min:1',
            'AkhirSurat' => 'required|string|max:20',
            'AkhirAyat' => 'required|integer|min:1',
            'AwalHalaman' => 'nullable|string|max:10',
            'AkhirHalaman' => 'nullable|string|max:10',
            'Timestamp' => 'required|date',
            'SetoranType' => 'required|in:tahsin,tahfidz',
            'TampilkanType' => 'required|in:surat,juz,halaman',
            'Kesalahan' => 'nullable|array',
            'Perhalaman' => 'required|array',
            'nomor' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Ambil data penyetor dan penerima
            $penyetor = User::where('user_name', $request->Peserta)->firstOrFail();
            $penerima = User::where('user_name', $request->Penyimak)->firstOrFail();

            // Ambil data chapter, juz, dan halaman
            $chapters = Chapter::all()->pluck('id', 'name_simple')->toArray();
            $juz = juz::all()->pluck('id', 'juz_number')->toArray();

            $nomor = 0;
            $info = null;
            if ($request->TampilkanType === 'surat') {
                $nomor = $chapters[$request->AwalSurat] ?? 0;
                $info = $request->AwalSurat;
            } elseif ($request->TampilkanType === 'juz') {
                $nomor = (int) str_replace('Juz ', '', $request->input('juz', '')); // Sesuaikan jika perlu
            } elseif ($request->TampilkanType === 'halaman') {
                $nomor = (int) $request->AwalHalaman;
            }

            $parafoleh = null;
            $paraftgl = $parafoleh ? Carbon::now()->setTimezone('Asia/Jakarta') : null;

            // Data untuk tabel qu_setoran
            $setoranData = [
                'tgl' => Carbon::parse($request->Timestamp)->setTimezone('Asia/Jakarta'),
                'penyetor' => $penyetor->user_id,
                'penerima' => $penerima->user_id,
                'setoran' => $request->SetoranType,
                'tampilan' => $request->TampilkanType,
                'nomor' => $request->nomor,
                'info' => $info,
                'hasil' => $request->Kesimpulan,
                'ket' => $request->Catatan,
                'kesalahan' => $request->Kesalahan ? $request->Kesalahan : null,
                'perhalaman' => [
                    'awal_halaman' => $request->AwalHalaman,
                    'akhir_halaman' => $request->AkhirHalaman,
                    'ayat' => [
                        'awal' => $request->AwalAyat,
                        'akhir' => $request->AkhirAyat,
                    ],
                    'conclusions' => $request->Perhalaman,
                ],
                'paraf' => 0,
                'paraftgl' => $paraftgl,
                'parafoleh' => $parafoleh,
            ];

            // Simpan ke tabel qu_setoran
            $setoran = Setoran::create($setoranData);

            // Proses untuk tabel qu_setoran_rekap
            // 1. Ambil user_current_city dari tabel users
            $userCityName = $penyetor->user_current_city;

            // 2. Ambil data kota dari tabel kota
            $kota = kota::where('nama', $userCityName)->first();
            if (!$kota) {
                throw new \Exception('Kota tidak ditemukan untuk user_current_city: ' . $userCityName);
            }

            // 3. Tentukan periode (YYYY-MM) dan tanggal saat ini
            $periode = Carbon::parse($request->Timestamp)->format('Y-m');
            $currentDay = Carbon::now()->setTimezone('Asia/Jakarta')->day;
            $columnDay = 't' . $currentDay;

            // 4. Cek apakah data dengan periode dan kota sudah ada
            $rekap = setoranRekap::where('periode', $periode)
                ->where('kota', $kota->nama)
                ->first();

            if ($rekap) {
                // Update jumlah di kolom tX
                $rekap->$columnDay = ($rekap->$columnDay ?? 0) + 1;
                $rekap->save();
            } else {
                // Insert data baru
                $rekapData = [
                    'periode' => $periode,
                    'negara' => $kota->negara ?? 'Indonesia', // Sesuaikan jika ada kolom negara
                    'provinsi' => $kota->provinsi ?? null,
                    'kota' => $kota->nama,
                    'lat' => $kota->lat ?? null,
                    'long' => $kota->long ?? null,
                    $columnDay => 1, // Set jumlah 1 untuk hari ini
                ];
                // Inisialisasi t1 sampai t31 dengan 0 jika tidak ada nilai
                for ($i = 1; $i <= 31; $i++) {
                    $rekapData['t' . $i] = $rekapData['t' . $i] ?? 0;
                }
                setoranRekap::create($rekapData);
            }

            // Response sukses
            return response()->json([
                'message' => 'Rekapan setoran berhasil disimpan',
                'data' => [
                    'id' => $setoran->id,
                    'tgl' => $setoran->tgl->toIso8601String(),
                    'penyetor' => $request->Peserta,
                    'penerima' => $request->Penyimak,
                    'hasil' => $setoran->hasil,
                    'kota' => $kota->nama,
                ],
            ], 201);

        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Gagal menyimpan rekapan setoran: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menyimpan rekapan setoran',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
