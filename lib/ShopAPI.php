<?php
require_once("ShopBase.php");
class ShopAPI extends ShopBase{
	private $RESPONSE;
	private $IS_ERROR=0;
	private $CODE=200;
	
	private function CreateJSONResponse(){
		$result = [
			"is_error" => $this->IS_ERROR,
			"code" => $this->CODE,
			"response" => $this->RESPONSE
		];		
		header("Content-Type: application/json; charset=utf-8") ;
		echo json_encode($result, JSON_UNESCAPED_UNICODE) ;
	}
	
	//обработать ошибку
	private function ErrorResult($err_code, $err_text){
		$this->IS_ERROR = true;
		$this->RESPONSE = $err_text;
		$this->CODE = $err_code;
	}
	
	private function IsProductParamsCorrect($name, $is_enabled, $announce, $description){
		//здесь можно реализовать проверку корректности данных перед вставкой в БД
		return true;
	}
	
	private function IsCategoryParamsCorrect($name, $is_enabled){
		//здесь можно реализовать проверку корректности данных перед вставкой в БД
		return true;
	}
	
	private function IsCategoryProductParamsCorrect($category, $product){
		//здесь можно проверить корректность данных
		return true;
	}

	//получить значение параметра $param в массиве $array
	private function GetParam($param, $array, $default=NULL){
		if(array_key_exists($param, $array)) return $array[$param];
		return $default;
	}
	
	public function GetCategories($category=1){					
		if(is_numeric($category)){
			$result = array();
			$categories = $this->get_categories((int)$category);
			if(is_array($categories) && !empty($categories)){
				foreach($categories as $category){
					//подсчитываем количество товара
					$tmp = $this->get_category_products($category["id"], false);
					$category["count_products"] = count($tmp);
					$result[] = $category;
				}
			}
			
		} else{
			$result = $this->ErrorResult(-1, "Передан некорректный параметр. Ожидаемый тип - int, полученный тип - ".gettype($category)."") ;
		}
		return $result;
		$this->CreateJSONResponse();
	}
	
	public function GetCategoryProducts($category=1){					
		if(is_numeric($category)){
			$result = array();
			$products = $this->get_category_products((int)$category);
			$result["products"] = $products;
			$result["count_products"] = count($products);
		} else{
			throw new Exception("Передан некорректный параметр. Ожидаемый тип - int, полученный тип - ".gettype($category)."", -1);
		}
		return $result;
	}
	
	public function AddProduct($name, $is_enabled=1, $announce=NULL, $description=NULL){
		if($this->IsProductParamsCorrect($name, $is_enabled, $announce, $description)){	
			$result = $this->add_product($name, $is_enabled, $announce, $description);
		} else{
			throw new Exception("Товар не добавлен: некорректные параметры", -1);
		}		
		return $result;
	}
	
	public function UpdateProduct($id, $name, $is_enabled=1, $announce=NULL, $description=NULL){
		if($this->IsProductParamsCorrect($name, $is_enabled, $announce, $description)){
			if($this->product_exists($id)){
				$result = $this->update_product($id, $name, $is_enabled, $announce, $description);
			}else{
				throw new Exception("Товар не обновлен: товар с идентификатором {$id} не найден", -1);
			}
			
		} else{
			throw new Exception("Товар не обновлен: некорректные параметры", -1);
		}			
		return $result;
	}
	
	public function DeleteProduct($id){
		if($this->product_exists($id)){
			$result = $this->delete_product($id);
		}else{
			throw new Exception("Товар не удален: товар с идентификатором {$id} не найден", -1);
		}
		
		return $result;
	}	
	
	public function AddCategoryProduct($category, $product){
		if($this->IsCategoryProductParamsCorrect($category, $product)){
			if(!$this->category_exists($category)) 
				throw new Exception("Товар не добавлен в категорию: не найдена категория {$category}");
			if(!$this->product_exists($product)) 
				throw new Exception("Товар не добавлен в категорию: не найден товар {$product}");
			$result = $this->add_category_product($category, $product);
		}  else{
			throw new Exception("Товар не добавлен в категорию: некорректные параметры", -1);
		}		
		return $result;
	}

	public function DeleteCategoryProduct($category, $product){
		if(!$this->category_exists($category)) 
			throw new Exception("Товар не удален из категории: не найдена категория {$category}");
		if(!$this->product_exists($product)) 
			throw new Exception("Товар не удален из категории: не найден товар {$product}");
		return $this->delete_category_product($category, $product);
	}
	
	public function AddCategory($name, $parent, $is_enabled=1){
		if($this->IsCategoryParamsCorrect($name, $parent, $is_enabled)){
			$result = $this->add_category($name, $parent, $is_enabled);
			$this->RESPONSE = $result;
		} else{
			throw new Exception("Категория не добавлена: некорректные параметры", -1);
		}		
		return $result;
	}
	
	public function UpdateCategory($id, $name, $parent, $is_enabled=1){
		if($this->IsCategoryParamsCorrect($name, $parent, $is_enabled)){
			if(!$this->category_exists($id)) 
				throw new Exception("Категория не обновлена: не найдена категория {$id}");
			$result = $this->update_category($id, $name, $parent, $is_enabled);
		}else{
			throw new Exception("Категория не обновлена: некорректные параметры", -1);
		}				
		return $result;
	}
	
	//если only_empty=false - удаляет категорию вместе с товарами в этой категории
	//иначе - не удаляет категорию, в которой имеются товары
	public function DeleteCategory($id, $only_empty=true){
		if(!$this->category_exists($id)) 
			throw new Exception("Категория удалить не удалось: не найдена категория {$id}");
		$result = $this->delete_category($id, $only_empty);
		if($result===false){
			throw new Exception("Категорию удалить не удалось. Возможно, в ней содержатся товары или подкатегории", -1);
		} 		
		return $result;
	}	
	
	public function Dispatch($action, $params){
		
		try{
			switch ($action){
				//получить список всех категорий
				case "get_all_categories":
					$this->RESPONSE = $this->GetCategories();
					break;
					
				case "get_categories":
					$category = $this->GetParam("category", 	$params);
					$this->RESPONSE = $this->GetCategories($category);
					break;
				
				//получить список товаров в категории (возвращаются товары и в подкатегориях указанной категории)
				case "get_products_by_category":
					$category = $this->GetParam("category", 	$params);
					$this->RESPONSE = $this->GetCategoryProducts($category);
					break;
				
				//добавить товар
				case "add_product":
					$name 		= $this->GetParam("name", 		$params);
					$is_enabled = $this->GetParam("is_enabled", $params, 1);
					$announce 	= $this->GetParam("announce", 	$params);
					$description = $this->GetParam("description", $params);
					
					$this->RESPONSE = $this->AddProduct($name, $is_enabled, $announce, $description);
					break;
				
				//обновить информацию о товаре
				case "update_product":
					$id 		= $this->GetParam("id", 		$params);
					$name 		= $this->GetParam("name", 		$params);
					$is_enabled = $this->GetParam("is_enabled", $params, 1);
					$announce 	= $this->GetParam("announce", 	$params);
					$description = $this->GetParam("description", $params);
					
					$this->RESPONSE = $this->UpdateProduct($id, $name, $is_enabled, $announce, $description);
					break;
				
				//удалить товар
				case "delete_product":
					$id 		= $this->GetParam("id", $params);					
					$this->RESPONSE = $this->DeleteProduct($id);
					break;
					
				//добавить товар в категорию
				case "add_product_to_category":
					$category 		= $this->GetParam("category", $params);
					$product 		= $this->GetParam("product", $params);
					$this->RESPONSE = $this->AddCategoryProduct($category, $product);
					break;
					
				//удалить товар из категории
				case "delete_product_from_category":
					$category 		= $this->GetParam("category", $params);
					$product 		= $this->GetParam("product", $params);
					$this->RESPONSE = $this->DeleteCategoryProduct($category, $product);
					break;
				
				//добавить категорию
				case "add_category":
					$name 		= $this->GetParam("name", 		$params);
					$is_enabled = $this->GetParam("is_enabled", $params, 1);
					$parent 	= $this->GetParam("parent", 	$params);
					
					$this->RESPONSE = $this->AddCategory($name, $parent, $is_enabled);
					break;
				
				//обновить категорию
				case "update_category":
					$id 		= $this->GetParam("id", 		$params);
					$name 		= $this->GetParam("name", 		$params);
					$is_enabled = $this->GetParam("is_enabled", $params, 1);
					$parent 	= $this->GetParam("parent", 	$params);
					
					$this->RESPONSE = $this->UpdateCategory($id, $name, $parent, $is_enabled);
					break;
				
				//удалить товар
				case "delete_category":
					$id 		= $this->GetParam("id", 		$params);	
					$only_empty = $this->GetParam("only_empty", $params, true);
					
					$this->RESPONSE = $this->DeleteCategory($id, $only_empty);
					break;
					
				default:
					throw new Exception("Вызван неизвестный метод: {$action}", -2);
					break;
			}
		}
		catch (Exception $ex) {
			$this->ErrorResult($ex->getCode(), $ex->getMessage());
		}
		
		$this->CreateJSONResponse();	
	}
	
}
?>