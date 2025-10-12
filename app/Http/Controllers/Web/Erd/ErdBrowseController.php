<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Erd;

use App\Http\Controllers\Controller;
use App\Models\GovOrg;
use App\Models\Industry;
use App\Models\Program;
use App\Models\PublicGood;
use App\Models\Subindustry;
use App\Models\ValueChainStage;
use Illuminate\Http\Request;

class ErdBrowseController extends Controller
{
    public function industries()
    {
        return response()->json(Industry::orderBy('industry_name')->get());
    }

    public function subindustries(Request $request)
    {
        $qb = Subindustry::with('industry')->orderBy('subindustry_name');
        if ($request->filled('industry_id')) {
            $qb->where('industry_id', (int) $request->integer('industry_id'));
        }

        return response()->json($qb->get());
    }

    public function stages()
    {
        return response()->json(ValueChainStage::orderBy('stage_name')->get());
    }

    public function publicGoods()
    {
        return response()->json(PublicGood::orderBy('name')->get());
    }

    public function programs(Request $request)
    {
        $qb = Program::query()->orderBy('id');
        if ($request->filled('pg_id')) {
            $qb->where('pg_id', (int) $request->integer('pg_id'));
        }

        return response()->json($qb->limit(500)->get());
    }

    public function govOrgs()
    {
        return response()->json(GovOrg::orderBy('name')->get());
    }
}
