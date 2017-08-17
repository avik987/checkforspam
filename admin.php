<?php include "template_parts/main_header.php"; ?>
<?php

//var_dump('<pre>',get_user_dbl());die;
$dbl = [];
$listed = [];
foreach (get_user_dbl() as $val){
	$f = json_decode($val[0]);
	$d = json_decode($val[1]);

	foreach ($f as $key =>$dblName){

		$dbl['name'][] = $spam_blacklist_servers[$key];
		$dbl['status'][] = $dblName->status;
	}
	foreach ($d as $key =>$dbllist){

		//$listed['name'][] = $key;
		$listed[$key][] = $dbllist;
	}

}

//var_dump('<pre>', $listed);die;

?>
<div id="page-wrapper">
	<div class="container" style="width: 315px;	background: #fff; float: left">


		<table class="table table-bordered" style="margin-top: 18px;">
			<thead>
			<tr>
				<th>RBL</th>
				<th>Status</th>

			</tr>
			</thead>
			<tbody>
			<?php foreach ($dbl['name'] as $key =>$val): ?>
			<tr>

				<td><?= $val   ?></td>
				<td ><div style="background: <?php if($dbl['status'][$key] == 'listed') echo 'red'; else{echo 'green';} ?>; text-align: center; color: #fff; border-radius: 5px;"><?= $dbl['status'][$key]   ?></div></td>


			</tr>
			<?php endforeach; ?>


			</tbody>
		</table>
	</div>
	<div id="container" style="max-width: 800px; height: 400px; margin: 0 auto; float: left; margin-left: 50px;"></div>


	<table id="datatable" style="display: none;">
		<thead>
		<tr>
			<th></th>
			<th>DBL</th>
			<th>Date</th>
		</tr>
		</thead>
		<tbody>
		<?php  foreach($listed as $key => $val): ?>
		<tr>
			<th><?= $val[$key][0] ?></th>
			<td>3</td>
			<td>4</td>
		</tr>
		<?php endforeach; ?>

		</tbody>
		</table>
			<script>
		$(document).ready(function () {
			Highcharts.chart('container', {
				data: {
					table: 'datatable'
				},
				chart: {
					type: 'column'
				},
				title: {
					text: 'Data extracted from a HTML table in the page'
				},
				yAxis: {
					allowDecimals: false,
					title: {
						text: 'Units'
					}
				},
				tooltip: {
					formatter: function () {
						return '<b>' + this.series.name + '</b><br/>' +
							this.point.y + ' ' + this.point.name.toLowerCase();
					}
				}
			});
		});
	</script>
	</div>
	<!--footer section start-->
<?php include "template_parts/main_footer.php"; ?>

	<!--footer section end-->

	<!-- main content end-->
