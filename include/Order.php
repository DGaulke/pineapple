<?php namespace pineapple;
/*
 *
 */
include_once('Controller.php');
include_once('Persistent.php');
include_once('Product.php');
include_once('Customer.php');

/* The Product class represents an item for sale by the bakery */
class Order extends Persistent {
	protected $orderId, $orderDate, $customer, $shipAddr1, $shipAddr2,
			$shipCity, $shipState, $shipZip, $nameOnCard, $cardType,
			$cardNumber, $expMonth, $expYear, $securityCode,
			$billAddr1, $billAddr2, $billCity, $billState, $billZip,
			$products = array();

	const shippingCost = 4.99;

	/* Constructor hidden to force use of static factory methods */
	private function __construct() {}

	/* Create a new Product object and validate */
	static function newOrder($data) {
		$order = new self();
		foreach ($data as $name => $value){
			$order->__set($name, $value);
		}
		if ($order->isValid()) {
			$order->updateInventory();
			$order->persist();
			$data = Controller::getInstance()->query("select @order_id");
			$order->orderId = $data->fetch_row()[0];
			foreach($order->products as $product){
				$parameters = "";
@				buildParameterList($parameters, $order->orderId, INTEGER);
				buildParameterList($parameters, $product->productId, INTEGER);
				buildParameterList($parameters, $product->price, STRING);
				buildParameterList($parameters, $product->purchasedQuantity, INTEGER);
				Controller::getInstance()->query("insert into order_detail ".
						"(order_id, product_id, current_product_price, ".
						"quantity) values($parameters)");
			}
			return $order;
		} else {
			throw new InvalidObjectException($order);
		}
	}
	/* Retrieve an array of all orders placed */
	static function allOrders(){
		$orders = parent::all(new self());
		foreach($orders as $order){
			$order->loadProducts();
		}
		return $orders;
	}
	/* Return an existing Order object for the given criteria */
	static function existingOrder($criteria){
		$order = new self();
		$order = $order->load($criteria);
		$order->loadProducts();
		return $order;
	}
	/* Provides access to protected attributes */
	function __get($name){
		switch ($name){
			case 'orderId':
			case 'orderDate':
			case 'customer':
			case 'shipAddr1':
			case 'shipAddr2':
			case 'shipCity':
			case 'shipState':
			case 'shipZip':
			case 'nameOnCard':
			case 'cardType':
			case 'cardNumber':
			case 'expMonth':
			case 'expYear':
			case 'products':
				return $this->$name;
			case 'customerId':
				return $this->customer->customerId;
			case 'itemCount':
				$count = 0;
				foreach($this->products as $product){
					$count += $product->purchasedQuantity;
				}
				return $count;
			case 'total':
				$total = 0;
				foreach($this->products as $product){
					$total += $product->purchasedQuantity * $product->price;
				}
				return self::shippingCost + $total;
			case 'shippingAddress':
				return $this->shipAddr1.(strlen($this->shipAddr2) > 0 ? ', '.
						$this->shipAddr2 : '').'<br>'.$this->shipCity.', '.
						$this->shipState.' '.$this->shipZip;
			case 'paymentInfo':
					return $this->nameOnCard.'<br>'.$this->cardType.
						' - XXXXXXXXXXXX'.substr($this->cardNumber, 12).
						'<br>Exp. '.$this->expMonth.'/'.$this->expYear;
			default:
				throw new \BadFunctionCallException(
						"$name does not exist or is not visible.");
		}
	}
	/* Provides write access to protected fields. Validates input and
	 * formats when necessary.
	 */
	function __set($name, $value) {
		nullIf($value, '');
		switch ($name){
			case 'customer':
				if($value == intval($value) && $value > 0){
					try {
						$customer = Customer::existingCustomer(
								["customer_id" => $value]);
						$this->$name = $customer;
					} catch (UnexpectedQueryResultException $ex){
						array_push($this->errors, "Invalid CustomerID");
					} catch (\Exception $ex){
						throw $ex;
					}
				} else {
					array_push($this->errors, "Invalid CustomerID");
				}
				break;
			case 'shipAddr1':
			case 'billAddr1':
				if ($this->validateStrAttribute($name, $value, true, 30,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'shipAddr2':
			case 'billAddr2':
				if ($this->validateStrAttribute($name, $value, false, 30,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'shipCity':
			case 'billCity':
				if ($this->validateStrAttribute($name, $value, true, 20,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'shipState':
			case 'billState':
				if ($this->validateStrAttribute($name, $value, true, 2,
					'/^[A-Z]{2}$/')){
					$this->$name = $value;
				}
				break;
			case 'shipZip':
			case 'billZip':
				if ($this->validateStrAttribute($name, $value, true, 10,
					'/^(\d{5}(-?\d{4})?)?$/')){
					$this->$name = $value;
				}
				break;
			case 'nameOnCard':
				if ($this->validateStrAttribute($name, $value, true, 60,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'cardType':
				if ($value != 'VISA' && $value != 'MasterCard'){
					array_push($this->errors,'Invalid Card Type');
				} else {
					$this->$name = $value;
				}
				break;
			case 'cardNumber':
				if ($this->validateStrAttribute($name, $value, true, 16,
					'/^\d{16}$/')){
					$this->$name = $value;
				}
				break;
			case 'securityCode':
				if ($this->validateStrAttribute($name, $value, true, 16,
					'/^\d{3}$/')){
					$this->$name = $value;
				}
				break;
			case 'expMonth':
				if($value == intval($value) && $value > 0 && $value < 13){
					$this->$name = $value;
				} else {
					array_push($this->errors, "Invalid Expiration Month");
				}
				break;
			case 'expYear':
				if($value == intval($value) && $value >= date("Y")){
					$this->$name = $value;
				} else {
					array_push($this->errors, "Invalid Expiration Year");
				}
				break;
			case 'products':
				foreach ($value as $productId => $quantity){
					try {
						$product = Product::existingProduct(
								["product_id" => $productId]);
					} catch (UnexpectedQueryResultException $ex){
						array_push($this->errors,
								"Product ID $productId does not exist");
						break;
					}
					if ($quantity > $product->quantity){
						array_push($this->errors,
								"Product ID $productId has insufficient stock");
						break;
					} else {
						$product->purchasedQuantity = $quantity;
						array_push($this->$name, $product);
					}
				}
				break;
			case 'orderDate':
				$this->$name = $value;
				break;
		}
	}
	/* Return all Orders from the given CustomerId */
	static function getOrdersByCustomer($customerId){
		$orders = self::allOrders();
		return array_filter($orders, function($o) use ($customerId)
				{return intval($o->customerId) == $customerId; });
	}
	/* Load the associated products with the order object and store in
	* $products attribute
	*/
	private function loadProducts() {
		$data = Controller::getInstance()->query("select product_id, ".
				"current_product_price, quantity from order_detail where ".
				"order_id = $this->orderId");
		while ($object = $data->fetch_assoc()){
			$product = Product::existingProduct(["product_id" =>
					$object['product_id']]);
			$product->price = $object['current_product_price'];
			$product->purchasedQuantity = $object['quantity'];
			array_push($this->products, $product);
		}
	}
	/* Subtracts the purchased quantities of products from the database */
	private function updateInventory() {
		foreach ($this->products as $product) {
			$product->quantity =
				$product->quantity - $product->purchasedQuantity;
			$product->update();
		}
	}
	function insertSQL(){
		buildParameterList($parameters,$this->customerId, INTEGER);
		buildParameterList($parameters,$this->orderDate, DATE);
		buildParameterList($parameters,$this->shipAddr1, STRING);
		buildParameterList($parameters,$this->shipAddr2, STRING);
		buildParameterList($parameters,$this->shipCity, STRING);
		buildParameterList($parameters,$this->shipState, STRING);
		buildParameterList($parameters,$this->shipZip, STRING);
		buildParameterList($parameters,$this->cardType, STRING);
		buildParameterList($parameters,$this->nameOnCard, STRING);
		buildParameterList($parameters,$this->cardNumber, STRING);
		buildParameterList($parameters,$this->expMonth, INTEGER);
		buildParameterList($parameters,$this->expYear, INTEGER);
		buildParameterList($parameters,$this->securityCode, STRING);
		buildParameterList($parameters,$this->billAddr1, STRING);
		buildParameterList($parameters,$this->billAddr2, STRING);
		buildParameterList($parameters,$this->billCity, STRING);
		buildParameterList($parameters,$this->billState, STRING);
		buildParameterList($parameters,$this->billZip, STRING);
		return "call create_order($parameters, @order_id)";
	}
	function getSQL(){
		return "select o.order_id as orderId, o.order_date as orderDate, ".
				"o.customer_id AS customer, s.address_line_1 as shipAddr1, ".
				"s.address_line_2 as shipAddr2, s.city as shipCity, ".
				"s.state_abrv as shipState, s.zip_code as shipZip, ".
				"o.card_type as cardType, o.name_on_card as nameOnCard, ".
				"o.credit_card_nbr AS cardNumber, o.expiration_month as ".
				"expMonth, o.expiration_year as expYear, o.security_code as ".
				"securityCode, b.address_line_1 as billAddr1, ".
				"b.address_line_2 as billAddr2, b.city as billCity, ".
				"b.state_abrv as billState, b.zip_code as billZip ".
				"from customer_order as o inner join address as s on ".
				"o.shipping_address_id = s.address_id inner join address as b ".
				"on o.billing_address_id = b.address_id";
	}
	function updateSQL(){
		throw new  \BadFunctionCallException("Update not implemented");
	}
}
