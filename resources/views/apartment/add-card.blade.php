<form action="" method="POST">
    @csrf
    <div>
        <label for="">Number</label>
        <input type="number" name="number">
    </div>
    <div>
        <label for="">Tên</label>
        <input type="text" name="name">
    </div>
    <div>
        <label for="">Trạng thái</label>
        <select name="status" id="">
            <option value="">Chọn trạng thái</option>
            <option value="0">Chưa kích hoạt</option>
            <option value="1">Đã kích hoạt</option>
        </select>
    </div>
    <div>
        <label for="">Ngày hết hạn</label>
        <input type="datetime-local" name="expire_time">
    </div>
    <div>
        <button type="submit">Thêm</button>
    </div>
</form>