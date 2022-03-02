<?php

namespace App\Http\Controllers;

use App\Exports\ApartmentsExport;
use App\Http\Resources\ApartmentResource;
use App\Http\Resources\DepartmentResource;
use App\Imports\ApartmentsImport;
use App\Models\Apartment;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\Building;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ApartmentController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getApartment(Request $request): JsonResponse
    {
        $apartments = ApartmentResource::collection(Apartment::all());
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
                    'buildings.name as building_id',
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
                    'buildings.name as building_id',
                    'users.email',
                    'users.phone_number',
                    'users.name'
                )
                ->where('users.phone_number', $request->keyword)
                ->orWhere('users.email', 'like', '%' . $request->keyword . '%')
                ->orWhere('users.name', 'like', '%' . $request->keyword . '%')
                ->get();
        } else if ($request->filled('building_id') && !$request->filled('keyword')) {
            $apartments = ApartmentResource::collection(Apartment::where('building_id', $request->building_id)->get());
        }

        return $this->success($apartments);
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
        $model = new Apartment();
        $model->fill($request->all());
        $model->save();

        return $this->success($model);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getApartmentInfo($id): JsonResponse
    {
        $apartment = Apartment::join('users', 'apartments.id', '=', 'users.apartment_id')
            ->join('buildings', 'apartments.building_id', '=', 'buildings.id')
            ->select(
                'apartments.apartment_id',
                'users.phone_number',
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
            ->join('bill_details', 'bills.id', '=', 'bill_details.bill_id')
            ->join('services', 'bill_details.service_id', '=', 'services.id')
            ->join('users', 'apartments.user_id', '=', 'users.id')
            ->select(
                'bills.id',
                'bills.name as ten_hoa_don',
                'users.name as ten_chu_ho',
                'apartments.apartment_id',
                'bills.amount',
                'bills.status'
            )
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
                'bills.status'
            )
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
                'bills.status'
            )
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
                'bill_details.bill_id',
                'bills.name as ten_hoa_don',
                'services.name as ten_dich_vu',
                'bill_details.quantity',
                'bill_details.total_price',
                'bills.apartment_id'
            )
            ->where('bill_details.bill_id', $bill_id)
            ->where('apartments.id', $id)
            ->get();

        return $this->success($bill_detail_by_apartment_id);
    }

    public function fileImport(Request $request)
    {
        Excel::import(new ApartmentsImport, $request->file('file')->store('excelFolder'));
        return back();
    }

    public function fileExport()
    {
        return Excel::download(new ApartmentsExport, 'apartment-collection.xlsx');
    }
}