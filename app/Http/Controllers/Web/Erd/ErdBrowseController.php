<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Erd;

use App\Http\Controllers\Controller;
use App\Models\CsoSuperCategory;
use App\Models\CsoType;
use App\Models\GovOrg;
use App\Models\Industry;
use App\Models\Program;
use App\Models\PublicGood;
use App\Models\Subindustry;
use App\Models\ValueChainStage;
use Illuminate\Http\Request;

class ErdBrowseController extends Controller
{
    public function industries(Request $request)
    {
        $qb = Industry::query()->orderBy('industry_name');
        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $qb->where('industry_name', 'like', "%$q%");
        }
        $resp = response()->json($qb->get());

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function subindustries(Request $request)
    {
        $qb = Subindustry::with('industry')->orderBy('subindustry_name');
        if ($request->filled('industry_id')) {
            $qb->where('industry_id', (int) $request->integer('industry_id'));
        }

        $resp = response()->json($qb->get());

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function stages()
    {
        $resp = response()->json(ValueChainStage::orderBy('stage_name')->get());

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function publicGoods()
    {
        $resp = response()->json(PublicGood::orderBy('name')->get());

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function programs(Request $request)
    {
        $qb = Program::query()->orderBy('id');
        if ($request->filled('pg_id')) {
            $qb->where('pg_id', (int) $request->integer('pg_id'));
        }
        $resp = response()->json($qb->limit(500)->get());

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function govOrgs()
    {
        $resp = response()->json(GovOrg::orderBy('name')->get());

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function csoSuperCategories()
    {
        $resp = response()->json(CsoSuperCategory::orderBy('name')->get());

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function csoTypes(Request $request)
    {
        $qb = CsoType::query()->orderBy('name');
        if ($request->filled('cso_super_category_id')) {
            $qb->where('cso_super_category_id', (int) $request->integer('cso_super_category_id'));
        }

        $resp = response()->json($qb->get());

        return $resp->header('Cache-Control', 'public, max-age=60');
    }
}
