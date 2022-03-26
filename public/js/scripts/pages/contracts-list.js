$(function () {
    ;('use strict')

    var dtTable = $('.contracts-list-table'), viewSidebar = $('.view-contract-modal'),
        newSidebar = $('.new-payment-modal'), newForm = $('.add-new-payment'), typeObj = {
            1: {title: LANG.Voyage}, 2: {title: LANG.Time}, 3: {title: LANG.Bareboat}, 4: {title: LANG.COA}
        }

    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        dtTable.dataTable({
            ajax: function (data, callback, settings) {
                // make a regular ajax request using data.start and data.length
                $.ajax({
                    url: assetPath + 'api/admin/contracts/list', data: {
                        length: data.length,
                        lang: $('html').attr('lang'),
                        start: data.start,
                        draw: data.draw,
                        search: data.search.value,
                        status: $('#status_filter').val(),
                        direction: data.order[0].dir,
                        order: data.columns[data.order[0].column].data.replace(/\./g, "__"),
                    }, headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                    }, success: function (res) {
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
                    }, error: function (response) {
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
            columns: [// columns according to JSON
                {data: ''}, {data: 'id'},
                {data: 'tenant.user.contact_name'},
                {data: 'owner.user.contact_name'},
                {data: 'type'}, {data: 'date_from'}, {data: 'date_to'}, {data: 'shipments_count'}, {data: 'total'}, {data: ''}],
            columnDefs: [{
                // For Responsive
                className: 'control',
                orderable: false,
                responsivePriority: 2,
                targets: 0,
                render: function (data, type, full, meta) {
                    return ''
                }
            }, {
                targets: 4, render: function (data, type, full, meta) {
                    return ('<span class="rounded-pill" text-capitalized>' + typeObj[data].title + '</span>')
                }
            }, {
                targets: 8, render: function (data, type, full, meta) {
                    return data.toLocaleString(undefined, {minimumFractionDigits: 0});
                }
            }, {
                // Actions
                targets: -1, title: LANG.Actions, orderable: false, render: function (data, type, full, meta) {
                    return ('<div class="btn-group">' + '<a class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' + feather.icons['more-vertical'].toSvg({class: 'font-small-4'}) + '</a>' + '<div class="dropdown-menu dropdown-menu-end">' + '<a href="javascript:;" class="dropdown-item add-payment" data-id="' + full['id'] + '">' + feather.icons['dollar-sign'].toSvg({class: 'font-small-4 me-50'}) + LANG.Payments + '</a>' + '<a href="javascript:;" class="dropdown-item item-view" data-id="' + full['id'] + '">' + feather.icons['eye'].toSvg({class: 'font-small-4 me-50'}) + LANG.View + '</a></div>' + '</div>' + '</div>')
                }
            }],
            order: [[1, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' + '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' + '<"col-sm-12 col-lg-8 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' + '>t' + '<"d-flex justify-content-between mx-2 row mb-1"' + '<"col-sm-12 col-md-6"i>' + '<"col-sm-12 col-md-6"p>' + '>',

            createdRow: function (row, data, index) {
                if (data.deleted_at) {
                    $(row).addClass('table-secondary');
                }
            },
            initComplete: function () {
                $(document).on('click', '.trashed-item', function () {
                    $('#trashed').val($(this).data('trashed'));
                    dtTable.DataTable().ajax.reload();
                });
            }, // Buttons with Dropdown
            buttons: [{
                extend: 'collection',
                className: 'btn btn-outline-secondary dropdown-toggle me-2',
                text: feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + LANG.Export,
                buttons: [{
                    extend: 'print',
                    text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + LANG.Print,
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }, {
                    extend: 'csv',
                    text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'CSV',
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }, {
                    extend: 'excel',
                    text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Excel',
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }, {
                    extend: 'pdf',
                    text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'PDF',
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }, {
                    extend: 'copy',
                    text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + LANG.Copy,
                    className: 'dropdown-item',
                    exportOptions: {columns: [1, 2, 3, 4, 5]}
                }],
                init: function (api, node, config) {
                    $(node).removeClass('btn-secondary')
                    $(node).parent().removeClass('btn-group')
                    setTimeout(function () {
                        $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex mt-50')
                    }, 50)
                }
            },],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/' + $('html').attr('lang') + '.json'
            },
        })
    }

    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        $.ajax({
            type: 'DELETE', url: assetPath + 'api/admin/contracts/' + element.data('id'), dataType: 'json', headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
            }, success: function (response) {
                if (parseInt(response.code) === 1) {
                    dtTable.DataTable().ajax.reload();
                    toastr['success'](response.message);
                } else {
                    toastr['error'](response.message);
                }
            }
        })
    });

    if (newForm.length) {
        newForm.on('submit', function (e) {
            let data = new FormData();
            e.preventDefault()
            data.append('object_id', $('#object_id').val());
            newForm.find('input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
                data.append($(this).attr('name'), $(this).val());
            });
            newForm.find('input[type=file]').each(function () {
                data.append($(this).attr('name'), $(this)[0].files[0]);
            });
            $.ajax({
                type: 'POST', url: assetPath + 'api/admin/contracts/payments', dataType: 'json', headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
                }, processData: false, contentType: false, data: data, success: function (response) {
                    if (parseInt(response.code) === 1) {
                        dtTable.DataTable().ajax.reload();
                        toastr['success'](response.message);
                    } else {
                        toastr['error'](response.message);
                    }
                }, error: function (response) {
                    if (parseInt(response.status) === 403) {
                        toastr['error'](LANG[response.status]);
                    } else {
                        toastr['error'](response.statusText)
                    }
                }
            })
            newForm[0].reset();
            newSidebar.modal('hide')
        })
    }

    $('#payments-container').repeater({
        initEmpty: true,
        show: function () {
            $(this).slideDown();
            // Feather Icons
            if (feather) {
                feather.replace({width: 14, height: 14});
            }
        }
    });

    $(document).on('click', '.item-view', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#view-type').html(typeObj[data.type].title);
        $('#view-tenant').html(data.tenant.user.contact_name);
        $('#view-owner').html(data.owner.user.contact_name);
        $('#view-start').html(data.date_from);
        $('#view-end').html(data.date_to);
        $('#view-value').html(data.total);
        viewSidebar.modal('show');
    });

    $(document).on('click', '.add-payment', function () {
        var element = $(this);

        $('#form_status').val(1);
        let data = dtTable.api().row(element.parents('tr')).data();
        $('#object_id').val(data.id);
        $('#view-payments').find('tr').remove();

        newForm.find('#city_id,input[type=text],input[type=date],input[type=email],input[type=number],input[type=password],input[type=tel],textarea,select').each(function () {
            $(this).val('');
        });

        data.payments.forEach(item => {
            if (item.is_down) {
                $('#view-down-value').html(item.value.toLocaleString(undefined, {minimumFractionDigits: 0}));
                $('#view-down-description').html(item.description);
                if (!item.submit_date) {
                    $('#view-down-submit').show().attr('name', 'payment[' + item.id + '][date]').data('paid', 1);
                    $('#view-down-text').hide();
                } else {
                    $('#view-down-text').show().html(item.submit_date);
                    $('#view-down-submit').data('paid', 0).hide();
                }
            } else {
                $('#payments-container').prepend($('<div>')
                    .append($('<div class="mb-1 row">')
                        .append($('<div class="col">')
                            .append($('<label class="form-label" for="value">').html(LANG.PaymentDue))
                            .append($(`<input type="number" class="form-control dt-full-name" placeholder="${LANG.PaymentDue}" name="payment[${item.id}][value]">`).val(item.value)))
                        .append($('<div class="col">')
                            .append($('<label class="form-label" for="paid">').html(LANG.Payment))
                            .append($(`<input type="number" class="form-control dt-full-name" placeholder="${LANG.Payment}" name="payment[${item.id}][paid]">`).val(item.paid)))
                        .append($('<div class="col">')
                            .append($('<label class="form-label" for="next">').html(LANG.NextPaymentDate))
                            .append($(`<input type="date" class="form-control dt-full-name" placeholder="${LANG.NextPaymentDate}" name="payment[${item.id}][next]">`).val(item.date)))
                        .append($('<div class="col">')
                            .append($('<label class="form-label" for="date">').html(LANG.SubmittedDate))
                            .append($(`<input type="date" class="form-control dt-full-name" placeholder="${LANG.SubmittedDate}" name="payment[${item.id}][date]">`).val(item.submit_date)))
                        .append($('<div class="col">')
                            .append($('<label class="form-label" for="description">').html(LANG.Description))
                            .append($(`<input type="text" class="form-control dt-full-name" placeholder="${LANG.Description}" name="payment[${item.id}][date]">`).val(item.description))))
                )
            }
        })

        $('[data-repeater-list]').empty();

        newSidebar.modal('show');
    });
})
