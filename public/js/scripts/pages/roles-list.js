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
        roleTitle = $('.role-title');

    roleAdd.on('click', function () {
        roleTitle.text('Add New Role'); // reset text
    });
    roleEdit.on('click', function () {
        roleTitle.text('Edit Role');
        $('#name').val(data.name);
        $('#contact').val(data.description);
    });


    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        $.ajax({
            type: 'DELETE',
            url: assetPath + 'api/admin/roles/' + element.data('id'),
            dataType: 'json',
            success: function (response) {
                if (parseInt(response.code) === 1) {
                    dtTable.DataTable().ajax.reload();
                    toastr['success'](response.message);
                } else {
                    toastr['error'](response.message);
                }
            }
        })
    })
})
