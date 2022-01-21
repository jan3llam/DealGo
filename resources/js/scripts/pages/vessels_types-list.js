$(function () {
    ;('use strict')

    var dtTable = $('.vessels-types-list-table'),
        newSidebar = $('.new-vessels-type-modal'),
        newForm = $('.add-new-vessels-type');

    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        dtTable.dataTable({
            ajax: assetPath + 'api/admin/vessels-types/list',
            columns: [
                // columns according to JSON
                {data: ''},
                {data: 'id'},
                {data: 'name'},
                {data: 'parent'},
                {data: 'dwt'},
                {data: 'draught'},
                {data: 'loa'},
                {data: 'geared'},
                {data: 'holds'},
                {data: 'vessels_count'},
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
                    targets: 3,
                    render: function (data, type, full, meta) {
                        return data ? data.name : '-';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type, full, meta) {
                        return data == 1 ? 'Yes' : 'No';
                    }
                },
                {
                    // Actions
                    targets: -1,
                    title: 'Actions',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return (
                            '<div class="btn-group">' +
                            '<a class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                            feather.icons['more-vertical'].toSvg({class: 'font-small-4'}) +
                            '</a>' +
                            '<div class="dropdown-menu dropdown-menu-end">' +
                            '<a href="javascript:;" class="dropdown-item item-update" data-id="' + full['id'] + '">' +
                            feather.icons['edit'].toSvg({class: 'font-small-4 me-50'}) +
                            'Edit</a>' +
                            '<a href="javascript:;" class="dropdown-item item-delete" data-id="' + full['id'] + '">' +
                            feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) +
                            'Delete</a></div>' +
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
                sLengthMenu: 'Showing _MENU_',
                search: 'Search',
                searchPlaceholder: 'Search..'
            },
            // Buttons with Dropdown
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + 'Export',
                    buttons: [
                        {
                            extend: 'print',
                            text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'Print',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'csv',
                            text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'CSV',
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
                            text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'PDF',
                            className: 'dropdown-item',
                            exportOptions: {columns: [1, 2, 3, 4, 5]}
                        },
                        {
                            extend: 'copy',
                            text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Copy',
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
                },
                {
                    text: 'Add new',
                    className: 'add-vessels-type btn btn-primary',
                    attr: {
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#modals-slide-in'
                    },
                    init: function (api, node, config) {
                        $(node).removeClass('btn-secondary')
                    }
                }
            ],
            // For responsive popup
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data()
                            return 'Details of  ' + data['name']
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
            }
        })
    }

    if (newForm.length) {
        let data = new FormData();

        newForm.validate({
            errorClass: 'error',
            rules: {
                'name': {
                    required: true
                },
                'dwt': {
                    required: true
                },
                'loa': {
                    required: true
                },
                'draught': {
                    required: true
                },
                'geared': {
                    required: true
                },
                'holds': {
                    required: true
                },
                'description': {
                    required: true
                },
            }
        })

        $('#parent').select2();

        newForm.on('submit', function (e) {
            var isValid = newForm.valid()
            var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';
            e.preventDefault()
            if (isValid) {
                if (type === 'update') {
                    data.append('object_id', $('#object_id').val());
                }
                newForm.find('input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
                    data.append($(this).attr('name'), $(this).val());
                });
                $.ajax({
                    type: 'POST',
                    url: assetPath + 'api/admin/vessels-types/' + type,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function (response) {
                        if (parseInt(response.code) === 1) {
                            dtTable.DataTable().ajax.reload();
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    }
                })
                newForm[0].reset();
                newSidebar.modal('hide')
            }
        })
    }


    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        $.ajax({
            type: 'DELETE',
            url: assetPath + 'api/admin/vessels-types/' + element.data('id'),
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

    $(document).on('click', '.item-update', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#modals-slide-in').modal('show')
        $('#form_status').val(2);
        $('#name').val(data.name);
        $('#dwt').val(data.dwt);
        $('#draught').val(data.draught);
        $('#loa').val(data.loa);
        $('#geared').val(data.geared);
        $('#holds').val(data.holds);
        $('#parent').val(data.parent_id);
        $('#parent').trigger('change.select2');
        $('#description').val(data.description);
        $('#object_id').val(data.id);
    });

    $(document).on('click', '.add-vessels-type', function () {
        $('#form_status').val(1);
        $('#object_id').val('');
        newForm.find('#parent_id,input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        })
    });
})
