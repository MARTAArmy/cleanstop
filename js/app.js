var map, featureList, busstopsSearch = [];
var code, email;

$(window).resize(function()
{
    $(".leaflet-control-layers").css("max-height", $("#map-container").height());
});

$('#donation-amount').autoNumeric('init', {aSign: '$ '});

var $donateModalButton = $('#donate-modal-button');
var $donateTabLink = $('#modal-tab-links a[href="#donate-tab"]');
var $stopDetailsTabLink = $('#modal-tab-links a[href="#stop-details-modal-info"]');
var $donateTabBody = $('#donate-tab');

$donateModalButton.click(function ()
{
    if ($donateTabLink.is(':visible'))
    {
        $donateTabLink.tab('show');
    }
});
$donateTabLink.on('hide.bs.tab', function ()
{
    $donateModalButton.show();    
});
$donateTabLink.on('show.bs.tab', function ()
{
    $donateModalButton.hide();
});

(function setupStripe()
{
    var _formData = null;

    var stripeHandler = StripeCheckout.configure("customButtonA", 
    {
        key: 'your-stripe-public-key',
        name: 'The Marta Army',
        zipCode: true,
        // image: todo,
        token: function(token, args)
        {
            $('#donation-form').hide();
            $('#donation-working-message').show();

            $.ajax(
            {
                method: "POST",
                url: "php/processDonation.php",
                data: 
                {
                    token: token.id,
                    stopcode: _formData.stopcode, 
                    name: _formData.name,
                    email: _formData.email,
                    amount: _formData.amount,
                    comments: _formData.comments                    
                }
            }).done(function( msg ) 
            {
                _formData = null;
                $('#donation-form').show();
                $('#donation-working-message').hide();
                
                $('#donation-form').clearForm();

                alert('Thank you for your donation!');
            }).fail( function (xhr, stat, err)
            {
                $('#donation-form').show();
                $('#donation-working-message').hide();
                alert(xhr.responseText)
            });
        }
    });

    $('#donation-form').validate(
    {
        submitHandler: function (form)
        {
            _formData = {
                name: $('#donation-name').val(),
                email: $('#donation-email').val(),
                amount: $('#donation-amount').autoNumeric('get') * 100,
                comments: $('#donation-comments').val(),
                stopcode: $('#stop-details-stopcode').text()
            }

            stripeHandler.open(
            {
                amount: _formData.amount,
                email: _formData.email,
                description: 'Donation for Stop#' + _formData.stopcode
            });

            return false;
        }
    });

    // Close Checkout on page navigation:
    $(window).on('popstate', function() 
    {
        stripeHandler.close();
    });
})();

function showStopDetailsModal(stop) // todo fix this
{
    $stopDetailsTabLink.tab('show');

    $("#stop-details-modal-title").html(stop.name);
    
    $('#stop-details-working').show();
    $('#stop-details-error').hide();
    $('#stop-details-tabs').hide();
    $('#donate-modal-button').hide();
    $('#donation-working-message').hide();
    $('#donation-form').show();

    if (stop.totalAmountReceived >= 20000)
    {
        $donateTabLink.hide();
        $donateModalButton.css(
        {
            opacity: 0,
            float: 'left'
        });
    }
    else
    {
        $donateTabLink.show();
        $donateModalButton.css(
        {
            opacity: 1,
            float: 'none'
        });
    }

    $("#featureModal").modal("show");
    
    $.ajax(
    {
        method: "GET",
        url: "php/getStopDonors.php",
        dataType: 'json',
        data: 
        { 
            stopcode: stop.stopcode,
            t: Date.now()
        }
    })
    .done(function (donors) 
    {
        var modalContent = 
        "<h3>Stop Information</h3>" +
        "<table class='table table-striped table-bordered table-condensed'>" + 
            "<tr><th>Name</th><td class='name'>" + stop.name + "</td></tr>" + 
            "<tr><th>Stop Code</th><td id='stop-details-stopcode' class='stopcode'>" + stop.stopcode + "</td></tr>" + 
            "<tr><th>Total amount received so far</th><td class='amount'> $" + stop.totalAmountReceived/100 + "</td></tr>"+ 
            // "<tr><th>Number of people using this stop daily</th><td class='usage'>" + stop.ridership + "</td></tr>" +
        "<table>";

        if (stop.totalAmountReceived > 20000)
        {
            modalContent += "<br/><h5>Thanks to your donations, this stop has reached its fundraising goal!</h5>";
        }

        $("#stop-details-modal-info").html(modalContent);

        if (!donors || !donors.length)
        {
            $("#stop-details-modal-donors-details").html('<h3>Donors for this stop</h3><p>No one has donated yet.</p>');
        }
        else
        {
            var donorsTable = "<h3>Donors for this stop</h3>" +
                "<table class='table table-striped table-bordered table-condensed'>" +
                "<thead><tr><th>Name</th><th>Amount</th></tr></thead><tbody>";

            for (var i = 0; i<donors.length; i++)
            {
                var donor = donors[i];
                donorsTable += '<tr><td>' + donor.name + '</td><td>' + donor.amount/100 + '</td></tr>'; 
            }
            donorsTable += '</tbody></table>';

            $("#stop-details-modal-donors-details").html(donorsTable);
        }

        $('#stop-details-working').hide();
        $('#stop-details-tabs').show();
        $('#donate-modal-button').show();
        //$('#donate-modal-button').hide();
    }).fail( function (xhr, stat, err)
    {
        $('#stop-details-working').hide();
        $('#stop-details-error').show();
    });
}

(function setupMap()
{
    L.mapbox.accessToken = 'pk.eyJ1IjoiaHVsazcyMiIsImEiOiJjaW90amNkaGYwMGJ6dGhtNDA2dng3cmkzIn0.h5Dtfee38GAIkJ78Zk7W_Q';
    
    var southWest = L.latLng(33.621758,-84.548093);
    var northEast = L.latLng(33.711815,-84.399038);
    var mybounds = L.latLngBounds(southWest, northEast);

    var map = L.mapbox.map("map", "mapbox.streets", 
    {
        zoom: 12,
        center: [33.674417, -84.469483],
        maxZoom: 18,
        minZoom: 13,
        zoomControl: false,
        maxBounds: mybounds
    });
    
    var TotalDonationControl = L.Control.extend(
    {
        options: 
        {
            position: 'topleft' 
        },
 
        onAdd: function (map) 
        {
            var $container = $('<div class="leaflet-bar leaflet-control leaflet-control-custom"></div>');
            var total = $('#total-donation').text();
            total = parseInt(total) / 100;
            $container.html('<b>Total donation so far:</b><br/>$' + total);
 
            $container.css(
            {
                backgroundColor: 'white',
                width: '160px',
                padding: '5px',
                fontSize: '14px'
            });

            return $container[0];
        }
    });

    map.addControl(new TotalDonationControl());   

    new L.Control.Zoom({ position: 'topleft' }).addTo(map);
 
    L.control.locate(
    {
        position: "topleft",
        drawCircle: true,
        follow: true,
        setView: true,
        keepCurrentZoomLevel: true,
        markerStyle: { weight: 1, opacity: 0.8, fillOpacity: 0.8 },
        circleStyle: { weight: 1, clickable: false },
        icon: "fa fa-location-arrow",
        metric: false,
        strings: { title: "My location", popup: "You are within {distance} {unit} from this point", outsideMapBoundsMsg: "You seem located outside the boundaries of the map" },
        locateOptions: { maxZoom: 18, watch: true, enableHighAccuracy: true, maximumAge: 10000, timeout: 10000 }
    }).addTo(map);

    var eastPointOutline = L.geoJson(eastpointJsonData, 
    {
        style: function(feature) 
        {
            switch (feature.properties.Name) 
            {
                case 'East Point': return {color: "#FF851B"};
            }
        }
    });
    map.addLayer(eastPointOutline);

    $.ajax(
    {
        method: "GET",
        url: "php/getAllStops.php",
        dataType: 'json',
        data: 
        { 
            t: Date.now()
        }
    })
    .done(function (data) 
    {
        var GOAL = 20000;
        var busstopMarkers = [];

        data.forEach(function (stop)
        {
            var svg = null;
            if (stop.totalAmountReceived < GOAL)
            {
                var perc = stop.totalAmountReceived / GOAL;
                var val = 2 * 8 * Math.PI * (1 - perc);
                var svg ='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="20" height="20">' +
                            '<circle r="8" cx="10" cy="10" style="stroke:#FF9F1E; fill:#FF9F1E; stroke-width: 2"></circle>' +
                            '<circle r="8" cx="10" cy="10" stroke-dasharray="50.27" stroke-dashoffset="' + val + '" style="stroke:green; fill:transparent; stroke-width: 2"></circle>' +
                         '</svg>';
            }
            else
            {
                var svg ='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="20" height="20">' +
                            '<circle r="8" cx="10" cy="10" style="stroke:green; fill:green; stroke-width: 2"></circle>' +
                         '</svg>';
            }

            var svgURL = "data:image/svg+xml;base64," + btoa(svg);

            var marker = L.marker([stop.lat, stop.lng], 
            { 
                icon: L.icon(
                {
                    iconUrl: svgURL,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [-2, 0]
                }),
                title: stop.name,
            });

            marker.on('click', function ()
            {
                showStopDetailsModal(stop);
            });
            marker.bindPopup(stop.name);
            marker.on('mouseover', function (e) {
                this.openPopup();
            });
            marker.on('mouseout', function (e) {
                this.closePopup();
            });
            
            busstopMarkers.push(marker);
        });

        var markerClustersLayer = new L.MarkerClusterGroup(
        {
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            disableClusteringAtZoom: 16,
            chunkedLoading:true
        });
        markerClustersLayer.addLayers(busstopMarkers);
        map.addLayer(markerClustersLayer);
    })
    .fail( function (xhr, stat, err)
    {
        alert("Oops, we couldn't display the bus stops. Please try refreshing!");
    });
})();
