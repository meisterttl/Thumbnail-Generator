<?php
session_start();

// Defining constants
const DESTINATION_FOLDER = "./images/";
?>

<?php
// Defining a class for source image process
class ImageProcess{
	public $imageType;
	private $imageName;
	private $imageTempName;
	private $fileSize;
	public $saveResult;
	
	public function setImageType( $imageFile ){
		$this->imageType = $imageFile[ "type" ];
	}
	
	public function setImageName( $imageFile ){
		$this->imageName = $imageFile[ "name" ];
	}
	
	public function setTempName( $imageFile){
		$this->imageTempName = $imageFile[ "tmp_name" ];
	}
	
	public function storeSourceImage(){
		$this->saveResult = move_uploaded_file( $this->imageTempName , DESTINATION_FOLDER . $this->imageName );
	}
	
	public function getSourceData(){
		$this->fileSize = getimagesize( DESTINATION_FOLDER . $this->imageName );
		$_SESSION[ "imageFile" ] = DESTINATION_FOLDER . $this->imageName;
		$_SESSION[ "imageWidth" ] = $this->fileSize[0];
		$_SESSION[ "imageHeight" ] = $this->fileSize[1];
		$_SESSION[ "fileType" ] = $this->imageType;
	}
}

$processImage = new ImageProcess();
?>

<?php
// Getting the image file, validating and temporarily storing it.
if( isset( $_POST[ "fileupload" ] ) ){
	$file = $_FILES[ "filename" ];
	
	if( isset( $file ) ){
		$processImage->setImageType( $file );
		
		if( $processImage->imageType == "image/jpeg" || $processImage->imageType == "image/png" || $processImage->imageType == "image/gif" ){
			$processImage->setImageName( $file );
			$processImage->setTempName( $file );
			$processImage->storeSourceImage();
			
			if( $processImage->saveResult == true ){
				$processImage->getSourceData();
			}else{
				$_SESSION[ "errorMessage" ] = "<p>There has been an error while processing the file. Please try again!</p>";
			}
			
		}else{
			$_SESSION[ "errorMessage" ] = "<p>Please upload a valid file type such as JPEG, PNG or GIF!</p>";
		}
		
	}else{
		$_SESSION[ "errorMessage" ] = "<p>Please upload an image file to proceed!</p>";
	}

}
?>

<?php
// Processing the image and preparing for thumbnail generation
if( isset( $_POST[ "thumbgen" ] ) ){
	$targetImage = $_SESSION[ "imageFile" ];
	$imageType = $_SESSION[ "fileType" ];
	
	$targetPath = pathinfo( $targetImage );
	$sourceName = $targetPath[ "filename" ];
	
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
			if( $transparency == "yes" && imageType == "image/png" ){
				imagealphablending( $resultImage, false );
				imagesavealpha( $resultImage, true );
			}
		}
		
		$sourceWidth = imagesx( $sourceImage );
		$sourceHeight = imagesy( $sourceImage );
		$orientation = $_POST[ "orientation" ];
		
		$originPoint = thumbOrientation( $orientation, $sourceWidth, $sourceHeight );
		$originX = $originPoint[0];
		$originY = $originPoint[1];
		
		imagecopyresampled( $resultImage, $sourceImage, 0, 0, $originX, $originY, $cropSize, $cropSize, $cropSize, $cropSize );
	}

// Displaying the thumbnail
	$outputName = $sourceName . "-resized.";
	
	if( $imageType == "image/jpeg" ){
		$outputName .= "jpg";
		imagejpeg( $resultImage , $outputName , 100 );
	}else if( $imageType == "image/png" ){
		$outputName .= "png";
		imagepng( $resultImage , $outputName , 9 );
	}else if( $imageType == "image/gif" ){
		$outputName .= "gif";
		imagegif( $resultImage , $outputName );
	}

	$_SESSION[ "outputName" ] = $outputName;
	unset( $_SESSION[ "imageFile" ] );

}else{
	$_SESSION[ "errorMessage" ] = "<p>Please select the following parameters to generate a thumbnail!</p>";
}

header( "Location: ./index.php" );
?>

<?php
// Functions for processing the image
function thumbOrientation( $thumbOrigin, $thumbWidth, $thumbHeight ){
	$origin = explode( "-", $thumbOrigin );
	$vertical = $origin[0];
	$horizontal = $origin[1];
	
	if( $horizontal == "left" ){
		$valueX = 0;
	}else if( $horizontal == "center" ){
		$valueX = ( $thumbWidth - $cropSize ) / 2;
	}else{
		$valueX = $thumbWidth - $cropSize;
	}
	
	if( $vertical == "top" ){
		$valueY = 0;
	}else if( $vertical == "center" ){
		$valueY = ( $thumbHeight - $cropSize ) / 2;
	}else{
		$valueY = $thumbHeight - $cropSize;
	}
	
	return array( $valueX, $valueY );
}
?>