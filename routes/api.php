<?php

use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChapterInfoController;
use App\Http\Controllers\GlobalQuraniSettingController;
use App\Http\Controllers\GroupQuraniSettingController;
use App\Http\Controllers\JuzController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\SearchingController;
use App\Http\Controllers\SearchingRevisiController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserQuraniSettingController;
use App\Http\Controllers\VersesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//chapters
Route::get('v1/chapters',[ChapterController::class,'getAllChapters']);
Route::get('v1/by_chapter/{noChapter}',[ChapterController::class,'getByChapter']);

//chapter info
Route::get('v1/chapters/{noChapter}/info',[ChapterInfoController::class,'getChapterInfo']);

//verses
Route::get('v1/verses',[VersesController::class,'getAllVerses']);
Route::get('v1/verses/by_page/{noPage}',[VersesController::class,'by_page']);
Route::get('v1/verses/by_key/{verse_key}',[VersesController::class,'by_verse']);
Route::get('v1/verses/by_chapter/{noChapter}',[VersesController::class,'byChapter']);
Route::get('v1/verses/by_juz/{noJuz}',[VersesController::class,'byJuz']);

//juz
Route::get('v1/juzs',[JuzController::class,'getAllJuz']);
Route::get('v1/by_juz/{juz}',[JuzController::class,'by_juz']);

//searching
Route::get('v1/search',[SearchingController::class,'search']);
Route::get('v1/v2/search',[SearchingRevisiController::class,'search']);

//API LINK ID QURANNI

//user
Route::get('v1/users/city',[UserController::class,'getCity']);

//Setoran
Route::post('v1/recap', [SetoranController::class, 'store']);
Route::get('v1/recap/{username}', [SetoranController::class, 'get']);

//recap
// Route::post('v1/recap',[RecapController::class,'store']);

//setting
Route::prefix('global-qurani-settings')->group(function () {
    Route::get('/', [GlobalQuraniSettingController::class, 'index'])->name('global-qurani-settings.index');

    Route::put('{id}', [GlobalQuraniSettingController::class, 'update'])->name('global-qurani-settings.update');

    Route::post('reset', [GlobalQuraniSettingController::class, 'reset'])->name('global-qurani-settings.reset');
});

Route::prefix('group-qurani-settings/{group_id}')->group(function () {
    Route::get('/', [GroupQuraniSettingController::class, 'getGroupSettings'])->name('group-qurani-settings.getGroupSettings');
    Route::put('{setting_id}', [GroupQuraniSettingController::class, 'updateGroupSetting'])->name('group-qurani-settings.updateGroupSetting');
    Route::post('reset', [GroupQuraniSettingController::class, 'resetGroupSetting'])->name('group-qurani-settings.resetGroupSetting');
});

Route::prefix('user-qurani-settings/{user_id}')->group(function (){
    Route::get('', [UserQuraniSettingController::class, 'getUserSettings']);
    Route::put('/{settingId}', [UserQuraniSettingController::class, 'updateUserSetting']);
    Route::delete('/reset', [UserQuraniSettingController::class, 'resetUserSetting']);
});
