<form action="" method="POST" class="remove-record-model" id="deleteModal_form">
    @csrf
    @method('delete')
    <div class="modal fade delete_modal" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <button type="button" class="btn btn-danger light remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info">Delete</button>
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