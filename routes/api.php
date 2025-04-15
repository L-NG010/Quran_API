<?php

use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChapterInfoController;
use App\Http\Controllers\JuzController;
use App\Http\Controllers\SearchingController;
use App\Http\Controllers\VersesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//chapters
Route::get('v1/quran/chapters',[ChapterController::class,'getAllChapters']);
Route::get('v1/quran/by_chapter/{noChapter}',[ChapterController::class,'getByChapter']);

//chapter info
Route::get('v1/quran/chapter/{noChapter}/info',[ChapterInfoController::class,'getChapterInfo']);

//verses
Route::get('v1/quran/verses',[VersesController::class,'getAllVerses']);
Route::get('v1/quran/by_page/{noPage}',[VersesController::class,'by_page']);
Route::get('v1/quran/by_verse/{verses}',[VersesController::class,'by_verses']);
Route::get('v1/quran/verses/by_chapter/{noChapter}',[VersesController::class,'byChapter']);

//juz
Route::get('v1/quran/juzs',[JuzController::class,'getAllJuz']);
Route::get('v1/quran/by_juz/{juz}',[JuzController::class,'by_juz']);

//searching
Route::get('v1/quran/search',[SearchingController::class,'searchChapters']);
