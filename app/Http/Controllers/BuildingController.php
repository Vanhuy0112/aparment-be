<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Models\Building;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BuildingController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getBuilding(): JsonResponse
    {
        $buildings = Building::all();
        $result = BuildingResource::collection($buildings);
        return $this->success($result);
    }

    /**
     * @return Application|Factory|View
     */
    public function addForm()
    {
        return view('building.add');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAdd(BuildingRequest $request): JsonResponse
    {
        $building = new Building();
        $building->fill($request->all());
        $building->save();
        return $this->success($building);
    }

    public function editForm($id)
    {
        $building = Building::find($id);
        return view('building.edit', compact('building'));
    }

    public function saveEdit($id, BuildingRequest $request): JsonResponse
    {
        $building = Building::find($id);
        $building->fill($request->all());
        $building->save();
        return $this->success($building);
    }

    public function geBuildingById($id): JsonResponse
    {
        $building = Building::find($id);
        if (!$building) {
            return $this->failed();
        }
        return $this->success($building);
    }

    public function getApartmentByBuildingId($id): JsonResponse
    {
        $buildings = Building::join('apartments','buildings.id','apartments.building_id')
                            ->leftJoin('users','apartments.id','users.apartment_id')
                            ->select(
                            'buildings.name as building_name',
                            'apartments.id',
                            'apartments.apartment_id',
                            'apartments.floor',
                            'apartments.status',
                            'apartments.description',
                            'apartments.square_meters',
                            'apartments.type_apartment',
                            'users.name as user_name'
                            )
                            ->where('buildings.id',$id)
                            ->get();
        return $this->success($buildings);
    }
}
