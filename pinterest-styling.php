<style type="text/css">
.pinterest-widget ul { margin: 0; list-style:none;}
.pinterest-widget div.imgcontainer {overflow:hidden; width: 100%; height: 100px;}
.pinterest-widget div.imgcontainer img {position:relative; top:-10%; left:0%; min-width:100%; min-height:100%;}
.pinterest-widget li {clear:none; border:none; padding:0px; margin:0px;}
<?php if($responsive_fixed == 'Yes') { ?>
.pinterest-widget li {float:left; width: 23%; margin-right: 2%;}
<?php 
	} else { 
	
		if($width != '') {
			echo '.pinterest-widget li {float:left;';
			echo ' width:' . $width . 'px;';
			echo 'margin-right: 2%;}'; 
		}  else { 
			echo '.pinterest-widget li {float:left; width:100%; margin-right: 2%;}';
			
		}  
		echo '.pinterest-widget div.imgcontainer img {left:-15%!important;}';
	?>
<?php 
	}
?>

@media screen and (max-width: 980px) {
	/*Enter your own styling here if needed*/
}

@media screen and (max-width: 760px) {
    .pinterest-widget li { width: 45%; margin-right: 5%; margin-bottom: 5%;}
}

@media screen and (max-width: 480px) {
	/*Enter your own styling here if needed*/
}
</style>