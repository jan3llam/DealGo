$(function () {
    const
        editUserForm = $('#editUserForm');

    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }

    // Edit user form validation
    if (editUserForm.length) {
        editUserForm.validate({
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true
                },
                gsm: {
                    required: true
                }
            }
        });

        editUserForm.on('submit', function (e) {
            var isValid = editUserForm.valid()
            e.preventDefault()
            if (isValid) {
                let data = new FormData();
                data.append('name', $('#name').val());
                data.append('phone', $('#phone').val());
                data.append('email', $('#email').val());
                data.append('_method', 'PUT');
                $.ajax({
                    type: 'PUT',
                    url: assetPath + 'api/admin/users/update/' + $('#object-id').val(),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function (response) {
                        if (parseInt(response.code) === 1) {
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    },
                    error: function (response) {
                        if (parseInt(response.status) === 403) {
                            toastr['error'](LANG[response.status]);
                        } else {
                            toastr['error'](response.statusText)
                        }
                    }
                })
            }
        })
    }
});
