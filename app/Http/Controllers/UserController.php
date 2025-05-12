<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getCity()
    {
        // Ambil data dari tabel qu_setoran_rekap dengan pengelompokan berdasarkan kota, lat, dan long
        $cities = DB::table('qu_setoran_rekap')
            ->selectRaw('
                kota,
                lat,
                `long`,
                SUM(COALESCE(t1, 0) + COALESCE(t2, 0) + COALESCE(t3, 0) + COALESCE(t4, 0) +
                    COALESCE(t5, 0) + COALESCE(t6, 0) + COALESCE(t7, 0) + COALESCE(t8, 0) +
                    COALESCE(t9, 0) + COALESCE(t10, 0) + COALESCE(t11, 0) + COALESCE(t12, 0) +
                    COALESCE(t13, 0) + COALESCE(t14, 0) + COALESCE(t15, 0) + COALESCE(t16, 0) +
                    COALESCE(t17, 0) + COALESCE(t18, 0) + COALESCE(t19, 0) + COALESCE(t20, 0) +
                    COALESCE(t21, 0) + COALESCE(t22, 0) + COALESCE(t23, 0) + COALESCE(t24, 0) +
                    COALESCE(t25, 0) + COALESCE(t26, 0) + COALESCE(t27, 0) + COALESCE(t28, 0) +
                    COALESCE(t29, 0) + COALESCE(t30, 0) + COALESCE(t31, 0)) as total_setoran
            ')
            ->whereNotNull('lat')
            ->whereNotNull('long')
            ->groupBy('kota', 'lat', 'long') // Kelompokkan berdasarkan kota, lat, dan long
            ->get();

        // Format data untuk respons
        $formattedResponse = $cities->map(function ($city) {
            return [
                'kota' => htmlspecialchars($city->kota, ENT_QUOTES, 'UTF-8'), // Sanitasi untuk karakter khusus
                'lat' => floatval($city->lat), // Pastikan float
                'long' => floatval($city->long), // Pastikan float
                'total_setoran' => intval($city->total_setoran) // Pastikan integer
            ];
        });
        // $cities = DB::connection('mysql2')->table('qu_setoran_rekap')
        //     ->selectRaw('
        //         kota,
        //         lat,
        //         `long`,
        //         SUM(COALESCE(t1, 0) + COALESCE(t2, 0) + COALESCE(t3, 0) + COALESCE(t4, 0) +
        //             COALESCE(t5, 0) + COALESCE(t6, 0) + COALESCE(t7, 0) + COALESCE(t8, 0) +
        //             COALESCE(t9, 0) + COALESCE(t10, 0) + COALESCE(t11, 0) + COALESCE(t12, 0) +
        //             COALESCE(t13, 0) + COALESCE(t14, 0) + COALESCE(t15, 0) + COALESCE(t16, 0) +
        //             COALESCE(t17, 0) + COALESCE(t18, 0) + COALESCE(t19, 0) + COALESCE(t20, 0) +
        //             COALESCE(t21, 0) + COALESCE(t22, 0) + COALESCE(t23, 0) + COALESCE(t24, 0) +
        //             COALESCE(t25, 0) + COALESCE(t26, 0) + COALESCE(t27, 0) + COALESCE(t28, 0) +
        //             COALESCE(t29, 0) + COALESCE(t30, 0) + COALESCE(t31, 0)) as total_setoran
        //     ')
        //     ->whereNotNull('lat')
        //     ->whereNotNull('long')
        //     ->groupBy('kota', 'lat', 'long') // Kelompokkan berdasarkan kota, lat, dan long
        //     ->get();

        // // Format data untuk respons
        // $formattedResponse = $cities->map(function ($city) {
        //     return [
        //         'kota' => htmlspecialchars($city->kota, ENT_QUOTES, 'UTF-8'), // Sanitasi untuk karakter khusus
        //         'lat' => floatval($city->lat), // Pastikan float
        //         'long' => floatval($city->long), // Pastikan float
        //         'total_setoran' => intval($city->total_setoran) // Pastikan integer
        //     ];
        // });

        // Kembalikan respons JSON
        return response()->json($formattedResponse);
    }
}
