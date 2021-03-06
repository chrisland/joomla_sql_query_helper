<?php
/**
 * @version     0.0.2
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 * @author      Christian Marienfeld <post@chrisland.de> - www.chrisland.de
 */

defined('_JEXEC') or die;

class DbHelper
{
	
	/* ######    setDataRow    ########
	*
	*	$table :string *required
	*		example-> "#__table_region"	
	*
	*	$data :array *required
	*		example-> array('title' => 'test')	
	*
	*	$id :int *optional
	*		example-> 5
	*	
	*
	*/
	function setDataRow($table,$data,$id) {
		
		if (!$table) {
			return false;
		}
		$db =& JFactory::getDBO();
		$wert = new StdClass();
		foreach( $data as $key => $content ) {
			$wert->$key = $content;
		}
		if ( !$id ) {
			$wert->id = null;
			if ( !$db->insertObject($table, $wert, 'id') ) {
				return false;
			}
			return $db->insertid();
		} else {
			$wert->id = $id;
			if ( !$db->updateObject($table, $wert, 'id') ) {
				return false;
			}
			return true;
		}
		return false;
	}
	
	
	
	/* ######    getDataRowWhere    ########
	*
	*	$table :string *required
	*		example-> "#__table_region"	
	*
	*	$fields :array *optional
	*		example-> array('title','content')	
	*
	*	$where :array *optional
	*		example-> array('project_id'=> $project_id, 'region_id' => $item->id)
	*	
	*
	*/
	function getDataRowWhere($table,$fields = null,$where = null,$order = null) {
		
		$db = self::getDatabaseDataWhere($table,$fields,$where,$order);
		return $db->loadObject();
	}
	
	
	/* ######    getDataListWhere    ########
	*
	*	$table :string *required
	*		example-> "#__table_region"	
	*
	*	$fields :array *optional
	*		example-> array('title','content')	
	*
	*	$where :array *optional
	*		example-> array('project_id'=> $project_id, 'region_id' => $item->id)
	*	
	*
	*/
	function getDataListWhere($table,$fields = null,$where = null,$order = null,$limit_start,$limit_limit) {
		
		$db = self::getDatabaseDataWhere($table,$fields,$where,$order,$limit_start,$limit_limit);
		return $db->loadAssocList();
	}
	

	
	/* ######    getDataRowWhereOR    ########
	*
	*	$table :string *required
	*		example-> "#__table_region"	
	*
	*	$fields :array *optional
	*		example-> array('title','content')	
	*
	*	$where :array *optional
	*		example-> array( array('id'=> $id_1) , array('id' => $id_2) )
	*	
	*
	*/
	function getDataListWhereOr($table,$fields = null,$where = null) {
		
		if (!$table) {
			return false;
		}
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.id');
		if (is_array($fields)) {
			foreach($fields as $field) {
				$query->select('a.'.$field);
			}
		}
		$query->from($table.' as a');
		$query_where = '';
		if ( is_array($where) ) {
			foreach($where as $where_item) {
				if ($query_where) { $query_where .= ' OR '; }
				foreach($where_item as $key_foo => $foo) {
					$query_where .= 'a.'.$key_foo.' = '.$foo;
				}
			}	
		}		
		$query->where($query_where);
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}
		return $db->loadAssocList();
	}
	
	
	
	
	/* ######    getDatabaseDataWhere    ########
	*
	*	$table :string *required
	*		example-> "#__table_region"	
	*
	*	$fields :array *required
	*		example-> array('title','content')	
	*
	*	$where :array *required
	*		example-> array('project_id'=> $project_id, 'region_id' => $item->id)
	*	
	*
	*/
	function getDatabaseDataWhere($table,$fields,$where,$order,$limit_start,$limit_limit) {
		
		if (!$table) {
			return false;
		}
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.id');
		if (is_array($fields)) {
			foreach($fields as $field) {
				$query->select('a.'.$field);
			}
		}
		$query->from($table.' as a');
		if ( is_array($where) ) {
			foreach($where as $where_key => $where_item) {
				$query->where('a.'.$where_key.' = "'.$where_item.'"' );
			}
		}		
		if (is_array($order)) {
			foreach($order as $order_item) {
				$query->order('a.'.$order_item);
			}
		}
		if ( is_int($limit_start) && is_int($limit_limit) ) { 
			$db->setQuery((string)$query,$limit_start, $limit_limit);
		} else {
			$db->setQuery((string)$query);
		}
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}
		return $db;
	}
}

