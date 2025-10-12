<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Erd;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Program;
use App\Models\PublicGood;
use App\Models\Subindustry;
use App\Models\ValueChainStage;

class ErdBrowseController extends Controller
{
    public function industries()
    {
        return response()->json(Industry::orderBy('industry_name')->get());
    }

    public function subindustries()
    {
        return response()->json(Subindustry::with('industry')->orderBy('subindustry_name')->get());
    }

    public function stages()
    {
        return response()->json(ValueChainStage::orderBy('stage_name')->get());
    }

    public function publicGoods()
    {
        return response()->json(PublicGood::orderBy('name')->get());
    }

    public function programs()
    {
        return response()->json(Program::orderBy('id')->limit(200)->get());
    }
}
