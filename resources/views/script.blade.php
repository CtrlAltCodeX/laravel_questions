<script>
    $(document).on('change', '.select_language', function() {
    // $('#select_language').change(function() {
        var languageId = $(this).val();

        if (languageId) {
            $.ajax({
                url: '/get-categories/' + languageId,
                method: 'GET',
                success: function(data) {
                    $('.select_category').empty().append('<option value="">Select Category</option>');
                    $.each(data, function(key, value) {
                        $('.select_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            $('.select_category').empty().append('<option value="">Select Category</option>');
        }
    });

    $('.select_category').change(function() {
        var categoryId = $(this).val();

        if (categoryId) {
            $.ajax({
                url: '/get-subcategories/' + categoryId,
                method: 'GET',
                success: function(data) {
                    $('.select_sub_category').empty().append('<option value="">Select Sub Category</option>');
                    $.each(data, function(key, value) {
                        $('.select_sub_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            $('.select_sub_category').empty().append('<option value="">Select Sub Category</option>');
        }
    });

    $('.select_sub_category').change(function() {
        var subCategoryId = $(this).val();

        if (subCategoryId) {
            $.ajax({
                url: '/get-subjects/' + subCategoryId,
                method: 'GET',
                success: function(data) {
                    $('.select_subject').empty().append('<option value="">Select Subject</option>');
                    $.each(data, function(key, value) {
                        $('.select_subject').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            $('.select_subject').empty().append('<option value="">Select Subject</option>');
        }
    });

    $('.select_subject').change(function() {
        var subjectId = $(this).val();

        if (subjectId) {
            $.ajax({
                url: '/get-topics/' + subjectId,
                method: 'GET',
                success: function(data) {
                    $('.select_topic').empty().append('<option value="">Select Topic</option>');

                    $.each(data, function(key, value) {
                        $('.select_topic').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            $('.select_topic').empty().append('<option value="">Select Topic</option>');
        }
    });
    
    $(document).on('input', 'input', function() {
        $("#selectlangauge").val($("#select_language").val());
    })

    $(document).on('change', "#qno", function() {
        $.ajax({
            url: "{{ route('get.question_no') }}",
            method: 'GET',
            data: {
                "category_id": $('#select_category').val(),
                "sub_category_id": $('#select_sub_category').val(),
                "subject_id": $('#select_subject').val(),
                "topic_id": $('#select_topic').val(),
                "q_no": $(this).val(),
                exist: true
            },
            success: function(data) {
                if (data != '0') {
                    $("#qno").val('');
                    alert('Question already Exist!!');
                }
            }
        });
    })


         // Handle form submission
         document.getElementById('modalForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(this);
            const actionUrl = this.action;

            fetch(actionUrl, {
                method: this.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message); 
            $('#modal').hide();
            location.reload(); 
                } else {
                    // Handle validation errors if any
                    console.error(data.errors);
                }
            })
            .catch(error => console.error('Error:', error));
        });

document.getElementById('closeModal').addEventListener('click', function () {
    document.getElementById('modal').style.display = 'none';
});
</script>