// JavaScript Document

var imageContainer = document.getElementById( 'imageContainer' );

if( imageContainer ){
	var $sourceContainer = $( '#imageContainer' );
	var $sourceImage = $sourceContainer.children( 'img' );
	var $imageWidth = $sourceImage.width();
	var $imageHeight = $sourceImage.height();
	
	if( $imageWidth < 672 ){
		$sourceContainer.css({
			'position': 'absolute',
			'width': $imageWidth,
			'height': $imageHeight,
			'top': '50%',
			'left': '50%',
			'margin-top': -$imageHeight / 2,
			'margin-left': -$imageWidth / 2
		});
	}
}