<?php
session_start();
?>
<!doctype html>
<html lang="en-us">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Thumbnail Generator</title>
	<link rel="stylesheet" type="text/css" href="styles/css-reset.css">
	<link rel="stylesheet" type="text/css" href="styles/styles.css">
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<div class="wrapper">
		<header>
			<h1>Thumbnail Generator</h1>
		</header>

		<main>
			<section>
				<?php
					if( isset( $_SESSION[ "errorMessage" ] ) ){
						$errorMessage = $_SESSION[ "errorMessage" ];
						echo $errorMessage;
						unset( $_SESSION[ "errorMessage" ] );
					}
				
					if( !isset( $_SESSION[ "outputName" ] ) ){
						if( !isset( $_SESSION[ "imageFile" ] ) ){
							?>
							<form enctype="multipart/form-data" action="./processing.php" method="post">
								<fieldset class="fileUpload">
									<legend>Select and upload an image (JPEG, PNG, and GIF) you wish to generate a thumbnail.</legend>
									<input type="hidden" name="fileupload">
									<input type="file" name="filename">
									<input type="submit" value="Submit">
								</fieldset>
							</form><?php
						}else{
							echo "<div><div class='image-container' id='imageContainer'><img src='".$_SESSION[ "imageFile" ]."'></div></div>";
							
							$imageWidth = $_SESSION['imageWidth'];
							$imageHeight = $_SESSION['imageHeight'];
							if( $imageWidth >= $imageHeight ){
								$numOption = floor( $imageWidth / 100 );
								$maxOption = $numOption * 100;
								if( $imageHeight <= $maxOption ){
									$numOption -= 1;
								}
								$maxDimension = $imageHeight;
							}else if( $imageWidth < $imageHeight) {
								$numOption = floor( $imageHeight / 100 );
								$maxOption = $numOption * 100;
								if( $imageWidth <= $maxOption ){
									$numOption -= 1;
								}
								$maxDimension = $imageWidth;
							}
							?>
							<div>
								<form action='./processing.php' method='post'>
									<input type='hidden' name='thumbgen'>
									<fieldset class="orientation">
										<legend>Orientation</legend>
										<input type='radio' name='orientation' value='topleft' id="topleft">
										<label for='topleft'>Top Left</label>
										<input type='radio' name='orientation' value='topcenter' id="topcenter">
										<label for='topcenter'>Top Center</label>
										<input type='radio' name='orientation' value='topright' id="topright">
										<label for='topright'>Top Right</label>
										<input type='radio' name='orientation' value='centerleft' id="centerleft">
										<label for='centerleft'>Center Left</label>
										<input type='radio' name='orientation' value='center' id="center">
										<label for='center'>Center</label>
										<input type='radio' name='orientation' value='centerright' id="centerright">
										<label for='centerright'>Center Right</label>
										<input type='radio' name='orientation' value='bottomleft' id="bottomleft">
										<label for='bottomleft'>Bottom Left</label>
										<input type='radio' name='orientation' value='bottomcenter' id="bottomcenter">
										<label for='bottomcenter'>Bottom Center</label>
										<input type='radio' name='orientation' value='bottomright' id="bottomright">
										<label for='bottomright'>Bottom Right</label>
									</fieldset>
							<?php
								$imageType = $_SESSION[ "fileType" ];
								if( $imageType == "image/png" || $imageType == "image/gif" ){
									?>
									<fieldset class="transparency">
										<legend>Keep the transparency?</legend>
										<input type="radio" name="transparency" value="yes" id="yes">
										<label for="yes">Yes</label>
										<input type="radio" name="transparency" value="no" id="no">
										<label for="no">No</label>
									</fieldset>
									<?php
								}
							?>
									<fieldset class="dimension">
										<legend>Dimension</legend>
							<?php
							echo "<input type='radio' name='dimension' value='d".$maxDimension."' id='d".$maxDimension."'>";
							echo "<label for='d".$maxDimension."'>".$maxDimension."x".$maxDimension."</label>";
							for( $i = $numOption; $i > 0; $i-- ){
								$dimension = $i*100;
								echo "<input type='radio' name='dimension' value='d".$dimension."' id='d".$dimension."'><label for='d".$dimension."'>".$dimension."x".$dimension."</label>";
							}
							echo "</fieldset><input type='submit' value='Submit' class='submitButton'></form></div>";
						}
					}else{
						echo "<div class='resizedImage'><p>Right click on the resized image, then press 'Save Image as...'</p>";
						echo "<img src='./" . $_SESSION[ "outputName" ] . "'></div>";
						unset( $_SESSION[ "outputName" ] );
					}
				?>
			</section>
		</main>
	</div>
<script src="scripts/jquery-1.11.2.min.js"></script>
<script src="scripts/scripts.js"></script>
</body>
</html>