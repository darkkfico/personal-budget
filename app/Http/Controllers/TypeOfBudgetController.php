<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;


class TypeOfBudgetController extends Controller
{
    public function choose(Request $request)
    {

        $request->validate([
            "type" => ['required', "in:auto,custom"],
        ]);

        session(['type' => $request->type]);

        if ($request->type === "auto") {
            return redirect()->route("auto.form");
        } else {
            return redirect()->route("custom.form");
        }
    }

}
