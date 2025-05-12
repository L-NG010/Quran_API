<?php

namespace App\Http\Controllers;

use App\Models\GlobalQuraniSetting;
use Illuminate\Http\Request;

class GlobalQuraniSettingController extends Controller
{
    /**
     * Mendapatkan semua pengaturan
     */
    public function index()
    {
        $settings = GlobalQuraniSetting::all();
        return response()->json($settings);
    }

    /**
     * Memperbarui pengaturan berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'value' => 'sometimes|string|max:255',
            'status' => 'sometimes|boolean',
        ]);

        $setting = GlobalQuraniSetting::findOrFail($id);

        if ($request->has('value')) {
            $setting->value = $request->value;
        }

        if ($request->has('status')) {
            $setting->status = $request->status;
        }

        $setting->save();

        return response()->json(['message' => 'Setting updated successfully', 'setting' => $setting]);
    }

    /**
     * Reset semua pengaturan ke default
     */
    public function reset(Request $request)
    {
        $defaultSettings = [
            'sa-1' => ['value' => 'Ayat Lupa', 'status' => 1],
            'sa-2' => ['value' => 'Ayat Waqaf atau Washal', 'status' => 1],
            'sa-3' => ['value' => 'Ayat Waqaf dan Ibtida', 'status' => 1],
            'sa-4' => ['value' => 'Lainnya', 'status' => 1],
            'sa-5' => ['value' => 'Gharib', 'status' => 1],
            'sk-1' => ['value' => 'Ghunnah', 'status' => 1],
            'sk-2' => ['value' => 'Harakat Tertukar', 'status' => 1],
            'sk-3' => ['value' => 'Huruf Tambah Kurang', 'status' => 1],
            'sk-4' => ['value' => 'Lupa', 'status' => 1],
            'sk-5' => ['value' => 'Mad', 'status' => 1],
            'sk-6' => ['value' => 'Makhroj', 'status' => 1],
            'sk-7' => ['value' => 'Nun Mati Tanwin', 'status' => 1],
            'sk-8' => ['value' => 'Qalqalah', 'status' => 1],
            'sk-9' => ['value' => 'Tasydid', 'status' => 1],
            'sk-10' => ['value' => 'Urutan Huruf Kata', 'status' => 1],
            'sk-11' => ['value' => 'Waqof Washol', 'status' => 1],
            'sk-12' => ['value' => 'Waqof Ibtida', 'status' => 1],
            'sk-13' => ['value' => 'Kata Lainnya', 'status' => 1],
            'sk-14' => ['value' => 'a', 'status' => 1],
            'tata-letak' => ['value' => '1', 'status' => 1],
            'font' => ['value' => '1', 'status' => 1],
            'font-size' => ['value' => '5', 'status' => 1],
            'kesimpulan' => ['value' => '1', 'status' => 1]
        ];

        foreach ($defaultSettings as $key => $values) {
            GlobalQuraniSetting::where('key', $key)->update([
                'value' => $values['value'],
                'status' => $values['status']
            ]);
        }

        return response()->json(['message' => 'Settings reset to default']);
    }
}
