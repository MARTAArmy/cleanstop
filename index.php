<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <title>Bus Stops</title>

    <link href='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.css">
    <link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css">
    <link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css">
    <link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.43.0/L.Control.Locate.css">

    <link rel="stylesheet" href="libs/leaflet-groupedlayercontrol/leaflet.groupedlayercontrol.css">
    <link rel="stylesheet" href="css/app.css">

    <link rel="icon" sizes="196x196" href="img/favicon.png">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>

<body>

    <div id="container">
        <div id="map"></div>
    </div>

    <div id="total-donation" style="display: none">
    <?php include('php/getTotal.php'); ?>
    </div>

    <div class="modal fade" id="featureModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-primary" id="stop-details-modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p id="stop-details-working">Just a sec...</p>
                    <p id="stop-details-error">Oops, something went wrong! Please try again later :(</p>
                    <div class="tabbable" id="stop-details-tabs"> <!-- Only required for left/right tabs -->
                        <ul class="nav nav-tabs" id="modal-tab-links">
                            <li class="active"><a href="#stop-details-modal-info" data-toggle="tab">Stops Information</a></li>
                            <li><a href='#donate-tab' data-toggle="tab">Donate</a></li>
                            <li><a href="#stop-details-modal-donors-details" data-toggle="tab" >Donors for this stop</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="stop-details-modal-info"></div>
                            
                            <div class="tab-pane" id="donate-tab">
                                <h3>Donate to this bus stop</h3>
                                <p id='donation-working-message'>Working... please do not close this window</p>
                                <form class="form" id='donation-form'>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="donation-name">Your name (you can use a nickname too!)</label>
                                            <input type="text" class="form-control" name="name" id="donation-name" required/>
                                        </div>
                                        <div class="form-group">
                                            <label for="donation-email">Your email (will be kept ABSOLUTELY private)</label>
                                            <input type="email" class="form-control" name="email" id="donation-email" required/>
                                        </div>
                                        <div class="form-group">
                                            <label for="donation-amount">Donation Amount</label>
                                            <input type="text" class="form-control" id="donation-amount" name="amount" required/>
                                        </div>
                                        <div class="form-group">
                                            <label for="donation-comments">Comments</label>
                                            <textarea class="form-control" id="donation-comments" name="comments" required></textarea>
                                        </div>
                                    </div>                            
                                    <button type="submit" class="btn btn-primary">Donate</button>
                                </form>
                            </div>
                            
                            <div class="tab-pane" id="stop-details-modal-donors-details"></div>
                        </div>
                    </div>
                </div>
               <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id='donate-modal-button'>Donate</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <script>L_PREFER_CANVAS = true;</script>
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>

    <script src="https://checkout.stripe.com/checkout.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.5/typeahead.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.3/handlebars.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>

    <script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>
    <script src="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js"></script>
    <script src="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.43.0/L.Control.Locate.min.js"></script>
    <script src="libs/leaflet-groupedlayercontrol/leaflet.groupedlayercontrol.js"></script>

    <script src="libs/leaflet.ajax.min.js"></script>
    <script src="libs/autoNumeric.js"></script>
    <script src="libs/jquery.form.js"></script>

    <script src="js/eastpoint-outline.js"></script>
    <script src="js/app.js"></script>

</body>
</html>
