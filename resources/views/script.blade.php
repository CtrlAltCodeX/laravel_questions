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

    $(document).on('change', '.select_category', function() {
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


 $(document).on('change', '.select_category', function () {
    const categoryId = $(this).val();

    if (categoryId) {
        $.ajax({
            url: '/get-subcategories/' + categoryId,
            method: 'GET',
            success: function (data) {
                const dropdownMenu = $('#dropdownMenu');
                dropdownMenu.empty(); // Clear old checkboxes

                // Add Select All option
                dropdownMenu.append(`
                    <label class="flex items-center px-4 py-2">
                        <input type="checkbox" onchange="toggleSelectAll(this)" />
                        <span class="ml-2">Select All</span>
                    </label>
                `);

                // Add each sub-category
                data.forEach(function (sub) {
                    dropdownMenu.append(`
                        <label class="flex items-center px-4 py-2">
                            <input type="checkbox" name="subcategories[]" value="${sub.id}" class="subcategory-checkbox" data-name="${sub.name}" />
                            <span class="ml-2">${sub.name}</span>
                        </label>
                    `);
                });

                // Re-bind change listener for label update
                $('.subcategory-checkbox').on('change', function () {
                    updateSubCategoryLabel();
                    fetchSubjects(); // if you want to fetch subjects on change
                });

                // Reset label
                updateSubCategoryLabel();
            }
        });
    } else {
        $('#dropdownMenu').html('<p class="text-sm text-gray-400 px-4 py-2">Please select a category</p>');
        updateSubCategoryLabel(); // Clear label
    }
});


    $(document).on('change', '.select_sub_category', function() {
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

    $(document).on('change', '.select_subject', function() {
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
    });

    $("#pagination").on("change", function() {
        $("#paginationForm").submit();
    });

    // Handle form submission
    document.getElementById('modalForm').addEventListener('submit', function(event) {
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
            document.getElementById('modal').style.display = 'none';  // hide modal
            location.reload();
        } else if (data.errors) {
          // 🔴 First clear all previous error messages
    const errorFields = ['name', 'language_id', 'category_id', 'sub_category_id', 'subject_id', 'discount', 'valid_until', 'mode', 'status'];
    errorFields.forEach(field => {
        const errorContainer = document.getElementById(`error-${field}`);
        if (errorContainer) {
            errorContainer.innerText = ''; // Clear old errors
        }
    });

    // 🔵 Show new validation errors
    for (const [field, messages] of Object.entries(data.errors)) {
        const errorContainer = document.getElementById(`error-${field}`);
        if (errorContainer) {
            errorContainer.innerText = messages.join(', ');
        }
    }
        } else {
            console.error('Unknown error:', data);
        }
            })
            .catch(error => console.error('Error:', error));
    });

    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('modal').style.display = 'none';
    });
</script>