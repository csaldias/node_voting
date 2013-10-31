<?php
$info=file_get_contents("info.txt");
$listam=intval(file_get_contents("listam.txt"));
$listaz=intval(file_get_contents("listaz.txt"));
$nulo=intval(file_get_contents("null.txt"));
$blanco=intval(file_get_contents("blanco.txt"));
?>

<!DOCTYPE html>
<html>
	<head>
		<script src="jquery.min.js"></script>
	</head>
	<body>

		<table>
									<tr>
										<td style="width:1px;">
										</td>
										<td style="">
											<b>SI</b>
										</td>
										<td>
											<input value="<?php echo $listam; ?>" type="number" id="listam" name="listam" min="0" style="width:80px;font-size:20pt;" value="0" onchange="update()" />
										</td>
									</tr>
									<tr>
										<td style="width:1px;">
										</td>
										<td style="">
											<b>NO</b>
										</td>
										<td>
											<input value="<?php echo $listaz; ?>" type="number" id="listaz" name="listaz" min="0" style="width:80px;font-size:20pt;" value="0" onchange="update()" />
										</td>
									</tr>
									<tr>
										<td style="width:1px;">
										</td>
										<td style="">
											<b>Blanco</b>
										</td>
										<td>
											<input value="<?php echo $blanco; ?>" type="number" id="blanco" name="blanco" min="0" style="width:80px;font-size:20pt;" value="0" onchange="update()" />
										</td>
									</tr>
									<tr>
										<td style="width:1px;">
										</td>
										<td style="">
											<b>Null</b>
										</td>
										<td>
											<input value="<?php echo $nulo; ?>" type="number" id="nulo" name="nulo" min="0" style="width:80px;font-size:20pt;" value="0" onchange="update()" />
										</td>
									</tr>
								</table>
								
								<textarea id="status" name="status" onchange="uptade();"><?php echo $info; ?></textarea>
								
	<script>
function update(){
	$.post('update.php',{'listam':$('#listam').val(),'listaz':$('#listaz').val(),'blanco':$('#blanco').val(),'nulo':$('#nulo').val(),'info':$('#status').val()},function(data){
		
	});
}
</script>
	</body>
</html>