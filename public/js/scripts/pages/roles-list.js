/*=========================================================================================
    File Name: app-user-list.js
    Description: User List page
    --------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent

==========================================================================================*/
$(function () {
    ;('use strict')

    var roleEdit = $('.role-edit-modal'),
        roleAdd = $('.add-new-role'),
        roleTitle = $('.role-title'),
        addRoleForm = $('#addRoleForm');
    roleAdd.on('click', function () {
        roleTitle.text('Add New Role'); // reset text
        addRoleForm.attr('action', 'http://dealgo.site/api/admin/roles/add');
        $('#object_id').val('');
    });
    roleEdit.on('click', function () {
        roleTitle.text('Edit Role');
        addRoleForm.attr('action', 'http://dealgo.site/api/admin/roles/update');
        $('#object_id').val($(this).data('id'));
    });

    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }

    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        Swal.fire({
            title: LANG.AreYouSure,
            text: LANG.DeleteMsg,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: LANG.Cancel,
            confirmButtonText: LANG.ConfirmSingleDelete,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'DELETE',
                    url: assetPath + 'api/admin/roles/' + element.data('id'),
                    dataType: 'json',
                    success: function (response) {
                        if (parseInt(response.code) === 1) {
                            toastr['success'](response.message);
                            window.location.reload();
                        } else {
                            toastr['error'](response.message);
                        }
                    }
                })
            }
        });
    })
})
