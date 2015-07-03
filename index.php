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
	<header>
		<h1>Select and Upload an image you wish to generate a thumbnail</h1>
	</header>

	<main>
		<section>
			<?php
				if( !isset( $_SESSION[ "outputName" ] ) ){
					if( !isset( $_SESSION[ "imageFile" ] ) ){
						?>
						<form enctype="multipart/form-data" action="./processing.php" method="post">
							<input type="hidden" name="fileupload">
							<input type="file" name="filename">
							<input type="submit" value="Submit">
						</form><?php
					}else{
						echo "<div><img src='".$_SESSION[ "imageFile" ]."'></div>";
						
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
								<fieldset>
									<legend>Orientation</legend>
									<input type='radio' name='orientation' value='topleft'>
									<label for='topleft'>Top Left</label>
									<input type='radio' name='orientation' value='topcenter'>
									<label for='topcenter'>Top Center</label>
									<input type='radio' name='orientation' value='topright'>
									<label for='topright'>Top Right</label>
									<input type='radio' name='orientation' value='centerleft'>
									<label for='centerleft'>Center Left</label>
									<input type='radio' name='orientation' value='center'>
									<label for='center'>Center</label>
									<input type='radio' name='orientation' value='centerright'>
									<label for='centerright'>Center Right</label>
									<input type='radio' name='orientation' value='bottomleft'>
									<label for='bottomleft'>Bottom Left</label>
									<input type='radio' name='orientation' value='bottomcenter'>
									<label for='bottomcenter'>Bottom Center</label>
									<input type='radio' name='orientation' value='bottomright'>
									<label for='bottomright'>Bottom Right</label>
								</fieldset>
								<fieldset>
									<legend>Dimension</legend>
						<?php
						echo "<input type='radio' name='dimension' value='d".$maxDimension."'>";
						echo "<label for='d".$maxDimension."'>".$maxDimension."x".$maxDimension."</label>";
						for( $i = $numOption; $i > 0; $i-- ){
							$dimension = $i*100;
							echo "<input type='radio' name='dimension' value='d".$dimension."'><label for='d".$dimension."'>".$dimension."x".$dimension."</label>";
						}
						echo "</fieldset><input type='submit' value='Submit'></form></div>";
					}
				}else{
					echo "<img src='".$_SESSION[ "imageFile" ]."'>";
					echo "<img src='./" . $_SESSION[ "outputName" ] . "'>";
					unset( $_SESSION[ "imageFile" ] );
					unset( $_SESSION[ "outputName" ] );
				}
			?>
		</section>
	</main>
	
</body>
</html>