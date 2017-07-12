<?php
require_once("MySql.php");
//Класс для работы с базой данных
class ShopBase{
	protected $db;
	
	function __construct(){
		$this->db = new MySqlWork() ;
	}
	
	//проверка результата выполнения запроса на ошибку
	protected function is_query_error($result){
		if(is_array($result) && array_key_exists("is_mysql_error", $result)){
			return true;
		}			
		return false;
	}
	
	//получить список товаров в категории
	protected function get_category_products($category=1, $only_unique=true){
		$query = "call GetCategoryProducts({?})";
		$products = $this->db->queryArray($query, array($category));
		if($only_unique){
			$prev_id = -1;
			$result = array();
			foreach($products as $product){
				if($prev_id <> $product["id"]){
					$result[] = $product;
				}
				$prev_id=$product["id"];				
			}
			return $result;
		}

		return $products;
	}
	
	//получить список категорий, включая вложенные категории
	protected function get_categories($category=1){					
		$query = "call GetCategories({?})";
		$result = $this->db->queryArray($query, array($category));
		return $result;
	}
	
	//Добавить товар
	protected function add_product($name, $is_enbaled=1, $annonce=NULL, $desciption=NULL){
		$query = "INSERT INTO `shop_product` (`name`, `is_enabled`, `announce`, `description`)".
				" VALUES ({?}, {?}, {?}, {?})";
		$result = $this->db->querySimple($query, array($name, $is_enbaled, $annonce, $desciption));
		
		return $result;
	}
	
	//Обновить товар
	protected function update_product($id, $name, $is_enbaled=1, $annonce=NULL, $desciption=NULL){
		$query = "UPDATE `shop_product`". 
				  "SET `is_enabled`={?},
						`name`={?},
						`announce`={?},
						`description`={?} 
				   WHERE `id`={?}";
		$result = $this->db->querySimple($query, array($is_enbaled, $name, $annonce, $desciption, $id));
		return $result;
	}
	
	//Удалить товар
	//удаляем товар и таблицы товаров shop_product и из таблицы shop_product_category
	//правильнее настроить в БД on delete cascade, чтобы здесь не выполнять 2 операции удаления
	protected function delete_product($id){
		$query = "DELETE FROM `shop_product_category` WHERE `product_id`={?}"; 
		$result = $this->db->querySimple($query, array($id));
		if($result===true){
			$query = "DELETE FROM `shop_product`  WHERE `id`={?};";		 
			$result = $this->db->querySimple($query, array($id));
		}		
		return $result;		
	}	
	
	//Добавить товар к категории
	protected function add_category_product($category, $product){
		$query = "INSERT INTO `shop_product_category` (`category_id`, `product_id`)".
				" VALUES ({?}, {?})";
		$result = $this->db->querySimple($query, array($category, $product));
		return $result;
	}
	
	//Удалить товар из категории
	protected function delete_category_product($category, $product){
		$query = "DELETE FROM `shop_product_category` ".
				"WHERE `category_id`={?} AND `product_id`={?}";
		$result = $this->db->querySimple($query, array($category, $product));
		return $result;
	}
	
	//Добавить Категорию
	protected function add_category($name, $parent, $is_enabled=1){
		$query = "INSERT INTO `shop_category` (`name`, `parent`, `is_enabled`)".
				" VALUES ({?}, {?}, {?})";
		$result = $this->db->querySimple($query, array($name, $parent, $is_enabled));
		return $result;
	}
	
	//Обновить категорию
	protected function update_category($id, $name, $parent, $is_enbaled=1){
		$query = "UPDATE `shop_category`". 
				  "SET `is_enabled`={?},
						`name`={?},
						`parent`={?}
				   WHERE `id`={?}";
		$result = $this->db->querySimple($query, array($is_enbaled, $name, $parent, $id));
		return $result;
	}
	
	//Вспомогательная функция: проверяет наличие товаров в категории
	protected function category_has_products($category){
		$products = $this->get_category_products($category);
		if(count($products)>0) return true;
		return false;
	}
	
	//Вспомогательная функция: проверяет категорию на пустоту
	protected function category_is_empty($category){
		$categories = $this->get_categories($category);
		if(count($categories)==1 && !$this->category_has_products($category)){
			return true;
		}
		return false;
	}
	
	//если only_empty=false - удаляет категорию вместе с товарами в этой категории
	//иначе - не удаляет категорию, в которой имеются товары
	protected function delete_category($category, $only_empty=true){
		
		if($only_empty===true){
			if($this->category_is_empty($category)){
				$query = "DELETE FROM `shop_category` WHERE `id`={?}" ;
				$result = $this->db->querySimple($query, array($category));
				
			}else{
				$result = false;
			}
		}else{
			//удаляем товары из категории и подкатегорий
			//параметр false означает, получить все товары, в том числе и неуникальные
			$products = $this->get_category_products($category, false);
			
			foreach($products as $product){		
				$this->delete_category_product($product["category"], $product["id"]);				
			}
			
			$categories = $this->get_categories($category);
			foreach($categories as $cat){
				$this->delete_category($cat["id"]);
				$result["categories"][] = $product["id"];
			}
			$result = true;
		}
		return $result;
	}
}
?>