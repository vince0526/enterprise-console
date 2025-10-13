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
    public function industries(Request $request): \Illuminate\Http\JsonResponse
    {
        $qb = Industry::query();
        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $qb->where('industry_name', 'like', "%$q%");
        }
        [$limit, $offset] = $this->limitOffset($request);
        $items = $qb->orderBy('industry_name')->skip($offset)->take($limit)->get();
        $resp = response()->json($items);

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function subindustries(Request $request): \Illuminate\Http\JsonResponse
    {
        $qb = Subindustry::with('industry');
        if ($request->filled('industry_id')) {
            $qb->where('industry_id', (int) $request->integer('industry_id'));
        }
        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $qb->where('subindustry_name', 'like', "%$q%");
        }
        [$limit, $offset] = $this->limitOffset($request);
        $items = $qb->orderBy('subindustry_name')->skip($offset)->take($limit)->get();
        $resp = response()->json($items);

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function stages(Request $request): \Illuminate\Http\JsonResponse
    {
        $qb = ValueChainStage::query();
        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $qb->where('stage_name', 'like', "%$q%");
        }
        [$limit, $offset] = $this->limitOffset($request);
        $items = $qb->orderBy('stage_name')->skip($offset)->take($limit)->get();
        $resp = response()->json($items);

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function publicGoods(Request $request): \Illuminate\Http\JsonResponse
    {
        $qb = PublicGood::query();
        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $qb->where('name', 'like', "%$q%");
        }
        [$limit, $offset] = $this->limitOffset($request);
        $items = $qb->orderBy('name')->skip($offset)->take($limit)->get();
        $resp = response()->json($items);

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function programs(Request $request): \Illuminate\Http\JsonResponse
    {
        $qb = Program::query();
        if ($request->filled('pg_id')) {
            $qb->where('pg_id', (int) $request->integer('pg_id'));
        }
        if ($request->filled('lead_org_id')) {
            $qb->where('lead_org_id', (int) $request->integer('lead_org_id'));
        }
        if ($request->filled('status')) {
            $qb->where('status', (string) $request->get('status'));
        }
        [$limit, $offset] = $this->limitOffset($request, 100, 500);
        $items = $qb->orderBy('id')->skip($offset)->take($limit)->get();
        $resp = response()->json($items);

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function govOrgs(Request $request): \Illuminate\Http\JsonResponse
    {
        $qb = GovOrg::query();
        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $qb->where('name', 'like', "%$q%");
        }
        if ($request->filled('org_type')) {
            $qb->where('org_type', (string) $request->get('org_type'));
        }
        if ($request->filled('jurisdiction')) {
            $qb->where('jurisdiction', (string) $request->get('jurisdiction'));
        }
        if ($request->filled('is_soe')) {
            $qb->where('is_soe', (bool) $request->boolean('is_soe'));
        }
        if ($request->filled('parent_org_id')) {
            $qb->where('parent_org_id', (int) $request->integer('parent_org_id'));
        }
        [$limit, $offset] = $this->limitOffset($request);
        $items = $qb->orderBy('name')->skip($offset)->take($limit)->get();
        $resp = response()->json($items);

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function csoSuperCategories(Request $request): \Illuminate\Http\JsonResponse
    {
        $qb = CsoSuperCategory::query();
        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $qb->where('name', 'like', "%$q%");
        }
        [$limit, $offset] = $this->limitOffset($request);
        $items = $qb->orderBy('name')->skip($offset)->take($limit)->get();
        $resp = response()->json($items);

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    public function csoTypes(Request $request): \Illuminate\Http\JsonResponse
    {
        $qb = CsoType::query();
        if ($request->filled('cso_super_category_id')) {
            $qb->where('cso_super_category_id', (int) $request->integer('cso_super_category_id'));
        }
        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $qb->where('name', 'like', "%$q%");
        }
        [$limit, $offset] = $this->limitOffset($request);
        $items = $qb->orderBy('name')->skip($offset)->take($limit)->get();
        $resp = response()->json($items);

        return $resp->header('Cache-Control', 'public, max-age=60');
    }

    /** @return array{0:int,1:int} */
    private function limitOffset(Request $request, int $default = 100, int $cap = 500): array
    {
        $limit = (int) $request->integer('limit', $default);
        $limit = max(1, min($cap, $limit));
        $offset = (int) max(0, (int) $request->integer('offset', 0));

        return [$limit, $offset];
    }
}
