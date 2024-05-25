<div class="table-responsive-md">
    <table class="table table-hover table-responsive-md">
        <thead>
            <tr>
                <th>#</th>
                <th class="width50">
                    <div class="common-check-main">
                        <label class="custom-control overflow-checkbox mb-0">
                            <input class="overflow-control-input" id="checkAll" type="checkbox"
                                required="">
                            <span class="overflow-control-indicator"></span>
                            <span class="overflow-control-description"></span>
                        </label>
                    </div>
                </th>
                <th>Merchant name</th>
                <th>EMail</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $value)
            <tr>
                <td>{{ $value['id'] }}</td>
                <th>
                    <label class="custom-control overflow-checkbox">
                        <input type="checkbox" class="overflow-control-input multiselect"
                            name="user_id[]" id="customCheckBox_{{ $value['id'] }}"
                            value="{{ $value['id'] }}">
                        <span class="overflow-control-indicator"></span>
                        <span class="overflow-control-description"></span>
                    </label>
                </th>
                <td>{{ $value['business_name'] }}</td>
                <td>{{ $value['email'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>