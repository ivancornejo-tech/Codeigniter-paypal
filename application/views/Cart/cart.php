<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container py-3">
		<div class="row">
			<div class="col-sm-8">
				<h3>Carrito de compras</h3>
			</div>
			<div class="col-sm-4">
				<?php echo anchor('/planes', 'Seguir comprando'); ?>
			</div>
		</div>
		<hr>
		<div class="row">
			<?php if ($this->cart->total_items()>0): ?>
				<div class="col-12 col-sm-6 col-md-8">
					<table class="table table-responsive">
						<thead class="thead-dark">
							<tr>
								<th scope="col">#</th>
								<th scope="col">Cantidad</th>
								<th scope="col">Description</th>
								<th scope="col" style="text-align:right">Precio</th>
								<th scope="col" style="text-align:right">Sub-Total</th>
								<th scope="col"></th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1; ?>
							<?php 
							foreach ($this->cart->contents() as $items): ?>
								<tr>
									<form action="<?php base_url();?>cart/actualizar_carrito" method="post">
									<?php echo form_hidden($i.'[rowid]', $items['rowid']); ?>
										<th scope="row"><?php echo $i;?></th>
										<td><?php echo form_input(array('name' => $i.'[qty]', 'value' => $items['qty'], 'maxlength' => '2', 'size' => '5', 'type' => 'text')); ?></td>
										<td>
											<?php echo $items['name']; ?>
											<?php if ($this->cart->has_options($items['rowid']) == TRUE): ?>
												<p>
													<?php foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value): ?>
														<strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br />
													<?php endforeach; ?>
												</p>
											<?php endif; ?>
										</td>
										<td style="text-align:right">$<?php echo $this->cart->format_number($items['price']); ?></td>
										<td style="text-align:right">$<?php echo $this->cart->format_number($items['subtotal']); ?></td>
										<td><?php echo form_submit('', 'Actualizar', array('type'=>'button', 'class'=>'btn btn-light')); ?></td>
										<td><?php //echo anchor('Cart/quitar_producto', 'Quitar'); ?>
											<a type="button" class="btn btn-danger" href="<?php echo base_url()?>Cart/quitar_producto/<?php echo $items['rowid'];?>"><i class="fas fa-trash-alt"></i></a> 
										</td>
									</form>
								</tr>
								<?php $i++; ?>
							<?php endforeach; ?>
							<tr>
								<td colspan="4"> </td>
								<!-- <td class="right"><strong>Total</strong></td> -->
								<td class="right">$<?php echo $this->cart->format_number($this->cart->total()); ?></td>
							</tr>
						</tbody>
					</table>
					<p>
						<?php echo anchor('cart/vaciar_carrito', 'Vaciar carrito', array('type'=>'button', 'class'=>'btn btn-danger')); ?>
					</p>
				</div>
				<div class="col-6 col-md-4">
					<div class="card" style="width: 18rem;">
						<div class="card-body">
							<form action="" method="post">
								<h5 class="card-title">Resumen del pedido</h5>
								<table class="table">
									<tbody>
										<tr>
											<td colspan="2"><strong>Total: </strong></td>
											<td> $<?php echo $this->cart->format_number($this->cart->total()); ?></td>
										</tr>
										<tr>
											<td colspan="2"><strong>Artículos: </strong></td>
											<td> <?php echo $this->cart->format_number($this->cart->total_items()); ?></td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
						<div class="card-footer">
							<form method="post" id="paypalForm" class="form-horizontal" role="form" action="<?= base_url() ?>cart/create_payment_with_paypal">
								<div class="form-group">
									<div class="form-check">
										<label for="Normas">
											<input class="form-check-input" type="checkbox" name="Normas" id="Normas" value="" required>
											Acepto los <a href="<?php echo site_url('terminos_y_condiciones')?>" target="black">términos y condiciones</a>
										</label>
									</div>
								</div>
								<div class="form-group text-center">
									<input type="image" id="paypalB" src="https://www.paypalobjects.com/webstatic/es_MX/mktg/logos-buttons/redesign/btn_10.png" alt="PayPal">
								</div>
							</form>
						</div>
					</div>
				</div>
			<?php else: ?>
				<p>
					<h5>Tu carrito está vacío.</h5>
				</p>
			<?php endif; ?>
		</div>
</div>
