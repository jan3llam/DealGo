$(function () {
    ;('use strict')

    var dtTable = $('.responses-list-table'),
        newSidebar = $('.new-response-modal'),
        newForm = $('.add-new-response'),
        viewSidebar = $('.view-offer-modal');


    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }

    var btn = $('#offer_id').val() ?
        [
            {
                extend: 'collection',
                className: 'btn btn-outline-secondary dropdown-toggle me-2',
                text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + LANG.Export,
                buttons: [
                    {
                        extend: 'print',
                        text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + LANG.Print,
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
                        text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + LANG.Copy,
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
            }, {
            extend: 'collection',
            className: 'btn btn-outline-secondary dropdown-toggle me-2',
            text: LANG.Status,
            buttons: [
                {
                    text: LANG.Pending, attr: {
                        "data-status": 0
                    }, className: 'status-item dropdown-item',
                },
                {
                    text: LANG.Approved, attr: {
                        "data-status": 1
                    }, className: 'status-item dropdown-item',
                },
                {
                    text: LANG.Rejected, attr: {
                        "data-status": 2
                    }, className: 'status-item dropdown-item',
                },
                {
                    text: LANG.Trashed, attr: {
                        "data-status": 3
                    }, className: 'status-item dropdown-item',
                }],
            init: function (api, node, config) {
                $(node).removeClass('btn-secondary')
                $(node).parent().removeClass('btn-group')
                setTimeout(function () {
                    $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex mt-50')
                }, 50)
            }
        }, {
            className: 'items-delete btn btn-danger me-2',
            text: feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + LANG.Delete,
            init: function (api, node, config) {
                $(node).removeClass('btn-secondary')
                if (!$('#request_id').val()) {
                    node.remove();
                }
            }
        },
            {
                text: LANG.AddNew,
                className: 'add-response btn btn-primary',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#modals-slide-in',
                    'data-bs-backdrop': 'static',
                    'data-bs-keyboard': 'false'
                },
                init: function (api, node, config) {
                    $(node).removeClass('btn-secondary')
                }
            }
        ] :
        [
            {
                extend: 'collection',
                className: 'btn btn-outline-secondary dropdown-toggle me-2',
                text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + LANG.Export,
                buttons: [
                    {
                        extend: 'print',
                        text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + LANG.Print,
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
                        text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + LANG.Copy,
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
                extend: 'collection',
                className: 'btn btn-outline-secondary dropdown-toggle me-2',
                text: LANG.Status,
                buttons: [
                    {
                        text: LANG.Pending, attr: {
                            "data-status": 0
                        }, className: 'status-item dropdown-item',
                    },
                    {
                        text: LANG.Approved, attr: {
                            "data-status": 1
                        }, className: 'status-item dropdown-item',
                    },
                    {
                        text: LANG.Rejected, attr: {
                            "data-status": 2
                        }, className: 'status-item dropdown-item',
                    },
                    {
                        text: LANG.Trashed, attr: {
                            "data-status": 3
                        }, className: 'status-item dropdown-item',
                    }],
                init: function (api, node, config) {
                    $(node).removeClass('btn-secondary')
                    $(node).parent().removeClass('btn-group')
                    setTimeout(function () {
                        $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex mt-50')
                    }, 50)
                }
            }, {
            className: 'items-delete btn btn-danger me-2',
            text: feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + LANG.Delete,
            init: function (api, node, config) {
                $(node).removeClass('btn-secondary')
                if (!$('#request_id').val()) {
                    node.remove();
                }
            }
        },
        ];

    if (dtTable.length) {
        dtTable.dataTable({
            ajax: function (data, callback, settings) {
                // make a regular ajax request using data.start and data.length
                $.ajax({
                    url: assetPath + 'api/admin/offers_responses/list',
                    data: {
                        length: data.length,
                        lang: $('html').attr('lang'),
                        start: data.start,
                        draw: data.draw,
                        offer_id: $('#offer_id').val(),
                        search: data.search.value,
                        status: $('#status_filter').val(),
                        direction: data.order[0].dir,
                        order: data.columns[data.order[0].column].data.replace(/\./g, "__"),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    },
                    success: function (res) {
                        if (parseInt(res.code) === 1) {
                            callback({
                                draw: res.data.meta.draw,
                                recordsTotal: res.data.meta.total,
                                recordsFiltered: res.data.meta.count,
                                data: res.data.data
                            });
                        } else {
                            toastr['error'](res.message);
                        }
                    },
                    error: function (response) {
                        if (parseInt(response.status) === 403) {
                            toastr['error'](LANG[response.status]);
                        } else {
                            toastr['error'](response.statusText)
                        }
                    }
                });
            },
            processing: true,
            serverSide: true,
            columns: [
                // columns according to JSON
                {data: ''},
                {data: 'id'},
                {data: 'id'},
                {data: 'tenant.user'},
                {data: 'offer.port_from'},
                {data: 'port_to'},
                {data: 'date_from'},
                {data: 'total'},
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
                    // For Checkboxes
                    targets: 1,
                    orderable: false,
                    responsivePriority: 3,
                    render: function (data, type, full, meta) {
                        return (
                            '<div class="form-check"> <input class="form-check-input dt-checkboxes" type="checkbox" value="' + data + '" id="checkbox-' +
                            data +
                            '" /><label class="form-check-label" for="checkbox-' +
                            data +
                            '"></label></div>'
                        );
                    },
                    checkboxes: {
                        selectRow: true,
                        selectAllRender:
                            '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>'
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, full, meta) {
                        return data ? data.contact_name : '-';
                    }
                },
                {
                    targets: [4, 5],
                    render: function (data, type, full, meta) {
                        return data ? data.name_translation : '-';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type, full, meta) {
                        var sum = 0;
                        full['payments'].forEach(item => {
                            sum += item.value;
                        })
                        return sum.toLocaleString(undefined, {minimumFractionDigits: 0});
                    }
                },
                {
                    // Actions
                    targets: -1,
                    title: LANG.Actions,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return (
                            '<div class="btn-group">' +
                            '<a class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                            feather.icons['more-vertical'].toSvg({class: 'font-small-4'}) +
                            '</a>' +
                            '<div class="dropdown-menu dropdown-menu-end">' +
                            '<a href="javascript:;" class="dropdown-item item-view" data-id="' + full['id'] + '">' +
                            feather.icons['eye'].toSvg({class: 'font-small-4 me-50'}) +
                            LANG.View + '</a>' +
                            '<a href="javascript:;" class="dropdown-item item-approve" data-id="' + full['id'] + '">' +
                            feather.icons['check'].toSvg({class: 'font-small-4 me-50'}) +
                            LANG.Approve + '</a>' +
                            '<a href="javascript:;" class="dropdown-item item-delete" data-id="' + full['id'] + '">' +
                            feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) +
                            LANG.Delete + '</a></div>' +
                            '</div>' +
                            '</div>'
                        )
                    }
                }
            ],
            order: [[1, 'desc']],
            dom:
                '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' +
                '<"col-sm-12 col-lg-3 d-flex justify-content-center justify-content-lg-start" l>' +
                '<"col-sm-12 col-lg-9 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
                '>t' +
                '<"d-flex justify-content-between mx-2 row mb-1"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',

            createdRow: function (row, data, index) {
                if (data.deleted_at) {
                    $(row).addClass('table-secondary');
                }
            },
            initComplete: function () {
                $(document).on('click', '.status-item', function () {
                    $('#status_filter').val($(this).data('status'));
                    dtTable.DataTable().ajax.reload();
                });
            },
            // Buttons with Dropdown
            buttons: btn,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/' + $('html').attr('lang') + '.json'
            },
        })
    }

    if (newForm.length) {

        $(document).on('change', '#contract', function () {
            var element = $(this);

            if (parseInt(element.val()) === 1) {
                $('#routes_container').hide();
            } else {
                $('#routes_container').show();
            }

        });

        $('#goods_container').repeater({
            initEmpty: true,
            show: function () {
                $(this).slideDown(function () {
                    $(this).find('.goods-select2').select2({dropdownParent: newSidebar});
                    $('[name^="goods"]').each(function () {
                        $(this).rules('add', {
                            required: true,
                        });
                    });
                });
                // Feather Icons
                if (feather) {
                    feather.replace({width: 14, height: 14});
                }
            }
        });

        $('#payments_container').repeater({
            initEmpty: true,
            show: function () {
                $(this).slideDown();
                $('[name^="payments"]').each(function () {
                    $(this).rules('add', {
                        required: true,
                    });
                });
                // Feather Icons
                if (feather) {
                    feather.replace({width: 14, height: 14});
                }
            }
        });

        $('#routes_container').repeater({
            initEmpty: true,
            show: function () {
                $(this).slideDown(function () {
                    $(this).find('.routes-select2').select2({
                        dropdownParent: newSidebar,
                        ajax: {
                            url: assetPath + 'api/admin/ports/list',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                            },
                            data: function (params) {
                                return {
                                    search: params.term,
                                    start: params.page || 0
                                }
                            },
                            processResults: function (data) {
                                data = data.data.data.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.name_translation,
                                    };
                                });
                                return {results: data};
                            }
                        }
                    });
                    $('[name^="routes"]').each(function () {
                        $(this).rules('add', {
                            required: true,
                        });
                    });
                });
                // Feather Icons
                if (feather) {
                    feather.replace({width: 14, height: 14});
                }
            }
        });

        $('#tenant,#contract').select2({dropdownParent: newSidebar});

        $('#port_from,#port_to').select2({
            dropdownParent: newSidebar,
            ajax: {
                url: assetPath + 'api/admin/ports/list',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                },
                data: function (params) {
                    return {
                        search: params.term,
                        start: params.page || 0
                    }
                },
                processResults: function (data) {
                    data = data.data.data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name_translation,
                        };
                    });
                    return {results: data};
                }
            }
        })

        $.validator.addMethod("greaterThan", function (value, element, params) {

            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) > new Date($(params).val());
            }

            return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val()));
        }, LANG.GreaterThanMsg);

        newForm.validate({
            errorClass: 'error',
            rules: {
                'tenant': {
                    required: true
                },
                'date': {
                    required: true
                },
                'down_value': {
                    required: true
                },
                'down_description': {
                    required: true
                },
                'name': {
                    required: true
                },
                'port_from': {
                    required: true
                },
                'port_to': {
                    required: true
                },
                'contract': {
                    required: true
                },
                'date_from': {
                    required: true
                },
                'date_to': {
                    required: true, greaterThan: "#date_from"
                },
                'description': {
                    required: true
                },
            }
        })

        $("#files").fileinput({'showUpload': false, 'previewFileType': 'any'});

        newForm.on('submit', function (e) {
            var isValid = newForm.valid()
            var type = parseInt($('#form_status').val()) === 1 ? 'add' : 'update';
            let data = new FormData();

            e.preventDefault()
            if (isValid) {
                if (type === 'update') {
                    data.append('object_id', $('#object_id').val());
                }
                data.append('offer', $('#offer_id').val());
                newForm.find('input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
                    data.append($(this).attr('name'), $(this).val());
                });
                $.ajax({
                    type: 'POST',
                    url: assetPath + 'api/admin/offers_responses/' + type,
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
                            dtTable.DataTable().ajax.reload();
                            toastr['success'](response.message);
                            newForm[0].reset();
                            newSidebar.modal('hide')
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

    $(document).on('change', '.calculate-value', function () {
        var sum = 0;
        $('.calculate-value').each(function () {
            sum += parseInt($(this).val());
        });
        $('#total').val(sum.toLocaleString(undefined, {minimumFractionDigits: 0}));
    });

    $(document).on('click', '.items-delete', function () {
        var ids = dtTable.api().columns().checkboxes.selected()[1];
        if (ids.length) {
            Swal.fire({
                title: LANG.AreYouSure,
                text: LANG.DeleteMsg,
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: LANG.Cancel,
                confirmButtonText: $.validator.format(LANG.ConfirmBulkDelete, [ids.length]),
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'DELETE',
                        url: assetPath + 'api/admin/offers_responses/bulk',
                        data: {ids: ids},
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                        },
                        success: function (response) {
                            if (parseInt(response.code) === 1) {
                                dtTable.DataTable().ajax.reload();
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
        } else {
            Swal.fire({
                title: LANG.Error,
                text: LANG.ChooseErrorMsg,
                icon: 'error',
                confirmButtonText: LANG.Ok,
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            })
        }
    })

    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        $.ajax({
            type: 'DELETE',
            url: assetPath + 'api/admin/offers_responses/' + element.data('id'),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
            },
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

    $(document).on('click', '.item-approve', function () {
        let element = $(this);
        Swal.fire({
            title: LANG.AreYouSure,
            text: LANG.ApproveMsg,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: LANG.Cancel,
            confirmButtonText: LANG.Yes,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'PUT',
                    url: assetPath + 'api/admin/offers_responses/approve/' + element.data('id'),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    },
                    success: function (response) {
                        if (parseInt(response.code) === 1) {
                            dtTable.DataTable().ajax.reload();
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
    });

    $(document).on('click', '.item-view', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#view-name').html(data.name);
        $('#view-tenant').html(data.tenant.user.contact_name);
        $('#view-date-from').html(data.date_from);
        $('#view-date-to').html(data.date_to);
        $('#view-description').html(data.description);
        $('#view-origin').html(data.port_from.name_translation);
        $('#view-destination').html(data.port_to.name_translation);
        if (parseInt(data.contract) === 1) {
            $('#view-routes-container').hide();
        } else {
            $('#view-routes').find('tr').remove();
            data.routes.forEach(item => {
                $('#view-routes').append($('<tr>')
                    .append($('<td>')
                        .html(item.name_translation)
                    )
                );
            })
            $('#view-routes-container').show();
        }
        $('#view-loads').find('tr').remove();
        data.goods_types.forEach(item => {
            $('#view-loads-container').show();
            $('#view-loads').append($('<tr>')
                .append($('<td>')
                    .html(item.good_type.name_translation)
                )
                .append($('<td>')
                    .html(item.weight)
                )
            );
        })
        $('#view-payments').find('tr').remove();
        data.payments.forEach(item => {
            if (item.is_down) {
                $('#view-down-value').html(item.value.toLocaleString(undefined, {minimumFractionDigits: 0}));
                $('#view-down-description').html(item.description);
            } else {
                $('#view-payments-container').show();
                $('#view-payments').append($('<tr>')
                    .append($('<td>')
                        .html(item.value.toLocaleString(undefined, {minimumFractionDigits: 0}))
                    )
                    .append($('<td>')
                        .html(item.date)
                    )
                    .append($('<td>')
                        .html(item.description)
                    )
                );
            }
        });
        viewSidebar.modal('show');
    });

    $(document).on('click', '.add-response', function () {
        $('#form_status').val(1);
        $('#image_container').attr('src', '');
        $('#object_id').val('');
        newForm.find('#city_id,input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        });
        $('#owner').trigger('change.select2');
        $("#files").fileinput('destroy').fileinput({'showUpload': false, 'previewFileType': 'any'});
    });
})
