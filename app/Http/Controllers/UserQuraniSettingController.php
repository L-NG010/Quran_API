<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserQuraniSetting;
use App\Models\GlobalQuraniSetting;
use Illuminate\Support\Facades\Log;

class UserQuraniSettingController extends Controller
{
    /**
     * Mengambil pengaturan user Qurani, menggabungkan dengan global jika tidak ada custom.
     */
    public function getUserSettings($userId)
    {
        Log::info("Fetching user settings for user_id: {$userId}");

        // Ambil semua pengaturan kustom user
        $userSettings = UserQuraniSetting::where('user', $userId)
            ->get()
            ->keyBy('setting');

        // Ambil semua pengaturan global
        $globalSettings = GlobalQuraniSetting::all()->keyBy('id');

        $settings = [];
        foreach ($globalSettings as $settingId => $global) {
            $userSetting = $userSettings[$settingId] ?? null;
            $settings[] = [
                'id' => $settingId,
                'key' => $global->key,
                'value' => $userSetting ? $userSetting->value : $global->value,
                'status' => $userSetting ? $userSetting->status : $global->status,
                'color' => $global->color
            ];
        }

        return response()->json($settings);
    }

    /**
     * Memperbarui pengaturan user Qurani.
     */
    public function updateUserSetting(Request $request, $userId, $settingId)
    {
        $globalSetting = GlobalQuraniSetting::find($settingId);
        if (!$globalSetting) {
            Log::error("Invalid setting_id: {$settingId} for user_id: {$userId}");
            return response()->json(['message' => 'Setting ID tidak valid'], 404);
        }

        $request->validate([
            'value' => 'nullable|string|max:255',
            'status' => 'sometimes|integer|in:0,1'
        ]);

        $userSetting = UserQuraniSetting::where('user', $userId)
            ->where('setting', $settingId)
            ->first();

        if (!$userSetting) {
            $userSetting = new UserQuraniSetting();
            $userSetting->user = $userId;
            $userSetting->setting = $settingId;
        }

        $userSetting->value = $request->input('value', $globalSetting->value);
        $userSetting->status = $request->input('status', $globalSetting->status);
        $userSetting->save();

        Log::info("Updated user setting: user_id={$userId}, setting_id={$settingId}, data=" . json_encode($request->all()));

        return response()->json(['message' => 'Pengaturan diperbarui', 'data' => $userSetting]);
    }

    /**
     * Mereset semua pengaturan user ke global.
     */
    public function resetUserSetting($userId)
    {
        Log::info("Resetting all user settings for user_id: {$userId}");
        UserQuraniSetting::where('user', $userId)->delete();

        return response()->json(['message' => 'Semua pengaturan user direset ke global']);
    }
}
