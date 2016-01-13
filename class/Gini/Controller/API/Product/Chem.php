<?php

/**
 * @brief API
 * @author Jinlin Li
 * @date 2016-1-13
 */
namespace Gini\Controller\API\Product;

/**
 * @brief 继承自Gini\Controller\API
 */
class Chem extends \Gini\Controller\API
{
    public function actionSearchProducts(array $criteria)
    {
		$db = \Gini\Database::db();
		$params = [];
		$types = ['highly_toxic', 'drug_precursor', 'hazardous'];
		$sql = "SELECT * FROM product ";
		if (isset($criteria['type']) && in_array($criteria['type'], $types)) {
			$sql .= "WHERE type=:type";
			$params['type'] = $type;
		}
		$products = $db->query($sql, null, $params)->rows();
		$count = count($products);
		$token = md5(J($criteria));
		$_SESSION[$token] = [
			'criteria' => $criteria,
			'sql' => $sql,
			'params' => $params
		];
		return [
			'token' => $token,
			'count' => $count,
		];
    }

    public function actionGetProducts($token, $start = 0, $perpage = 25)
    {
		$start = is_numeric($start) ? $start : 0;
		$perpage = min($perpage, 25);
		$db = \Gini\Database::db();
		$params =  $_SESSION[$token];
		$sql = $params['sql'].' LIMIT '.$start.','.$perpage;
		$params = $params['params'];
		$products = $db->query($sql, null, $params)->rows();
		$data = [];
		foreach ($products as $product) {
			$data[$product->id] = [
				'cas_no' => $product->cas_no,
				'name' => $product->name,
				'type' => $product->type,

			];
		}
		return $data;
    }
}
?>