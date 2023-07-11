{{-- JQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
    $(function() {
        $("#sortable tbody").sortable({
            update: function(event, ui) {
                var positions = [];
                $("#sortable tbody tr").each(function(index) {
                    positions.push($(this).data("id"));
                });

                $.ajax({
                    url: "{{ route('course.sort') }}",
                    method: "POST",
                    data: {
                        positions: positions
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        console.log("成功")
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            },
        }).disableSelection();
    });
</script>
