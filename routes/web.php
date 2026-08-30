<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutoBudgetController;
use App\Http\Controllers\AutoBudgetItemController;
use App\Http\Controllers\CustomBudgetController;
use App\Http\Controllers\CustomBudgetItemController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TypeOfBudgetController;
use Illuminate\Support\Facades\Route;


// index
Route::get("/", [HomeController::class, "index"])->name("home.index");

// register
Route::get("/register", [AuthController::class, "registerForm"])->name("auth.register");
Route::post("/register", [AuthController::class, "register"])->name("auth.store");


// login
Route::get("/login", [AuthController::class, "loginForm"])->name("auth.login");
Route::post("/login", [AuthController::class, "login"])->name("login");

// logout
Route::get("/logout", [AuthController::class, "logout"])->name("auth.logout");


Route::middleware('auth')->group(function () {

    // dashboard
    Route::get("/dashboard", [HomeController::class, "dashboard"])->name("start.dashboard");

    // start
    Route::get("/start", [HomeController::class, "start"])->name("start.start");

    // Type of budget
    Route::post("/choose", [TypeOfBudgetController::class, "choose"])->name("start.choose");

    // AUTO
    Route::get("/auto", [AutoBudgetController::class, "form"])->name("auto.form");
    Route::post("/auto/store", [AutoBudgetController::class, "create"])->name("auto.create");
    Route::get("/auto/index", [AutoBudgetController::class, "index"])->name("auto.index");
    Route::get("/auto/{id}/change", [AutoBudgetController::class, "change"])->name("auto.change");
    Route::patch("/auto/{budget}/edit", [AutoBudgetController::class, "edit"])->name("auto.edit");
    Route::patch("/auto/convert", [AutoBudgetController::class, "convert"])->name("auto.convert");
    Route::get("/auto/{budget}/history", [AutoBudgetController::class, "history"])->name("auto.history");


    // CUSTOM 
    Route::get("/custom", [CustomBudgetController::class, "form"])->name("custom.form");
    Route::post("/custom/store", [CustomBudgetController::class, "store"])->name("custom.store");
    Route::get("/custom/nonResetable", [CustomBudgetController::class, "nonResetable"])->name("custom.formNonResetable");
    Route::post("/custom/store/nonResetable", [CustomBudgetController::class, "storeNonResetable"])->name("custom.nonResetable");
    Route::get("/custom/index", [CustomBudgetController::class, "index"])->name("custom.index");
    Route::get("/custom/change", [CustomBudgetController::class, "change"])->name("custom.change");
    Route::patch("/custom/edit", [CustomBudgetController::class, "edit"])->name("custom.edit");
    Route::get("/custom/{budget}/history", [CustomBudgetController::class, "history"])->name("custom.history");


    // AUTO ITEM
    Route::post("auto/item/store", [AutoBudgetItemController::class, "create"])->name("auto.item.create");
    Route::patch("auto/item/{item}/edit", [AutoBudgetItemController::class, "edit"])->name("auto.item.edit");
    Route::delete("auto/item/{item}/delete", [AutoBudgetItemController::class, "delete"])->name("auto.item.delete");

    // CUSTOM ITEM
    Route::post("custom/item/store", [CustomBudgetItemController::class, "create"])->name("custom.item.create");
    Route::patch("custom/item/{item}/edit", [CustomBudgetItemController::class, "edit"])->name("custom.item.edit");
    Route::delete("custom/item/{item}/delete", [CustomBudgetItemController::class, "delete"])->name("custom.item.delete");
});
