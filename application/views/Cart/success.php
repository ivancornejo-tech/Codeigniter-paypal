<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Smart Busniness</title>
	<!-- <script src="<?php echo base_url(); ?>web/js/jquery/jquery-3.3.1.min.js"></script> -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <link rel="stylesheet" href="<?php echo base_url(); ?>web/css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link  rel="stylesheet" href="<?php echo base_url(); ?>web/css/fonts.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>web/css/style.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>web/css/estilos-impresion.css" media="print" />
    <link rel='stylesheet' type="text/css" href="<?php echo base_url(); ?>web/fontawesome-free-5.5.0-web/css/all.min.css">
	<link rel="shortcut icon" href="">
	<!-- development version, includes helpful console warnings -->

	<script>var base_url = '<?php echo base_url(); ?>';</script>
</head>
<body>

	<div class="container py-5">
		<div class="row">
			<div class="col-md-4">
				<img class="img-fluid d-block" src="<?php echo base_url();?>web/img/logo.png">
			</div>
        	<div class="col-md-8"><h2>SmartBusinesPOS</h2></div>
			<hr>
			<?php //print_r($PaypalArray);
			if(count($PaypalArray)>0):
				foreach($PaypalArray as $item): ?>
				<div class="col-md-12 text-left py-1">
					<strong>No. Pedido: <?php echo $item['idVentas']; ?></strong>
				</div>
				<div class="col-md-12 text-left py-1">
					<?php echo $item['CreateTime']; ?>
				</div>
				<hr>
				<div class="col-md-12 text-left py-1">
					<h2>Estimado usuario</h2>
					<span>Su pago fue exitoso, gracias por la compra.</span>
				</div>
				<div class="col-md-12 text-left py-1">
					<p class="">
						Hemos enviado un correo con los datos necesarios para usar SmartBusinesPOS
						al correo electonico <strong><?php echo $item['PayerMail']; ?></strong>
					</p>
				</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

	</div><!-- /.container -->

	<script src="<?php echo base_url(); ?>web/js/popper.min.js"></script>
    <script src="<?php echo base_url(); ?>web/css/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="<?php echo base_url(); ?>web/css/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
	
</body>
</html>
