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
    public function getApartment(Request $request)
    {
        $apartments = Apartment::paginate(5);
        $buildings = Building::all();
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

        return view('apartments.index', compact('apartments', 'buildings'));
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
        return view('apartments.add', compact('buildings'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAdd(ApartmentRequest $request)
    {
        $model = new Apartment();
        $model->fill($request->all());
        $model->save();
        return redirect(route('apartment'))->with('message', 'Thêm mới căn hộ thành công!');
    }

    public function editForm($id)
    {
        $apartment = Apartment::find($id);
        $buildings = Building::all();
        return view('apartments.edit', compact('apartment', 'buildings'));
    }

    public function saveEdit($id, ApartmentRequest $request)
    {
        $model = Apartment::find($id);
        $model->fill($request->all());
        $model->save();
        return redirect(route('apartment.edit', ['id' => $id]))->with('message', 'Sửa căn hộ thành công!');
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
