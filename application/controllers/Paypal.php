<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use PayPal\Api\ItemList;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;

class Paypal extends CI_Controller
{
    public $_api_context;

    function  __construct()
    {
        parent::__construct();
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

    function index(){
        $this->load->view('Paypal/payment_credit_form');
    }


    function create_payment_with_paypal()
    {

        // setup PayPal api context
        $this->_api_context->setConfig($this->config->item('settings'));


        // ### Payer
        /* Método de pago*/
        $payer['payment_method'] = 'paypal';

        
        // information
        /**
         * 
         */

        $item1["name"] = $this->input->post('item_name');
        $item1["sku"] = $this->input->post('item_number');  // Similar to `item_number` in Classic API
        $item1["description"] = $this->input->post('item_description');
        $item1["currency"] ="MXN";
        $item1["quantity"] =1;
        $item1["price"] = $this->input->post('item_price');

        $itemList = new ItemList();
        $itemList->setItems(array($item1));
        
        $details['tax'] = 0.0; //impuestos solo funciona con el tipo de pago paypal
        $details['subtotal'] = $this->input->post('details_subtotal');
        
        // ### Amount
        $amount['currency'] = "MXN";
        $amount['total'] = $details['tax'] + $details['subtotal'];
        $amount['details'] = $details;
        
        // ### Transaction
        $transaction['reference_id'] = 'Smp-ejemplo';
        $transaction['description'] ='Orden-ejemplo';
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
        $redirectUrls->setReturnUrl($baseUrl."Paypal/getPaymentStatus")
            ->setCancelUrl($baseUrl."Paypal/getPaymentStatus");

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
        redirect('paypal/index');

    }


    public function getPaymentStatus()
    {

        // paypal credentials

        /** Get the payment ID before session clear **/
        $payment_id = $this->input->get("paymentId") ;
        $PayerID = $this->input->get("PayerID") ;
        $token = $this->input->get("token") ;
        /** clear the session payment ID **/

        if (empty($PayerID) || empty($token)) {
            $this->session->set_flashdata('success_msg','Payment failed');
            redirect('paypal/index');
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
            $this->session->set_flashdata('success_msg','Payment success');
            redirect('Paypal/success');
        }
        $this->session->set_flashdata('success_msg','Payment failed');
        redirect('Paypal/cancel');
    }
    
    public function success(){
        $this->load->view("Paypal/success");
    }
    
    public function cancel(){
        //$this->Paypal_model->create();
        $this->load->view("Paypal/cancel");
    }
    
}