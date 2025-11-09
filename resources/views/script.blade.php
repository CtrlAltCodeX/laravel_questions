<script>
    $(document).on('change', '.select_language', function() {
        var languageId = $(this).val();
        getCategories(languageId);
    });

    $(document).on('change', '.select_category', function() {
        var categoryId = $(this).val();
        getSubCategories(categoryId);
    });

    $(document).on('change', '.select_sub_category', function() {
        var subCategoryId = $(this).val();
        console.log(subCategoryId);
        getSubjects(subCategoryId);
    });

    $(document).on('change', '.select_subject', function() {
        var subjectId = $(this).val();
        getTopics(subjectId);
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

        const form = this;
        const formData = new FormData(form);
        const actionUrl = form.action;

        // --- beforeSend equivalent ---
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerText = 'Saving...';

        // Optionally show a loading spinner
        const loader = document.getElementById('form-loader');
        if (loader) loader.classList.remove('hidden');

        // --- actual fetch call ---
        fetch(actionUrl, {
            method: form.method,
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(response => response.json())
        .then(data => {
            // --- success logic ---
            if (data.success) {
                alert(data.message);
                document.getElementById('modal').style.display = 'none';
                location.reload();
            } 
            // --- validation errors ---
            else if (data.errors) {
                const errorFields = [
                    'name', 'language_id', 'category_id', 'sub_category_id',
                    'subject_id', 'discount', 'valid_until', 'mode', 'status'
                ];

                errorFields.forEach(field => {
                    const errorContainer = document.getElementById(`error-${field}`);
                    if (errorContainer) errorContainer.innerText = '';
                });

                for (const [field, messages] of Object.entries(data.errors)) {
                    const errorContainer = document.getElementById(`error-${field}`);
                    if (errorContainer) errorContainer.innerText = messages.join(', ');
                }
            } else {
                console.error('Unknown error:', data);
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            // --- after completion ---
            submitButton.disabled = false;
            submitButton.innerText = 'Save';

            if (loader) loader.classList.add('hidden');
        });
    });

    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('modal').style.display = 'none';
    });

    function getCategories(languageId, categoryId = null)
    {
        if (languageId) {
            return $.ajax({
                url: '/get-categories/' + languageId,
                method: 'GET',
                success: function(data) {
                    $('.select_category').empty().append('<option value="">Select Category</option>');
                    $.each(data, function(key, value) {
                        $('.select_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });

                    $('.select_category').val(categoryId);
                }
            });
        } else {
            $('.select_category').empty().append('<option value="">Select Category</option>');
        }
    }

    function getSubCategories(categoryId, subCategoryId = null)
    {
        if (categoryId) {
            return $.ajax({
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
    }

    function getSubjects(subCategoryId)
    {
        if (subCategoryId) {
            return $.ajax({
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
    }

    function getTopics(subjectId)
    {
        if (subjectId) {
            return $.ajax({
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
    }

    $(document).ready(function() {
        $(".editButton").click(function() {
            var data = JSON.parse($(this).attr('data'));
            const languageId = data.language_id;
            const categoryId = data.category_id;
            const subCategoryId = data.sub_category_id;
            getCategories(languageId, categoryId);
            getSubCategories(categoryId, subCategoryId);
            getSubjects(subCategoryId);
        })
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
                        // updateSubCategoryLabel();
                        fetchSubjects(); // if you want to fetch subjects on change
                    });

                    // Reset label
                    // updateSubCategoryLabel();
                }
            });
        } else {
            $('#dropdownMenu').html('<p class="text-sm text-gray-400 px-4 py-2">Please select a category</p>');
            // updateSubCategoryLabel(); // Clear label
        }
    });
</script>