<?php

class ControllerExtensionFeedGssApi extends Controller {

	private $debugIt = false;
	
	public function version() {
		echo("3.1.1");
	}
	
	/*
	 * Get orders
	 */

	public function orders() {
		$this->checkPlugin();

		$orderData['orders'] = array();

		
		if (isset($this->request->get['pageSize']) && !empty($this->request->get['pageSize']) && ctype_digit($this->request->get['pageSize'])) {
			$pageSize = $this->request->get['pageSize'];
		} else {
			$pageSize = 100;
		}
		
		if (isset($this->request->get['page']) && !empty($this->request->get['page']) && ctype_digit($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['dateTime']) && !empty($this->request->get['dateTime']) ) {
			$DateTime = $this->request->get['dateTime'];
		} else {
			$DateTime = '';
		}

		if (isset($this->request->get['status']) && !empty($this->request->get['status']) ) {
			$statusids = $this->request->get['status'];
		} else {
			$statusids = '';
		}

		$this->load->model('account/gss_order');

		$filter_data = array(
			'statusids' => $statusids,
			'DateTime'   => $DateTime,
			'start'                  => ($page - 1) * $pageSize,
			'limit'                  => $pageSize
		);
		
		$results = $this->model_account_gss_order->getOrderByStatusId($filter_data);

		if ($this->debugIt) {
			echo '<pre>';
			print_r($results);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($results));
		}
	}

	public function UpdateOrder() {
		$this->checkPlugin();

		$orderData['orders'] = array();

		
		if (isset($this->request->get['orderId']) && !empty($this->request->get['orderId'])  && ctype_digit($this->request->get['orderId'])) {
			$OrderId = $this->request->get['orderId'];
		} else {
			$OrderId = '';
		}
		
		if (isset($this->request->get['statusId']) && !empty($this->request->get['statusId'])  && ctype_digit($this->request->get['statusId'])) {
			$StatusId = $this->request->get['StatusId'];
		} else {
			$StatusId = 0;
		}
		
		if (isset($this->request->get['trackingNumber']) && !empty($this->request->get['trackingNumber']) ) {
			$TrackingNumber = $this->request->get['trackingNumber'];
		} else {
			$TrackingNumber = '';
		}

		

		$this->load->model('checkout/order');

		 

		$this->model_checkout_order->addOrderHistory($OrderId,$StatusId,'Tracking Link: ' . $TrackingNumber,false,false);
		$json = array("OrderId" => $OrderId, "Status" => $StatusId, "Tracking" => $TrackingNumber);
		
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	protected function checkPlugin() {

		$json = array("success" => false);

		/* check rest api is enabled */
		if (!$this->config->get('feed_gss_api_status')) {
			$json["error"] = 'API is disabled. Enable it!';
		}

		/* validate api security key */
		if ($this->config->get('feed_gss_api_key') && (!isset($this->request->get['key']) || $this->request->get['key'] != $this->config->get('feed_gss_api_key'))) {
			$json["error"] = 'Invalid secret key';
		}

		if (isset($json["error"])) {
			$this->response->addHeader('Content-Type: application/json');
			echo(json_encode($json));
			exit;
		} else {
			$json = array("success" => true);
			$this->response->setOutput(json_encode($json));
		}
	}

}
