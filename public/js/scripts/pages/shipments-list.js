$(function () {
    ;('use strict')

    var dtTable = $('.shipments-list-table'),
        viewSidebar = $('.view-shipment-modal');


    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }
    if (dtTable.length) {
        dtTable.dataTable({
            ajax: function (data, callback, settings) {
                // make a regular ajax request using data.start and data.length
                $.ajax({
                    url: assetPath + 'api/admin/shipments/list',
                    data: {
                        length: data.length,
                        lang: $('html').attr('lang'),
                        start: data.start,
                        draw: data.draw,
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
                {data: 'contract_id'},
                {data: 'contract.tenant.user.contact_name'},
                {data: 'port_from'},
                {data: 'port_to'},
                {data: 'vessel.owner.user.contact_name'},
                {data: 'vessel'},
                {data: 'date'},
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
                    targets: [4, 5],
                    render: function (data, type, full, meta) {
                        return data ? data.name_translation : '-';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type, full, meta) {
                        return data.name + "<a href='" + assetPath + "admin/track/" + data.id + "'>" + feather.icons['map-pin'].toSvg({class: 'font-small-4 me-50'}) + "</a>";
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
                '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
                '<"col-sm-12 col-lg-8 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
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
                $(document).on('click', '.trashed-item', function () {
                    $('#trashed').val($(this).data('trashed'));
                    dtTable.DataTable().ajax.reload();
                });
            },
            // Buttons with Dropdown
            buttons: [
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
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/' + $('html').attr('lang') + '.json'
            },
        })
    }

    $(document).on('click', '.item-delete', function () {
        var element = $(this);
        $.ajax({
            type: 'DELETE',
            url: assetPath + 'api/admin/shipments/' + element.data('id'),
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

    $(document).on('click', '.status-switcher', function () {
        let element = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to change status for this item?",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: LANG.Cancel,
            confirmButtonText: 'Yes',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'PUT',
                    url: assetPath + 'api/admin/shipments/status/' + element.data('id'),
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
        $('#view-contract').html(data.contract_id);
        $('#view-tenant').html(data.contract.tenant.user.contact_name);
        $('#view-owner').html(data.vessel.owner.user.contact_name);
        $('#view-vessel').html(data.vessel.name);
        $('#view-origin').html(data.port_from.name_translation);
        $('#view-destination').html(data.port_to.name_translation);
        $('#view-date').html(data.date);
        viewSidebar.modal('show');
    });

})
