<div id="model-bind">
    <div class="modal-header">
        <h4 class="modal-title">Merchant list</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
    </div>
    <div class="modal-body">
        <div class="table-responsive-md">
            <table class="table table-hover table-responsive-md">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Merchant name</th>
                        <th>EMail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $value)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $value['id'] }}</td>
                            <td>{{ $value['business_name'] }}</td>
                            <td>{{ $value['email'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
    </div>
</div>
