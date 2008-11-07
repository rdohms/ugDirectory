<?php
/*------------------------------------------------------------------------------
** File:		polygon.geo.class.php
** Description:	PHP class extending 'polygon' class with geo specific methods. 
** Version:		1.0
** Author:		Erie
** Homepage:	Eriestuff.blogspot.com
**------------------------------------------------------------------------------
** COPYRIGHT (c) 2008 ERIE
**
** The source code included in this package is free software; you can
** redistribute it and/or modify it under the terms of the GNU General Public
** License as published by the Free Software Foundation. This license can be
** read at:
**
** http://www.opensource.org/licenses/gpl-license.php
**
** This program is distributed in the hope that it will be useful, but WITHOUT 
** ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
** FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. 
**------------------------------------------------------------------------------
**
** Based on 


** Rev History
** -----------------------------------------------------------------------------
** 1.0	2008/03/02	Initial Release

*/

require_once('polygon.class.php'); // the class to extend

class geo_polygon extends polygon {

	var $GEOmode = TRUE; // default to true, if geomode is not wanted, this class extension can be ommitted

	function setGEOmode($bool){
		$this->GEOmode = ($bool)?TRUE:FALSE;
	}
	// Return if polygon is set in geomode
	function use_geo_mode(){
		return ($this->GEOmode)?TRUE:FALSE;
	}
	// Switch function for distance in geo mode or 2Dplane mode
	function distance($x1,$y1,$x2,$y2){
		if($this->use_geo_mode()){
			return $this->geo_dist($x1,$y1,$x2,$y2);
		}else{
			return $this->dist($x1,$y1,$x2,$y2);
		}
	}
	
	/* 
	 * Calculate geodesic distance (in m) between two points specified by latitude/longitude (in numeric degrees)
	 * following the orthodrome ('as the crow flies') using Vincenty inverse formula for ellipsoids
	 * This method is translated from the javascript version 
	 * found on http://www.movable-type.co.uk/scripts/latlong-vincenty.html
	 * and copyrighted by Chris Veness © 2002-2008
	 * Translated to php by Eriestuff.blogspot.com
	 */
	function geo_dist($lon1, $lat1, $lon2, $lat2) {
		// check if polygon is in geo mode
		if(!$this->use_geo_mode()){die('error: function geo_dist should not be used if vertices are not defined by latitudes and longitudes (geomode)');}
		$a = 6378137;
		$b = 6356752.3142;
		$f = 1/298.257223563;  // WGS-84 ellipsiod
		$L = deg2rad($lon2-$lon1);
		$U1 = atan((1-$f) * tan(deg2rad($lat1)));
		$U2 = atan((1-$f) * tan(deg2rad($lat2)));
		$sinU1 = sin($U1);
		$cosU1 = cos($U1);
		$sinU2 = sin($U2);
		$cosU2 = cos($U2);
	  
		$lambda = $L;
		$lambdaP = 2*M_PI;
		$iterLimit = 20;
		while (abs($lambda-$lambdaP) > 1e-12 && --$iterLimit>0) {
			$sinLambda = sin($lambda);
			$cosLambda = cos($lambda);
			$sinSigma = sqrt(($cosU2*$sinLambda) * ($cosU2*$sinLambda) + ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda) * ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda));
			if ($sinSigma==0) return 0;  // co-incident points
			$cosSigma = $sinU1*$sinU2 + $cosU1*$cosU2*$cosLambda;
			$sigma = atan2($sinSigma, $cosSigma);
			$sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
			$cosSqAlpha = 1 - $sinAlpha*$sinAlpha;
			$cos2SigmaM = $cosSigma - 2*$sinU1*$sinU2/$cosSqAlpha;
			if (is_numeric($cos2SigmaM)) $cos2SigmaM = 0;  // equatorial line: cosSqAlpha=0 (§6)
			$C = $f/16*$cosSqAlpha*(4+$f*(4-3*$cosSqAlpha));
			$lambdaP = $lambda;
			$lambda = $L + (1-$C) * $f * $sinAlpha * ($sigma + $C*$sinSigma*($cos2SigmaM+$C*$cosSigma*(-1+2*$cos2SigmaM*$cos2SigmaM)));
		}
		if ($iterLimit==0){ return NULL; }	// formula failed to converge
	
		$uSq = $cosSqAlpha * ($a*$a - $b*$b) / ($b*$b);
		$A = 1 + $uSq/16384*(4096+$uSq*(-768+$uSq*(320-175*$uSq)));
		$B = $uSq/1024 * (256+$uSq*(-128+$uSq*(74-47*$uSq)));
		$deltaSigma = $B*$sinSigma*($cos2SigmaM+$B/4*($cosSigma*(-1+2*$cos2SigmaM*$cos2SigmaM)-
			$B/6*$cos2SigmaM*(-3+4*$sinSigma*$sinSigma)*(-3+4*$cos2SigmaM*$cos2SigmaM)));
		$s = $b*$A*($sigma-$deltaSigma);
		
		$s = round($s,3); // round to 1mm precision
		return $s;
	}
	/*
	** Return the distance between two points on the Globe
	** This calculates the length of the Great Circle between the 2 points
	** It uses the mean radius of the earth and considders the globe to be 
	** a perfect sphere instead of an ellipsoid. Therefor the result can be
	** off as much as 0.3%, especially around the poles.
	** For better accuracy, use the geo_dist function.
	*/
	function geo_dist_mean_radius($lng1, $lat1, $lng2, $lat2){
		// check if polygon is in geo mode
		if(!$this->use_geo_mode()){die('error: function geo_dist_mean_radius should not be used if vertices are not defined by latitudes and longitudes (geomode)');}
		$r = 6372797;	// mean radius of Earth in meters
		$pi80 = M_PI / 180;
		$lat1 *= $pi80;
		$lng1 *= $pi80;
		$lat2 *= $pi80;
		$lng2 *= $pi80;
		$dlat = $lat2 - $lat1;
		$dlng = $lng2 - $lng1;
		$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$distance = $r * $c;
		
		return $distance; // in meters
	}
	/*
	** Get shortest distance from this polygon's border (vertex or segment) to a given vertex
	** Both geomode and normal (2D) mode are supported
	*/
	function distance2point(&$vertex){
		$distance = infinity; // start very far away
		// loop all vertices of polygon
		$va =& $this->first; 	// first vertex of first segment to check
		$vb =& $va->Next();		// second vertex of segment
		do{
			// get the distance of given vertex to current line segment
			$dist = $this->dist_line2point($va->X(),$va->Y(),$vb->X(),$vb->Y(),$vertex->X(),$vertex->Y());
			// save it if it is shortest distance so far
			$distance = ($dist<$distance)?$dist:$distance;
			// set next line segments' vertices
			$va =& $va->Next();
			$vb =& $va->Next();
		}while ($vb->id() != $this->first->id());
		// return distance if it was resolved, otherwise return null
		return ($distance<infinity)?$distance:NULL;	 // if in geomode, distance is in meters
	}
	/*
	** Get shortest distance from point C to line (segment) AB
	** Translated to php from the algorithms found on:
	** http://www.topcoder.com/tc?module=Static&d1=tutorials&d2=geometry1#line_point_distance
	*/
	function dist_line2point($xa,$ya,$xb,$yb,$xc,$yc, $isSegment=TRUE){
		// get distance from point A to B
		$distAB = $this->distance($xa,$ya,$xb,$yb); // distance function switches between polygon modes (geo/2d)
		// check if point A and B are not the same
		if($distAB == 0){
			// point A == point B
		    if($isSegment){ 
				// get distance from point A to C
				$dist = $this->distance($xa,$ya,$xc,$yc);
			}else{
				// AB is not a line, throw php error to easily locate the problem
				echo('error: function distance() - point A and B are the same, so no line is defined;'); $error = 0/0; exit;
			}
		}else{
			// Get distance from point C to line AB (length of perpendicular from AB to C)
			// in other words: we need the height of triangle ABC with base AB
			// We use this basic geometry rule for triangles: area = base * height / 2
			// so, the distance from C to line AB is given by: height = 2 * area / base
			// Take the 'cross product' of points ABC, which gives twice the area of triangle ABC
			// devide this by the distance from A to B, to get the distance from AB to C
	    	$cross = $this->cross($xa,$ya,$xb,$yb,$xc,$yc);
			$dist = $cross / $distAB;
			// If AB should not be interpreted as a 'line' but as a 'line segment' (as in polygons),
			// the just used perpendicular of AB to C (height of triangle) could intersect AB beyond the 
			// start (A) or end (B) of segment AB, in which case the distance from point C to segment AB 
			// is given by the distance from point C to the start/end of the segment.
			// Treat these special cases:
		    if($isSegment){ 
				// Check if the perpendicular line from C to AB intersects line AB beyond point A or point B
				// The 'dot product' of BAC is greater than 0 if the perpendicular intersects BA beyond point A
				// The 'dot product' of ABC is greater than 0 if the perpendicular intersects AB beyond point B
				if($this->dot($xb,$yb,$xa,$ya,$xc,$yc) > 0){
					// intersect beyond point A, get distance from point C to point A
					$dist = $this->distance($xa,$ya,$xc,$yc);
				}elseif($this->dot($xa,$ya,$xb,$yb,$xc,$yc) > 0){
					// intersect beyond point B, get distance from point C to point B
					$dist = $this->distance($xb,$yb,$xc,$yc);
				}
		    }
			// distance could be negative, depending on the direction of the used vectors
			$dist = abs($dist);
		}
		return $dist; // if in GEOmode, distance is in meters
	}
	/* 
	** Compute the dot product AB . BC (line segment AB with line segment BC)
	** Translated to php from the algorithms found on:
	** http://www.topcoder.com/tc?module=Static&d1=tutorials&d2=geometry1#line_point_distance
	*/
	function dot($Xa,$Ya,$Xb,$Yb,$Xc,$Yc){
		// translate coordinates to vectors
		// the vector A->B is defined as [Xb-Xa,Yb-Ya]
		// in other words: the difference between the two points in both axal directions
		if($this->use_geo_mode()){
			// Coordinates given in latitude/longitude cannot be substracted from eachother
			// Work around: project 2 extra points in both axal directions so they form 
			// a 'rectangle' so that original points are situated diagonally
			// Then, get the width and height of this rectangle
			$ABx = $this->distance($Xa,$Ya,$Xb,$Ya);
			$ABy = $this->distance($Xa,$Ya,$Xa,$Yb);
			$BCx = $this->distance($Xb,$Yb,$Xc,$Yb);
			$BCy = $this->distance($Xb,$Yb,$Xb,$Yc);
			// Vectors can have positive and negative values for the x and y direction,
			// depending on the direction in which they point. The calculated distances
			// are always positive, so they should be reset to reflect their direction.
			// Flip the direction if it needs to be flipped
			$ABx *= ($Xb<$Xa)?-1:1;
			$ABy *= ($Yb<$Ya)?-1:1;
			$BCx *= ($Xc<$Xb)?-1:1;
			$BCy *= ($Yc<$Yb)?-1:1;
			// If one of the two points has a negative longitude and the points are more dan 180 degrees appart, 
			// the Great-Circle-Line (shortest distance) will pass the -180|+180 meredian (backside of the earth).
			// In this case B might have a lower longitude than A but the direction of the vector should be positive (i.e. the earth is round).
			if($Xa>0 && $Xb<0 && $Xa+abs($Xb)>180){ $ABx = abs($ABx);}
			if($Xb>0 && $Xc<0 && $Xb+abs($Xc)>180){ $BCx = abs($BCx);}
		}else{
			// in a 2D plane, coordinates can simply be substracted to get vectors
			$ABx = $Xb - $Xa;
			$ABy = $Yb - $Ya;
			$BCx = $Xc - $Xb;
			$BCy = $Yc - $Yb;
		}
		// calculate dot product by multiplying vectors
		$dot = $ABx * $BCx + $ABy * $BCy;
		return $dot;
	}

	/* 
	** Compute the cross product AB x AC (line segment AB with line segment BC)
	** Translated to php from the algorithms found on:
	** http://www.topcoder.com/tc?module=Static&d1=tutorials&d2=geometry1#line_point_distance
	*/
	function cross($Xa,$Ya,$Xb,$Yb,$Xc,$Yc){
		// translate coordinates to vectors
		// the vector A->B is defined as [Xb-Xa,Yb-Ya]
		// in other words: the difference between the two points in both axal directions
		if($this->use_geo_mode()){
			// Coordinates given in latitude/longitude cannot be substracted from eachother
			// Work around: project 2 extra points in both axal directions so they form 
			// a 'rectangle' where the original points are situated diagonally
			// Then, get the distance of the width and height of this rectangle
			$ABx = $this->distance($Xa,$Ya,$Xb,$Ya);
			$ABy = $this->distance($Xa,$Ya,$Xa,$Yb);
			$ACx = $this->distance($Xa,$Ya,$Xc,$Ya);
			$ACy = $this->distance($Xa,$Ya,$Xa,$Yc);
			// Vectors can have positive and negative values for the x and y direction,
			// depending on the direction in which they point. The calculated distances
			// are always positive, so they should be reset to reflect their direction.
			// Flip the direction if it needs to be flipped
			$ABx *= ($Xb<$Xa)?-1:1;
			$ABy *= ($Yb<$Ya)?-1:1;
			$ACx *= ($Xc<$Xa)?-1:1;
			$ACy *= ($Yc<$Ya)?-1:1;
			// If one of the two points has a negative longitude and the points are more dan 180 degrees appart, 
			// the Great-Circle-Line (shortest distance) will pass the -180|+180 meredian (backside of the earth).
			// In this case B might have a lower longitude than A but the direction of the vector should be positive (i.e. the earth is round).
			if($Xa>0 && $Xb<0 && $Xa+abs($Xb)>180){ $ABx = abs($ABx);}
			if($Xb>0 && $Xc<0 && $Xb+abs($Xc)>180){ $BCx = abs($BCx);}
		}else{
			// in a 2D plane, coordinates can simply be substracted to get vectors
			$ABx = $Xb - $Xa;
			$ABy = $Yb - $Ya;
			$ACx = $Xc - $Xa;
			$ACy = $Yc - $Ya;
		}
		// calculate the cross product
		$cross = $ABx * $ACy - $ABy * $ACx;
		return $cross;
	}
	/*
	 * calculate initial bearing between two points
	 * Addapted from: http://www.movable-type.co.uk/scripts/latlong.html
	 * On its turn addapted from: Ed Williams' Aviation Formulary, http://williams.best.vwh.net/avform.htm#Crs
	 * This is the initial bearing which if followed in a straight line along a great-circle arc (orthodrome) 
	 * will take you from the start point to the end point; in general, the bearing you are following will 
	 * have varied by the time you get to the end point (if you were to go from say 35°N,45°E (Baghdad) to 
	 * 35°N,135°E (Osaka), you would start on a bearing of 60° and end up on a bearing of 120°!).
	 */
	function initial_bearing($lon1, $lat1, $lon2, $lat2) {
		$lat1 = deg2rad($lat1);
		$lat2 = deg2rad($lat2);
		$dLon = deg2rad(($lon2-$lon1));
		$y = sin($dLon) * cos($lat2);
		$x = cos($lat1)*sin($lat2) - sin($lat1)*cos($lat2)*cos($dLon);
		return (rad2deg(atan2($y, $x))+360) % 360;
	}
	/*
	** For final bearing, take the initial bearing from the end point to the start point and reverse it (using ? = (?+180) % 360).
	*/
	function final_bearing($lon1, $lat1, $lon2, $lat2) {
		$reverse_inital_bearing = $this->initial_bearing($lon2, $lat2, $lon1, $lat1);
		return ($reverse_inital_bearing+180) % 360;
	}

}
?>