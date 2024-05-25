<form action="" method="POST" class="remove-record-model" id="deleteModal_form">
    @csrf
    @method('delete')
    <div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Record </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info  remove-data-from-delete-form"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Delete</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).on("click", ".delete_modal", function () {
        var url = $(this).attr("data-url");
        var id = $(this).attr("data-id");
        $('#deleteModal_form').attr('action' , url)
        $('#delete_modal').modal('show')
    });
</script>

<form action="" method="POST" class="remove-record-model" id="restoreModal_form">
    @csrf
    <div class="modal fade" id="restore_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Restore Record </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to restore this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info  remove-data-from-restore-form"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Restore</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).on("click", ".restore_modal", function () {
        var url = $(this).attr("data-url");
        var id = $(this).attr("data-id");
        $('#restoreModal_form').attr('action' , url)
        $('#restore_modal').modal('show')
    });
</script>