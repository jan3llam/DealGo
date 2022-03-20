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
                $.get(assetPath + 'api/admin/shipments/list', {
                    length: data.length,
                    lang: $('html').attr('lang'),
                    start: data.start,
                    draw: data.draw,
                    search: data.search.value,
                    trashed: $('#trashed').val(),
                    direction: data.order[0].dir,
                    order: data.columns[data.order[0].column].data.replace(/\./g, "__"),
                }, function (res) {
                    callback({
                        draw: res.data.meta.draw,
                        recordsTotal: res.data.meta.total,
                        recordsFiltered: res.data.meta.count,
                        data: res.data.data
                    });
                });
            },
            processing: true,
            serverSide: true,
            columns: [
                // columns according to JSON
                {data: ''},
                {data: 'id'},
                {data: 'contract.id'},
                {data: 'tenant.name'},
                {data: 'port_from.name'},
                {data: 'port_to.name'},
                {data: 'owner.name'},
                {data: 'vessel.name'},
                {data: 'end_at'},
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
                {
                    extend: 'collection',
                    className: 'btn btn-outline-secondary dropdown-toggle me-2',
                    text: feather.icons['trash'].toSvg({class: 'font-small-4 me-50'}) + 'Trashed',
                    buttons: [
                        {
                            text: 'Yes',
                            attr: {
                                "data-trashed": 1
                            },
                            className: 'trashed-item dropdown-item',
                        },
                        {
                            text: 'No',
                            attr: {
                                "data-trashed": 0
                            },
                            className: 'trashed-item dropdown-item',
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
                    success: function (response) {
                        if (parseInt(response.code) === 1) {
                            dtTable.DataTable().ajax.reload();
                            toastr['success'](response.message);
                        } else {
                            toastr['error'](response.message);
                        }
                    }
                })
            }
        })
    });

    $(document).on('click', '.item-view', function () {
        var element = $(this);
        let data = dtTable.api().row(element.parents('tr')).data().user;
        viewSidebar.modal('show');
        $('#view-type').html(data.type);
        if (data.type == 1) {
            $('#view-company-container').show();
        }

        $('#view-legal').html('<a href="' + assetPath + 'images/' + data.legal_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#view-license').html('<a href="' + assetPath + 'images/' + data.license_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');
        $('#view-company').html('<a href="' + assetPath + 'images/' + data.company_file + '">' + feather.icons['external-link'].toSvg({class: 'font-small-4 me-50'}) + '</a>');

        $('#view-name').html(data.full_name);
        $('#view-contact').html(data.contact_name);
        $('#view-commercial').html(data.commercial_number);
        $('#view-email').html(data.email);
        $('#view-phone').html(data.phone);
        $('#view-country').html(data.city.country.name).trigger('change.select2');
        $('#view-city').html(data.city.name);
        $('#view-address-1').html(data.address_1);
        $('#view-address-2').html(data.address_2);
        $('#view-zip').html(data.zip_code);

    });
})
