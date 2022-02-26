$(function () {
    ;('use strict')

    var assetPath = '../../../app-assets/';

    if ($('body').attr('data-framework') === 'laravel') {
        assetPath = $('body').attr('data-asset-path')
    }

    $('.vessels-select2').select2({
        ajax: {
            url: assetPath + 'api/admin/vessels/list',
            dataType: 'json',
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
                        text: item.name,
                    };
                });
                return {results: data};
            }
        }
    }).on("change.select2", function () {
        $.ajax({
            url: assetPath + 'api/admin/vessels/check/' + $(this).find("option:selected").val(),
            type: 'GET',
            cache: false,
            contentType: 'application/json',
            dataType: "json",
            success: function (result) {
                if (parseInt(result.code) === 1) {
                    var latlng = new google.maps.LatLng(result.data.latitude, result.data.longitude);
                    if (!marker) {
                        marker = new google.maps.Marker({
                            position: latlng,
                            map,
                            title: "Vessel (" + result.data.name + ")",
                            icon: {
                                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                                scale: 6,
                                fillColor: "red",
                                fillOpacity: 0.8,
                                strokeWeight: 2,
                                rotation: parseInt(result.data.rotation)
                            }
                        });
                    } else {
                        var icon = marker.getIcon();
                        icon.rotation = result.data.rotation;
                        marker.setIcon(icon);
                        marker.setPosition(latlng);
                        marker.setTitle("Vessel (" + result.data.name + ")");
                    }
                    if (info) {
                        info.close();
                    }
                    info = new google.maps.InfoWindow({
                        content: "Vessel (" + result.data.name + ")"
                    });
                    map.setCenter(latlng);
                    info.open(map, marker);
                } else {
                    toastr['error'](result.message);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            }
        });
    });
})
