
function group(){
	
	var me = this;
	this.map = "";
	this.events = null;
	this.poly = null;
	
	this.initialize = function () {
	  
	  gmap = this;
	  
	  if (GBrowserIsCompatible()) {
	    this.map = new GMap2(document.getElementById("map_canvas"));
	    this.map.setCenter(new GLatLng(-23.606110,-46.693027), 16);
	  }
	
	  this.map.addControl(new GSmallMapControl());
	  this.map.addControl(new GMapTypeControl());
	  
	  this.geocoder = new GClientGeocoder();
	
	};
	
	this.initDrawPolygon = function (){
		
		this.removeListeners();
		
		this.removePoly();
		
		var gArea = new GPolygon( new GLatLng(this.map.getCenter()), "#284283",5,1, "#92A4D3",0.4 );
		this.events = GEvent.addListener(gArea,"endline",function () {
			
			var points = new Array(); 
			for(var i=0; i < gArea.getVertexCount(); i++){
				points[i] = gArea.getVertex(i);
			}
			
			$('#area_coords').val(points.toString());
			
			me.removeListeners();
		});
	
		this.events = GEvent.addListener(gArea,"click",function () {
		
		});
		
		this.map.addOverlay(gArea);
		this.poly = gArea;
		gArea.enableDrawing();
	
	};
	
	this.initAddVenue = function(){
		this.removeListeners();
		this.events = GEvent.addListener(this.map, "click",  function(overlay, latlng) {
			me.addVenue(overlay,latlng);
		});
		
	};
	
	this.addVenue = function (overlay, latlng){
		this.origLatLng = latlng;
		this.geocoder.getLocations(latlng, me.callShow);
		
	};
	
	this.callShow = function (response){
		me.showVenueForm(response);
	};
	
	this.showVenueForm = function (response){
		
		//Set vars for later overwriting or  not
		var address = "";
		var latlng = me.origLatLng;
		
		if (response.Status.code == 200) {
			
			place = response.Placemark[0];
			
			if (place.AddressDetails.Accuracy >= 8){
				
				address = place.address;
				latlng = new GLatLng(place.Point.coordinates[1],place.Point.coordinates[0]);
				
			}
			
			
		}
		
		//Populate form
		$("#vn_coords").val( latlng.lat()+"/"+latlng.lng() );
		$("#vn_address").val( address );
		
		//Show form
		$('#vn_form_div').dialog('open');
		this.origLatLng = null;

	};
	
	this.createVenueMarker = function(data){
		
		$('#vn_form_div').dialog('close');
		var coordsString = $("#vn_coords").val();
		var coords = coordsString.split("/");

		//save to DB
		var reqData = $("[id^=vn_]").serializeArray();
		reqData[reqData.length] = {"name": "gid", "value": $('#tmp_id').val()};
		$.post("admin/venue/add",reqData,me.checkAdd,'json');
		
		point = new GLatLng( coords[0], coords[1] );
		marker = new GMarker(point);
		me.map.addOverlay(marker);
		
		me.removeListeners();
	};
	
	this.checkAdd = function(response){
		
		alert(response.msg);
		
	};
	
	this.removeListeners = function(){
		
		if (this.events != null){
			GEvent.removeListener(this.events);
			this.events = null;
		}
	};
	
	this.removePoly = function (){
		if (this.poly != null){
			this.map.removeOverlay(this.poly);
			this.poly = null;
		}
	};
	
	this.admins = new Array();
	
	this.addAdmin = function(){
		
		if ($.inArray($('#admins_add').val(),this.admins) < 0){
			this.admins[$('#admins_add').val()] = $('#admins_add').val();
			$('#admins_list').append("<li>"+$('#admins_add').find('option').filter(':selected').text()+"</li>");
		}
	};
	
	this.parseAdmins = function(){
		
		$('#admins').val( this.admins.toString() );
		
	};
	
	
	this.showArea = function(vertexes){
		
		var gArea = new GPolygon( new GLatLng(this.map.getCenter()), "#284283",5,1, "#92A4D3",0.4 );
		
		for(var i=0; i < vertexes.length; i++){
			gArea.insertVertex(i, new GLatLng(vertexes[i].lat,vertexes[i].lng) );
		}
		
		this.map.addOverlay(gArea);
		this.poly = gArea;
		
	};
	
	this.showVenues = function (venues){
		
		for(var i=0; i < venues.length; i++){
			point = new GLatLng( venues[i].coords.lat, venues[i].coords.lng, {icon:'default'} );
			this.map.addOverlay( this.createMarkerVenues(point) );
			
		}
	};
	
	this.createMarkerVenues = function(point){
		
		var marker = new GMarker(point);
		
		GEvent.addListener(marker, "click", function() {
			
			marker.openInfoWindowHtml("<b>Loading...</b>");
			
			var request = GXmlHttp.create();
			request.open("GET", "/", true);
			request.onreadystatechange = function() {
				//marker.openInfoWindowHtml("resultado");//venues[i].name + "\n" + venues[i].address);
			}
			request.send(null);
			
          });
		
		return marker;
		
	}
	
	
	
	//constructor
	this.initialize();
}

function initMap(instName){
	eval( instName + " = new group();" );
}

