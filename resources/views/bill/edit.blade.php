<form action="" method="POST">
    @csrf
    <div>
        <label for="">Tên hóa đơn</label>
        <input type="text" name="name" value="{{$bill->name}}">
    </div>
    <div>
        <label for="">Trạng thái</label>
        <select name="status" id="" disabled>
            <option value="0" @if($bill->status == 0) selected @endif>Chưa thanh toán</option>
            <option value="1" @if($bill->status == 1) selected @endif>Đã thanh toán</option>
        </select>
    </div>
    <div>
        <label for="">Tổng tiền</label>
        <input type="number" name="amount" value="{{$bill->amount}}" disabled>
    </div>
    <div>
        <label for="">Căn hộ</label>
        <input type="text" name="apartment_id" value="{{$bill->apartment->apartment_id}}" disabled>
    </div>
    <div>
        <label for="">Ghi chú</label>
        <textarea name="notes" cols="30" rows="5">{{$bill->notes}}</textarea>
    </div>
    <div>
        <button type="submit">Sửa</button>
    </div>
</form>
