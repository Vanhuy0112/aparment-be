<?php

namespace App\Http\Controllers;

use App\Http\Resources\VehicleTypeResource;
use App\Models\VehicleType;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VehicleTypeController extends Controller
{
    public function getVehicleType():JsonResponse
    {
        $vehicle_types = VehicleType::all();
        $result = VehicleTypeResource::collection($vehicle_types);
        return $this->success($result);
    }

    public function addForm(){
        return view('vehicle-type.add');
    }

    public function saveAdd(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),
        ['name' => 'required|string|unique:vehicle_types|regex:/A-Za-z/',
        'price' => 'required|integer|min:1',],
        [
            'name.required'=> 'Tên Không được trống',
            'name.string'=> 'Tên phải là chuỗi',
            'name.unique'=>'Tên đã tồn tại',
            'name.regex'=>'Tên không được chứa kí tự đặc biệt, số và phải là chữ',
            'price.required'=>'Phí không được trống',
            'price.integer'=>'Phí phải là số',
            'price.min'=>'Phí nhỏ nhất là 1'

        ]
    );
    if ($validator->fails()) {
        return $this->failed($validator->messages());
    }
        
        $vehicle_type = new VehicleType();
        $vehicle_type->fill($request->all());
        $vehicle_type->save();
        return $this->success($vehicle_type);
    }
    public function saveEdit(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(),
        ['name' => [
            'required', 'string','regex:[a-zA-Z]',
            Rule::unique('vehicle_types')->ignore($id)
        ],
        
        'price' => 'required|integer|min:1',],
        [
            'name.required'=> 'Tên Không được trống',
            'name.string'=> 'Tên phải là chuỗi',
            'name.unique'=>'Tên đã tồn tại',
            'name.regex'=>'Tên không được chứa kí tự đặc biệt, số và phải là chữ',
            'price.required'=>'Phí không được trống',
            'price.integer'=>'Phí phải là số',
            'price.min'=>'Phí nhỏ nhất là 1'

        ]
    );
    if ($validator->fails()) {
        return $this->failed($validator->messages());
    }
        
        $vehicle_type = VehicleType::find($id);
        $vehicle_type->fill($request->all());
        $vehicle_type->save();
        return $this->success($vehicle_type);
    }
}
