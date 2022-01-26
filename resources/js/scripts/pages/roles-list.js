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
    });

})
