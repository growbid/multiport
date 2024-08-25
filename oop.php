<?php
	// oop training
	// $brand = "Lambogini";
	// $color = "Black";

	// function getCarInfo($brand, $color){
	// 	return "Brand: ".$brand.", Color: ".$color;
	// }

	// echo getCarInfo("Lambogini", "Black");

	class Car{
		// properties || fields
		private $brand;
		private $color;
		private $viacleType = "car";

		// constructor
		public function __construct($brand = "None", $color = "None"){ $this->brand = $brand; $this->color = $color; }

		// get properties
		public function getBrand(){return $this->brand;}
		public function getColor(){return $this->color;}
		public function getViacleType(){return $this->viacleType;}

		// set properties
		public function setBrand($brand){$this->brand = $brand;}
		public function setColor($color){$this->color = $color;}
		public function setViacleType($viacleType){$this->viacleType = $viacleType;}
	}


	$car01 = new Car("Lambogini", "Black");
	$car02 = new Car("BMW", "Blue");
?>