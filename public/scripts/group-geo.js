
groupMap = {

	map: "",
	
	initialize: function () {
	
	  if (GBrowserIsCompatible()) {
	    this.map = new GMap2(document.getElementById("map_canvas"));
	    this.map.setCenter(new GLatLng(37.4419, -122.1419), 13);
	  }
	
	  this.map.addControl(new GSmallMapControl());
	  this.map.addControl(new GMapTypeControl());
	
	},
	
	initDrawPolygon: function (){
		
		var gArea = new GPolygon( new GLatLng(this.map.getCenter()), "#284283",5,1, "#92A4D3",0.4 );
		GEvent.addListener(gArea,"endline",function () {
	
			alert( gArea.getArea());

			});
	
		this.map.addOverlay(gArea);
		gArea.enableDrawing();
	
	},
	
	addVenue: function (){
		
	  	GEvent.addListener(this.map, "click",  function(overlay, latlng) {
	  		this.map.addOverlay(new GMarker(latlng));
		});
		
	}
};