<?php
session_start();

const DESTINATION_FOLDER = "./images/";

if( isset( $_POST[ "fileupload" ] ) ){
	if( isset( $_FILES[ "filename" ] ) ){
		$fileType = $_FILES[ "filename" ][ "type" ];
		
		if( $fileType == "image/jpeg" || $fileType == "image/png" || $fileType == "image/gif" ){
			$fileName = $_FILES[ "filename" ][ "name" ];
			$fileTempName = $_FILES[ "filename" ][ "tmp_name" ];
			
			echo "<p>Name: ".$fileName."</p>";
			echo "<p>Type: ".$fileType."</p>";
			echo "<p>Size in bytes: ".$_FILES[ "filename" ][ "size" ]."</p>";
			echo "<p>Temporary Name: ".$fileTempName."</p>";
			echo "<p>Error: ".$_FILES[ "filename" ][ "error" ]."</p>";
			
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
				echo "<p>File Upload unsuccessful.</p>";
			}
			
		}else{
			//header("Location: ./index.php");
		}
		
	}else{
		//header("Location: ./index.php");
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
	
	if( isset( $_POST[ "orientation" ] ) && isset( $_POST[ "dimension" ] ) ){		
		$dimension = explode( "d", $_POST[ "dimension" ] );
		$cropSize = $dimension[1];
		
		$resultImage = ImageCreateTrueColor( $cropSize, $cropSize );
		
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
		//unset( $_SESSION[ "imageFile" ] );
		
		header( "Location: ./index.php" );
	}else{
		//header( "Location: ./index.php" );
	}
}
?>