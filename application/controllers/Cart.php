<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */

use PayPal\Api\ItemList;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;

class Cart extends CI_Controller
{
	public $_api_context;

	public function __construct()
    {
	  parent::__construct();
	  $this->load->helper('form');

	  /**
	   * Paypal configuración  
	   */

	  $this->load->model('Paypal_model');
        // paypal credentials
        $this->config->load('Paypal');

        $this->_api_context = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
				$this->config->item('client_id'), 
				$this->config->item('secret')
            )
        );
	}

	public function index(){
		$datos['titulo'] = 'Lista de productos';
		$datos['contenido'] = 'carrito';
		$this->layout->view("cart", $datos);
	}
	
	public function agregar_carrito(){
		if($this->input->post()){
			$data = array(
				'id'      => $this->input->post('idProducto'),
				'qty'     => 1,
				'price'   => $this->input->post('Costo'),
				'name'    => $this->input->post('NombreProducto'),
				'options' => array('Equipos' => $this->input->post('cantidad_Equipos'))
			);
			$this->cart->insert($data);
			redirect('cart');
		}
	}

	public function mostrar_carrito(){
		$datos['titulo'] = 'Lista de productos';
		$datos['contenido'] = 'carrito';
		$this->layout->view('cart', $datos);
	}
	
	public function vaciar_carrito($redirect = true){
		$this->cart->destroy();
		$this->session->set_userdata('carrito', array());
		if($redirect){
			redirect('cart');
		}
	}

	public function quitar_producto($indice){
		$data = array(
			'rowid' => $indice,
			'qty'   => 0
		);
		$this->cart->update($data);
		redirect('cart');
	}

	public  function actualizar_carrito(){
		if($this->input->post()){
			$data =$this->input->post();
			$this->cart->update($data);
			redirect('cart');
		}
	}

	/**
	 * Paypal funciones y métodos
	 */

	public function create_payment_with_paypal()
    {

        // setup PayPal api context
        $this->_api_context->setConfig($this->config->item('settings'));


		// ### Payer
		/* Método de pago*/
        $payer['payment_method'] = 'paypal';

		
		// information
		/**
		 * Pasamos todos los items del carrito de compras
		 * y llenamos la descripción del pago 
		 */
		$i=0;
		$Subtotal=0;
		foreach ($this->cart->contents() as $items){
			$item_cart[$i]["name"] = $items['name'];
			foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value){
				$item_cart[$i]["description"] = 'Equipos: '.$option_value;
			}
			$item_cart[$i]["currency"] ="MXN";
			$item_cart[$i]["quantity"] = $items['qty'];
			$item_cart[$i]["price"] = $items['price'];
			$Subtotal += $items['price']; 
			$i++;
		}

        $itemList = new ItemList();
		$itemList->setItems($item_cart);
		
        //$details['tax'] = 0.0; //impuestos solo funciona con el tipo de pago paypal
        $details['subtotal'] = $this->cart->format_number($Subtotal);
		
		// ### Amount
        $amount['currency'] = "MXN";
        $amount['total'] = $this->cart->format_number($this->cart->total());;
        $amount['details'] = $details;
		
		// ### Transaction
		$transaction['reference_id'] = 'Smp-'.$this->_getIdPedido();
        $transaction['description'] ='SmartBusinessPOS Orden-'.$this->_getIdPedido();
        $transaction['amount'] = $amount;
        $transaction['invoice_number'] = uniqid();
		$transaction['item_list'] = $itemList;

		//print("<pre>".print_r($transaction,true)."</pre>");

		// ### Redirect urls
		/**
		 * llamado al método getPaymentStatus
		 * que determina al estatus de la transacción
		 * y generar la redirección alas paginas
		 */
        $baseUrl = base_url();
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($baseUrl."cart/getPaymentStatus")
            ->setCancelUrl($baseUrl."cart/getPaymentStatus");

		// ### Payment
		// A Payment Resource; create one using
		// the above types and intent set to sale 'sale'
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->_api_context);
        } catch (Exception $ex) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $ex);
            exit(1);
        }
        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        if(isset($redirect_url)) {
            /** redirect to paypal **/
            redirect($redirect_url);
        }

        $this->session->set_flashdata('success_msg','Unknown error occurred');
        redirect('cart');

    }


    public function getPaymentStatus()
    {

        // paypal credentials

        /** Obtener la identificación de pagor antes de limpiarla **/
        $payment_id = $this->input->get("paymentId") ;
        $PayerID = $this->input->get("PayerID") ;
        $token = $this->input->get("token") ;
        /** clear the session payment ID **/

        if (empty($PayerID) || empty($token)) {
            $this->session->set_flashdata('success_msg','Payment failed');
            redirect('cart');
        }

        $payment = Payment::get($payment_id,$this->_api_context);


        /** PaymentExecution object includes information necessary **/
        /** to execute a PayPal account payment. **/
        /** The payer_id is added to the request query parameters **/
        /** when the user is redirected from paypal back to your site **/
        $execution = new PaymentExecution();
        $execution->setPayerId($this->input->get('PayerID'));

        /**Execute the payment **/
        $result = $payment->execute($execution,$this->_api_context);

        //  DEBUG RESULT, remove it later **/
        if ($result->getState() == 'approved') {
            $trans = $result->getTransactions();

            // item info
            $Subtotal = $trans[0]->getAmount()->getDetails()->getSubtotal();
            //$Tax = $trans[0]->getAmount()->getDetails()->getTax();

            $payer = $result->getPayer();
            // payer infitemListo //
            $PaymentMethod =$payer->getPaymentMethod();
            $PayerStatus =$payer->getStatus();
            $PayerMail =$payer->getPayerInfo()->getEmail();

            $relatedResources = $trans[0]->getRelatedResources();
            $sale = $relatedResources[0]->getSale();
            // sale info //
            $saleId = $sale->getId();
            $CreateTime = $sale->getCreateTime();
            $UpdateTime = $sale->getUpdateTime();
            $State = $sale->getState();
			$Total = $sale->getAmount()->getTotal();
			
			/** Arreglo para enviar la información a ala BD**/
			
			$paypalArray = array('Total' => $Total, 
			'SubTotal' => $Subtotal, 
			'PaymentMethod' => $PaymentMethod, 
			'PayerStatus' => $PayerStatus, 
			'PayerMail' => $PayerMail, 
			'SaleId' => $saleId, 
			'CreateTime' => $CreateTime,
			'UpdateTime' => $UpdateTime, 
			'Payment_state' => $State);
			$idPay = $this->Paypal_model->create($paypalArray);
			$idVenta = $this->guardarVenta($idPay);
			$this->guardarProductosVendidos($idVenta);
			$this->session->set_flashdata('success_msg','Payment success');
			$setID = $this->_securityUrl($idVenta, 'encode');
			redirect('cart/success/'.$setID);
			
        }
        $this->session->set_flashdata('success_msg','Payment failed');
        redirect('cart/cancel');
	}

	private function _securityUrl($data =  null, $action = 'encode'){
		if(!is_null($data)){
			$this->load->library('encrypt');
			switch ($action) {
				case 'encode':
					$enc_data=$this->encrypt->encode($data);
					$enc_data=str_replace(array('+', '/', '='), array('-', '_', '~'), $enc_data);
					return $enc_data;
					break;
				case 'decode':
					$dec_data=str_replace(array('-', '_', '~'), array('+', '/', '='), $data);
					$dec_data=$this->encrypt->decode($dec_data);
					return $dec_data;
					break;
				default:
					return '';
					break;
			}
		}
		return null;
	}
	
    public function success($idVenta = null){
		if(is_null($idVenta)){
			show_404();
		}
		$getID = ($this->_securityUrl($idVenta, 'decode'));
		$data['PaypalArray'] = $this->Paypal_model->getPaypalPayment($getID);
		if(!is_null($data['PaypalArray']) && count($data['PaypalArray'])>0){
			$contenido['paypal'] = $this->load->view('Cart/success', $data, true);
			$this->load->view("Cart/pagos", $contenido);
		}else{
			show_404();
		}
		//unset($getID, $data);
	}
	
    public function cancel(){
		//$this->Paypal_model->create();
		$contenido['paypal'] = $this->load->view('Cart/cancel', '', true);
        $this->load->view("Cart/pagos", $contenido);
	}
	
	private function _getIdPedido(){
		return $this->Paypal_model->getID();
	}

	private function guardarVenta($idPay){
		$venta = array('idPayment' => $idPay);
		$idVenta = $this->Paypal_model->insertarVenta($venta);
		return $idVenta;
	}

	private function guardarProductosVendidos($idVenta){
		$i=0;
		foreach ($this->cart->contents() as $items){
			$item_cart[$i]['idProductos'] = $items['id'];
			$item_cart[$i]["Cantidad"] = $items['qty'];
			foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value){
				$item_cart[$i]['Equipos'] = $option_value;
			}
			$item_cart[$i]["Precio"] = $items['price'];
			$item_cart[$i]["idVentas"] = $idVenta;
			$i++;
		}
		$this->Paypal_model->insertarProductosVendidos($item_cart);
		$this->vaciar_carrito(false);
		return $item_cart;
	}


}
