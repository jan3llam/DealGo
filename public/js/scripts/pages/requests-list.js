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

    var dtTable = $('.tickets-list-table'),
        statusObj = {
            1: {title: 'مفتوح', class: 'badge-light-success status-switcher'},
            0: {title: 'مغلق', class: 'badge-light-secondary status-switcher'}
        }


    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        dtTable.DataTable({
            ajax: assetPath + 'api/admin/tickets/list',
            columns: [
                // columns according to JSON
                {data: ''},
                {data: 'id'},
                {data: 'user.name'},
                {data: 'title'},
                {data: 'text'},
                {data: 'files'},
                {data: 'status'},
                {data: 'created_at'},
                {data: 'updated_at'},
                {data: 'deleted_at'},
                {data: ''}
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: 'control',
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return ''
                    }
                },
                {
                    targets: 6,
                    render: function (data, type, full, meta) {
                        var $status = full['status']
                        return (
                            '<span class="badge rounded-pill btn ' +
                            statusObj[$status].class +
                            '" text-capitalized data-id="' + full['id'] + '">' +
                            statusObj[$status].title +
                            '</span>'
                        )
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, full, meta) {
                        var string = '';
                        data.forEach(item => {
                            string += '<a target="_blank" href="' + assetPath + 'images/' + item + '" >' +
                                feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>';
                        });
                        return string;
                    }
                },
                {
                    targets: [7, 8, 9],
                    render: function (data, type, full, meta) {
                        return data ? moment(data).format('DD-MM-YYYY') : '-'
                    }
                },
                {
                    // Actions
                    targets: -1,
                    title: 'الخيارات',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var btn = '';
                        if (full['status']) {
                            btn = '<a href="javascript:;" class="dropdown-item item-delete" data-id="' + full['id'] + '">' +
                                feather.icons['check'].toSvg({class: 'font-small-4 me-50'}) +
                                'موافقة</a>';
                        }
                        return (
                            '<div class="btn-group">' +
                            '<a class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                            feather.icons['more-vertical'].toSvg({class: 'font-small-4'}) +
                            '</a>' +
                            '<div class="dropdown-menu dropdown-menu-end">' +
                            btn +
                            '<a href="javascript:;" class="dropdown-item item-delete" data-id="' + full['id'] + '">' +
                            feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) +
                            'حذف</a></div>' +
                            '</div>' +
                            '</div>'
                        )
                    }
                }
            ],
            order: [[1, 'desc']],
            dom:
                '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' +
                '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
                '<"col-sm-12 col-lg-8 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
                '>t' +
                '<"d-flex justify-content-between mx-2 row mb-1"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',
            language: {
                sLengthMenu: 'عرض _MENU_',
                search: 'البحث',
                searchPlaceholder: 'البحث..'
            },
            // Buttons with Dropdown
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + 'تصدير',
                    buttons: [
                        {
                            extend: 'print',
                            text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'طباعة',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'csv',
                            text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Csv',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'excel',
                            text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'pdf',
                            text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Pdf',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'copy',
                            text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'نسخ',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        }
                    ],
                    init: function (api, node, config) {
                        $(node).removeClass('btn-secondary')
                        $(node).parent().removeClass('btn-group')
                        setTimeout(function () {
                            $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex mt-50')
                        }, 50)
                    }
                }
            ],
            // For responsive popup
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data()
                            return 'تفاصيل ' + data['name']
                        }
                    }),
                    type: 'column',
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.columnIndex !== 6 // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                col.rowIdx +
                                '" data-dt-column="' +
                                col.columnIndex +
                                '">' +
                                '<td>' +
                                col.title +
                                ':' +
                                '</td> ' +
                                '<td>' +
                                col.data +
                                '</td>' +
                                '</tr>'
                                : ''
                        }).join('')
                        return data ? $('<table class="table"/>').append('<tbody>' + data + '</tbody>') : false
                    }
                }
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/ar.json'
            },
        })
    }

    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        $.ajax({
            type: 'DELETE',
            url: assetPath + 'api/admin/requests/' + element.data('id'),
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

    $(document).on('click', '.status-switcher', function () {
        let element = $(this);
        $.ajax({
            type: 'PUT',
            url: assetPath + 'api/admin/requests/status/' + element.data('id'),
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
    });
})
