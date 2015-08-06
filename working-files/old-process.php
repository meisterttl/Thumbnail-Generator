<?php
session_start();

const DESTINATION_FOLDER = "./images/";

if( isset( $_POST[ "fileupload" ] ) ){
	if( isset( $_FILES[ "filename" ] ) ){
		$fileType = $_FILES[ "filename" ][ "type" ];
		
		if( $fileType == "image/jpeg" || $fileType == "image/png" || $fileType == "image/gif" ){
			$fileName = $_FILES[ "filename" ][ "name" ];
			$fileTempName = $_FILES[ "filename" ][ "tmp_name" ];
			
			$result = move_uploaded_file( $_FILES[ "filename" ][ "tmp_name" ], DESTINATION_FOLDER . $fileName );
			
			if( $result == true ){
				echo "<p>File Upload successful.</p>";
				$fileSize = getimagesize( DESTINATION_FOLDER . $fileName );
				$_SESSION[ "imageFile" ] = DESTINATION_FOLDER . $fileName;
				$_SESSION[ "imageWidth" ] = $fileSize[0];
				$_SESSION[ "imageHeight" ] = $fileSize[1];
				$_SESSION[ "fileType" ] = $fileType;
				
				header( "Location: ./index.php" );
			}else{
				$_SESSION[ "errorMessage" ] = "<p>There has been an error while processing the file. Please try again!</p>";
				header("Location: ./index.php");
			}
			
		}else{
			$_SESSION[ "errorMessage" ] = "<p>Please upload a valid file type such as JPEG, PNG or GIF!</p>";
			header("Location: ./index.php");
		}
		
	}else{
		$_SESSION[ "errorMessage" ] = "<p>Please upload an image file to proceed!</p>";
		header("Location: ./index.php");
	}
}else if( isset( $_POST[ "thumbgen" ] ) ){
	$targetImage = $_SESSION[ "imageFile" ];
	$imageType = $_SESSION[ "fileType" ];
	
	$targetPath = pathinfo( $targetImage );
	$sourceName = $targetPath[ "filename" ];
	
	//header( "Content-Type: ".$imageType );
	if( $imageType == "image/jpeg" ){
		$sourceImage = imagecreatefromjpeg( $targetImage );
	}else if( $imageType == "image/png" ){
		$sourceImage = imagecreatefrompng( $targetImage );
	}else if( $imageType == "image/gif" ){
		$sourceImage = imagecreatefromgif( $targetImage );
	}
	
	if( isset( $_POST[ "transparency" ] ) ){
		$transparency = $_POST[ "transparency" ];
		if( $transparency == "yes" && $imageType == "image/png" ){
			imagealphablending( $sourceImage, true );
		}
	}
	
	if( isset( $_POST[ "orientation" ] ) && isset( $_POST[ "dimension" ] ) ){		
		$dimension = explode( "d", $_POST[ "dimension" ] );
		$cropSize = $dimension[1];
		
		$resultImage = ImageCreateTrueColor( $cropSize, $cropSize );
		
		if( isset( $_POST[ "transparency" ] ) ){
			$transparency = $_POST[ "transparency" ];
			if( $transparency == "yes" ){
				if( $imageType == "image/png" ){
					imagealphablending( $resultImage, false );
					imagesavealpha( $resultImage, true );
				}else if( $imageType == "image/gif" ){
					imagetruecolortopalette( $resultImage, true, 256 );
				}
			}
		}
		
		$sourceWidth = imagesx( $sourceImage );
		$sourceHeight = imagesy( $sourceImage );
		
		$orientation = $_POST[ "orientation" ];
		
		if( $orientation == "topleft" ){
			$originX = 0;
			$originY = 0;
		}else if( $orientation == "topcenter" ){
			$originX = ( $sourceWidth - $cropSize ) / 2;
			$originY = 0;
		}else if( $orientation == "topright" ){
			$originX = $sourceWidth - $cropSize;
			$originY = 0;
		}else if( $orientation == "centerleft" ){
			$originX = 0;
			$originY = ( $sourceHeight - $cropSize ) / 2;
		}else if( $orientation == "center" ){
			$originX = ( $sourceWidth - $cropSize ) / 2;
			$originY = ( $sourceHeight - $cropSize ) / 2;
		}else if( $orientation == "centerright" ){
			$originX = $sourceWidth - $cropSize;
			$originY = ( $sourceHeight - $cropSize ) / 2;
		}else if( $orientation == "bottomleft" ){
			$originX = 0;
			$originY = $sourceHeight - $cropSize;
		}else if( $orientation == "bottomcenter" ){
			$originX = ( $sourceWidth - $cropSize ) / 2;
			$originY = $sourceHeight - $cropSize;
		}else{
			$originX = $sourceWidth - $cropSize;
			$originY = $sourceHeight - $cropSize;
		}
		
		imagecopyresampled( $resultImage, $sourceImage, 0, 0, $originX, $originY, $cropSize, $cropSize, $cropSize, $cropSize );
		
		if( $imageType == "image/jpeg" ){
			$outputName = $sourceName . "-resized.jpg";
			imagejpeg( $resultImage, $outputName, 100 );
		}else if( $imageType == "image/png" ){
			$outputName = $sourceName . "-resized.png";
			imagepng( $resultImage, $outputName, 9 );
		}else if( $imageType == "image/gif" ){
			$outputName = $sourceName . "-resized.gif";
			imagegif( $resultImage, $outputName );
		}
		
		$_SESSION[ "outputName" ] = $outputName;
		unset( $_SESSION[ "imageFile" ] );
		
		header( "Location: ./index.php" );
	}else{
		$_SESSION[ "errorMessage" ] = "<p>Please select the following parameters to generate a thumbnail!</p>";
		header("Location: ./index.php");
	}
}
?>