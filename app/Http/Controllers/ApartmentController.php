<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Imports\BaseImport;
use App\Models\Apartment;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\Building;
use App\Models\Card;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ApartmentController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getApartment(Request $request): JsonResponse
    {
        $apartments = Apartment::all();
        if ($request->filled('building_id') && $request->filled('keyword')) {
            $apartments = Apartment::join('users', 'apartments.id', '=', 'users.apartment_id')
                ->join('buildings', 'apartments.building_id', '=', 'buildings.id')
                ->select(
                    'apartments.id',
                    'apartments.apartment_id',
                    'apartments.floor',
                    'apartments.status',
                    'apartments.description',
                    'apartments.square_meters',
                    'apartments.type_apartment',
                    'apartments.building_id',
                    'apartments.user_id',
                    'buildings.name as building_name',
                    'users.email',
                    'users.phone_number',
                    'users.name'
                )
                ->where([
                    ['apartments.building_id', $request->building_id],
                    ['users.phone_number', $request->keyword],
                ])
                ->orWhere([
                    ['apartments.building_id', $request->building_id],
                    ['users.email', 'like', '%' . $request->keyword . '%'],
                ])
                ->orWhere([
                    ['apartments.building_id', $request->building_id],
                    ['users.name', 'like', '%' . $request->keyword . '%'],
                ])
                ->orWhere([
                    ['apartments.building_id', $request->building_id],
                    ['apartments.apartment_id', 'like', '%' . $request->keyword . '%'],
                ])
                ->get();
        } else if (!$request->filled('building_id') && $request->filled('keyword')) {
            $apartments = Apartment::join('users', 'apartments.id', '=', 'users.apartment_id')
                ->join('buildings', 'apartments.building_id', '=', 'buildings.id')
                ->select(
                    'apartments.id',
                    'apartments.apartment_id',
                    'apartments.floor',
                    'apartments.status',
                    'apartments.description',
                    'apartments.square_meters',
                    'apartments.type_apartment',
                    'apartments.building_id',
                    'apartments.user_id',
                    'buildings.name as building_name',
                    'users.email',
                    'users.phone_number',
                    'users.name'
                )
                ->where('users.phone_number', $request->keyword)
                ->orWhere('users.email', 'like', '%' . $request->keyword . '%')
                ->orWhere('users.name', 'like', '%' . $request->keyword . '%')
                ->orWhere('apartments.apartment_id', 'like', '%' . $request->keyword . '%')
                ->get();
        } else if ($request->filled('building_id') && !$request->filled('keyword')) {
            $apartments = Apartment::where('building_id', $request->building_id)->get();
        }

        if ($request->filled('page') && $request->filled('page_size')) {
            $apartments = $apartments->skip(($request->page - 1) * $request->page_size)->take($request->page_size);
        }
        $result = ApartmentResource::collection($apartments);
        return $this->success($result);
    }

    public function getApartmentNotOwned(Request $request): JsonResponse
    {
        $apartments = Apartment::where('user_id', NULL)->get();
        if ($request->filled('page') && $request->filled('page_size')) {
            $apartments = $apartments->skip(($request->page - 1) * $request->page_size)->take($request->page_size);
        }
        $result = ApartmentResource::collection($apartments);
        return $this->success($result);
    }

    public function getApartmentNotOwnedAndId(Request $request,$id): JsonResponse
    {
        $apartments = Apartment::where('user_id', $id)
                                ->orwhere('user_id', NULL)
                                ->get();
        if ($request->filled('page') && $request->filled('page_size')) {
            $apartments = $apartments->skip(($request->page - 1) * $request->page_size)->take($request->page_size);
        }
        $result = ApartmentResource::collection($apartments);
        return $this->success($result);
    }

    public function addForm()
    {
        $buildings = Building::all();
        return view('apartment.add', compact('buildings'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAdd(Request $request): JsonResponse
    {
        
        $validator = Validator::make($request->all(),
        [
            'apartment_id' => 'required|string|unique:apartments|regex:/^[a-zA-Z0-9]+$/',
            'floor' => 'required|integer|min:1',
            'status' => 'required|integer|min:0|max:1',
            'square_meters' => 'nullable|numeric|min:1',
            'type_apartment' => 'required|integer|min:0|max:1',
            'building_id' => 'required|integer|min:1',
            'user_id' => 'nullable|integer|min:1'
        ],
        [
            'apartment_id.required' => 'Tên Không được trống',
            'apartment_id.string' => 'Tên phải là chuỗi',
            'apartment_id.unique' => 'Tên căn hộ đã tồn tại',
            'apartment_id.regex' => 'Tên không được chứa kí tự',
            'floor.required' => 'Tầng không được trống ',
            'floor.integer' => 'Tầng phải là định dạn số',
            'floor.min' => 'Tầng không được nhỏ hơn 1',
            'status.required' => 'Trạng thái không được trống',
            'status.integer' => 'Trạng thái phải là số',
            'status.min' => 'Trạng thái là 0 hoặc 1',
            'status.max' => 'Trạng thái là 0 hoặc 1',

            'square_meters.numeric' => 'Diện tích phải là đinh dạng số',
            'square_meters.min' => 'Diện tích không được nhỏ hơn 1',

            'type_apartment.required' => 'Loại căn hộ không được trống',
            'type_apartment.integer' => 'Loại căn hộ phải là số',
            'type_apartment.min' => 'Loại căn hộ phải là 0 hoặc 1',
            'type_apartment.max' => 'Loại căn hộ phải là 0 hoặc 1',

            'building_id.required' => 'Tòa không được trống',
            'building_id.integer' => 'Tòa định dạng phải là số',
            'building_id.min' => 'Tòa nhỏ nhất là 1',

            'user_id.integer' => 'user_id phải là số',
            'user_id.min' => 'User_id nhỏ nhất là 1',

        ]
    );
    if ($validator->fails()) {
        return $this->failed($validator->messages());
    }
        $model = new Apartment();
        $model->fill($request->all());
        $model->save();
        return $this->success($model);
    }

    public function editForm($id)
    {
        $apartment = Apartment::find($id);
        $buildings = Building::all();
        return view('apartment.edit', compact('apartment', 'buildings'));
    }

    public function saveEdit($id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),
        [
            'apartment_id' => [
                'required', 'string','regex:/^[a-zA-Z0-9]+$/',
                Rule::unique('apartments')->ignore($id),
                
            ],
            'floor' => 'required|integer|min:1',
            'status' => 'required|integer|min:0|max:1',
            'square_meters' => 'nullable|numeric|min:0',
            'type_apartment' => 'required|integer|min:0|max:1',
            'building_id' => 'required|integer|min:1',
            'user_id' => 'nullable|integer|min:1'
        ],
        [
            'apartment_id.required' => 'Tên Không được trống',
            'apartment_id.string' => 'Tên phải là chuỗi',
            'apartment_id.unique' => 'Tên đã tồn tại',
            'apartment_id.regex' => 'Tên không được chứa kí tự',
            'floor.required' => 'Tầng không được trống ',
            'floor.integer' => 'Tầng phải là định dạn số',
            'floor.min' => 'Tầng không được nhỏ hơn 1',
            'status.required' => 'Trạng thái không được trống',
            'status.integer' => 'Trạng thái phải là số',
            'status.min' => 'Trạng thái là 0 hoặc 1',
            'status.max' => 'Trạng thái là 0 hoặc 1',
            'square_meters.numeric' => 'Diện tích phải là đinh dạng số',
            'square_meters.min' => 'Diện tích không được nhỏ hơn 1',
            'type_apartment.required' => 'Loại căn hộ không được trống',
            'type_apartment.integer' => 'Loại căn hộ phải là số',
            'type_apartment.min' => 'Loại căn hộ phải là 0 hoặc 1',
            'type_apartment.max' => 'Loại căn hộ phải là 0 hoặc 1',
            'building_id.required' => 'Tòa không được trống',
            'building_id.integer' => 'Tòa định dạng phải là số',
            'building_id.min' => 'Tòa nhỏ nhất là 1',
            'user_id.integer' => 'user_id phải là số',
            'user_id.min' => 'User_id nhỏ nhất là 1',

        ]);
        if ($validator->fails()) {
            return $this->failed($validator->messages());
        }
        $model = Apartment::find($id);
        $model->fill($request->all());
        $model->save();
        return $this->success($model);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getApartmentById($id): JsonResponse
    {
        $apartment = Apartment::leftJoin('users', 'apartments.id', '=', 'users.apartment_id')
            ->join('buildings', 'apartments.building_id', '=', 'buildings.id')
            ->select(
                'apartments.id',
                'apartments.apartment_id',
                'users.phone_number',
                'buildings.id as building_id',
                'buildings.name as building_name',
                'apartments.square_meters',
                'apartments.status',
                'users.name',
                'users.avatar'
            )
            ->where('apartments.id', $id)
            ->get();

        return $this->success($apartment);
    }

    /**
     * Get bill by apartment id
     *
     * @param $id
     * @return JsonResponse
     */
    public function getBillByApartmentId($id): JsonResponse
    {
        $bill_by_apartment_id = Bill::join('apartments', 'bills.apartment_id', '=', 'apartments.id')
            ->leftJoin('bill_details', 'bills.id', '=', 'bill_details.bill_id')
            ->leftJoin('services', 'bill_details.service_id', '=', 'services.id')
            ->join('users', 'apartments.user_id', '=', 'users.id')
            ->select(
                'bills.id',
                'bills.name as ten_hoa_don',
                'users.name as ten_chu_ho',
                'apartments.apartment_id',
                'bills.amount',
                'bills.status',
                'bills.created_at',
                'bills.updated_at'
            )
            ->withCount('billDetail as so_luong_hdct')
            ->distinct()
            ->where('apartments.id', $id)
            ->get();
        return $this->success($bill_by_apartment_id);
    }

    public function getUnpaidBillByApartmentId($id): JsonResponse
    {
        $unpaid_bill_by_apartment_id = Bill::join('apartments', 'bills.apartment_id', '=', 'apartments.id')
            ->join('bill_details', 'bills.id', '=', 'bill_details.bill_id')
            ->join('services', 'bill_details.service_id', '=', 'services.id')
            ->join('users', 'apartments.user_id', '=', 'users.id')
            ->select(
                'bills.id',
                'bills.name as ten_hoa_don',
                'users.name as ten_chu_ho',
                'apartments.apartment_id',
                'bills.amount',
                'bills.status',
                'bills.created_at',
                'bills.updated_at'
            )
            ->withCount('billDetail as so_luong_hdct')
            ->distinct()
            ->where('apartments.id', $id)
            ->where('bills.status', 0)
            ->get();
        return $this->success($unpaid_bill_by_apartment_id);
    }

    public function getPaidBillByApartmentId($id): JsonResponse
    {
        $paid_bill_by_apartment_id = Bill::join('apartments', 'bills.apartment_id', '=', 'apartments.id')
            ->join('bill_details', 'bills.id', '=', 'bill_details.bill_id')
            ->join('services', 'bill_details.service_id', '=', 'services.id')
            ->join('users', 'apartments.user_id', '=', 'users.id')
            ->select(
                'bills.id',
                'bills.name as ten_hoa_don',
                'users.name as ten_chu_ho',
                'apartments.apartment_id',
                'bills.amount',
                'bills.status',
                'bills.created_at',
                'bills.updated_at'
            )
            ->withCount('billDetail as so_luong_hdct')
            ->distinct()
            ->where('apartments.id', $id)
            ->where('bills.status', 1)
            ->get();
        return $this->success($paid_bill_by_apartment_id);
    }

    /**
     * Get build detail by apartment ID
     *
     * @param $id
     * @param $bill_id
     * @return JsonResponse
     */
    public function getBillDetailByApartmentId($id, $bill_id): JsonResponse
    {
        $bill_detail_by_apartment_id = BillDetail::join('services', 'bill_details.service_id', '=', 'services.id')
            ->join('bills', 'bill_details.bill_id', '=', 'bills.id')
            ->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
            ->select(
                'bill_details.id',
                'bill_details.bill_id',
                'bills.name as ten_hoa_don',
                'services.name as ten_dich_vu',
                'services.price as don_gia',
                'bill_details.quantity',
                'bill_details.total_price',
                'bills.apartment_id'
            )
            ->where('bill_details.bill_id', $bill_id)
            ->where('apartments.id', $id)
            ->get();
        return $this->success($bill_detail_by_apartment_id);
    }

    public function addCardForm($id)
    {
        return view('apartment.add-card');
    }

    public function saveAddCard($id, Request $request): JsonResponse
    {
        $number = rand(100000000, 999999999);
        $count_exist_number = Card::where('number', $number)->count();
        while ($count_exist_number > 0) {
            $number = rand(100000000, 999999999);
            $count_exist_number = Card::where('number', $number)->count();
        }

        $count_card_by_apartment_id = Card::where('apartment_id', $request->apartment_id)->count();
        //Giới hạn mỗi phòng chỉ có tối đa 5 thẻ
        if ($count_card_by_apartment_id > 4){
            return $this->failed();
        }

        $card = new Card();
        $card->fill($request->all());
        $card->number = $number;
        $card->apartment_id = $id;
        $card->save();
        return $this->success($card);
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function uploadApartment(Request $request)
    {
        $fileUpload = $request->file('file_upload');
        try {
            $dataApartments = Excel::toArray(new BaseImport(), $fileUpload);
            $isValidData = true;
            foreach ($dataApartments as $dataApartment) {
                if ($dataApartment <= 0) {
                    $isValidData = false;
                }
            }
            if ($isValidData) {
                Excel::import(new BaseImport(), $fileUpload);
                return $this->success(__('string.success'));
            }
            return $this->failed('string.failed');
        } catch (\Exception $message) {
            Log::error($message->getMessage());
            return redirect()->back()->withErrors(__('string.admin_error'));
        }
    }
}
