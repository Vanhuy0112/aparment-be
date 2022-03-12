<form action="" method="POST">
    @csrf
{{--    <input type="hidden" name="_method" value="put" />--}}
    <div>
        <label for="">Chọn dịch vụ</label>
        <select name="service_id" id="">
            @foreach ($services as $item)
                <option value="{{$item->id}}" @if($bill_detail->service_id == $item->id) selected @endif>{{$item->name . ' - ' . number_format($item->price, 0, ',', '.') . 'VNĐ'}}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="">Chọn hóa đơn</label>
        <select name="bill_id" id="">
            @foreach ($bills as $item)
                <option value="{{$item->id}}" @if($bill_detail->bill_id == $item->id) selected @endif>{{$item->name}}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="">Số lượng</label>
        <input type="number" name="quantity" value="{{$bill_detail->quantity}}">
    </div>
    <div>
        <button type="submit">Sửa</button>
    </div>
</form>
