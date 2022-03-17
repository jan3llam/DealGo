// Add new role Modal JS
//------------------------------------------------------------------
(function () {
    var addRoleForm = $('#addRoleForm');

    // add role form validation
    if (addRoleForm.length) {
        $.validator.addMethod("permissions", function (value, elem, param) {
            return $("input[type=checkbox]:checkbox:checked").length > 0;
        }, "You must select at least one!");

        addRoleForm.validate({
            submitHandler: function (form) {
                if ($("input[type=checkbox]:checkbox:checked").length > 0) {
                    form.submit();
                } else {
                    $('#permissions-msg').html('Please select at least one type of permissions').addClass('error').show();
                }
            },
            rules: {
                name: {
                    required: true
                },
                description: {
                    required: true
                }
            }
        });
    }

    // reset form on modal hidden
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });

    $(document).on('click', '.role-edit-modal', function () {
        var permissions = $(this).data('permissions');
        var name = $(this).data('name');
        var description = $(this).data('description');
        permissions.forEach(item => {
            $('[name=permissions\\[\\]\\[' + item + '\\]]').prop('checked', true);
        });
        $('#modalRoleName').val(name);
        $('#modalRoleDescription').val(description);
    });

    $(document).on('click', '.add-new-role', function () {
        $('[name^=permissions\\[\\]]').prop('checked', false);
        $('#modalRoleName').val('');
        $('#modalRoleDescription').val('');
    });

    // Select All checkbox click
    const selectAll = document.querySelector('#selectAll'),
        checkboxList = document.querySelectorAll('[type="checkbox"]');
    selectAll.addEventListener('change', t => {
        checkboxList.forEach(e => {
            e.checked = t.target.checked;
        });
    });
})();
