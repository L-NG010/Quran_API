<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GroupQuraniSetting;
use App\Models\GlobalQuraniSetting;
use Illuminate\Support\Facades\Log;
class GroupQuraniSettingController extends Controller
{
    /**
     * Mengambil pengaturan grup Qurani, menggabungkan dengan global jika tidak ada custom.
     */
    public function getGroupSettings($groupId)
    {
        Log::info("Fetching group settings for group_id: {$groupId}");

        // Ambil semua pengaturan kustom grup
        $groupSettings = GroupQuraniSetting::where('group', $groupId)
            ->get()
            ->keyBy('setting');

        // Ambil semua pengaturan global
        $globalSettings = GlobalQuraniSetting::all()->keyBy('id');

        $settings = [];
        foreach ($globalSettings as $settingId => $global) {
            $groupSetting = $groupSettings[$settingId] ?? null;
            $settings[] = [
                'id' => $settingId,
                'key' => $global->key,
                'value' => $groupSetting ? $groupSetting->value : $global->value,
                'status' => $groupSetting ? $groupSetting->status : $global->status,
                'color' => $global->color,
            ];
        }

        return response()->json($settings);
    }

    /**
     * Memperbarui pengaturan grup Qurani.
     */
    public function updateGroupSetting(Request $request, $groupId, $settingId)
    {
        $globalSetting = GlobalQuraniSetting::find($settingId);
        if (!$globalSetting) {
            Log::error("Invalid setting_id: {$settingId} for group_id: {$groupId}");
            return response()->json(['message' => 'Setting ID tidak valid'], 404);
        }

        $request->validate([
            'value' => 'nullable|string|max:50',
            'status' => 'sometimes|integer|in:0,1',
        ]);

        $groupSetting = GroupQuraniSetting::where('group', $groupId)
            ->where('setting', $settingId)
            ->first();

        if (!$groupSetting) {
            $groupSetting = new GroupQuraniSetting();
            $groupSetting->group = $groupId;
            $groupSetting->setting = $settingId;
        }

        $groupSetting->value = $request->input('value', $globalSetting->value);
        $groupSetting->status = $request->input('status', $globalSetting->status);
        $groupSetting->save();

        Log::info("Updated group setting: group_id={$groupId}, setting_id={$settingId}, data=" . json_encode($request->all()));

        return response()->json(['message' => 'Pengaturan diperbarui', 'data' => $groupSetting]);
    }

    /**
     * Mereset semua pengaturan grup ke global.
     */
    public function resetGroupSetting($groupId)
    {
        Log::info("Resetting all group settings for group_id: {$groupId}");
        GroupQuraniSetting::where('group', $groupId)->delete();

        return response()->json(['message' => 'Semua pengaturan grup direset ke global']);
    }
}
