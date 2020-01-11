<?php 
 
namespace App\Service; 


class ColorService{ 
 
 private $colorService=[
 						"Rouge" => "red",
 						"Vert" => "green",
 						"Jaune" => "yellow",
 						"purple" => "purple"
 					]; 
 
 public function getColorService(){ 
 
 return $this->colorService; 
 } 
} 
 
?> 