<?php
/*
	CCelkoNastedSet class 

    Written by Setec Astronomy

	Thanks to Kirill Hryapin <kx@chl.ru> for finding and solving bugs into the MoveNode () method.

    Modified by Vladimir Obukhov <r2@instantsoft.ru> for InstantCMS project.

	Members
	$TableName => Table name that contains nasted sets 
	$FieldID => Field name for table ID
	$FieldIDParent => Field name for table IDParent
	$FieldLeft => Field name for table nasted set left field 
	$FieldRight => Field name for table nasted set right field  
    $FieldDiffer => Field name used to manage more than one type of nasted set in the same table
	$FieldLevel => Field name for table nasted set level field  (0 = root node)
	$FieldOrder => Field name for table nasted set order field   
	$FieldIgnore => Field name for tablr nested set ignore field 
	
	$TransactionTable => Name for table used to manage transactions

	$MyLink => MySQL database link resource

	Methods 
	SelectSubNodes () => Returns all the sub node of IDNode fo Level number of level.
	SelectPath () => Returns the path from the IDNode to the root node.
	MoveNode () => Method used to move the node IDNode to IDParent.
	AddRootNode () => Method used to add the root node for a nested set
	AddNode () => Method used to add a node to IDParent. Return the new IDNode.
	DeleteNode () => Method used to delete the node identified by IDNode and all his children.
	ClearNodes () => Method used to clear the nasted set table

	BeginTransaction () => Method that start the transaction. Returns true or false.
	EndTransaction () => Method that stop the transaction.
	IsInTransaction () => Method to check the transaction state. Returns true or false.

*/

class CCelkoNastedSet { 

   public  $TableName;
   private $FieldID;
   private $FieldIDParent;
   private $FieldLeft;
   private $FieldRight;
   private $FieldDiffer;
   private $FieldLevel;
   private $FieldOrder;
   private $FieldIgnore;
   
   public  $TransactionTable;
   private $_IsInTransaction;
   private $_TransactionTStamp;

   public $MyLink;

   public function __construct ()
	{ 
	   $this->TableName = "tblCelkoTree";  
	   $this->FieldID = "id"; 
	   $this->FieldIDParent = "parent_id"; 
	   $this->FieldLeft = "NSLeft"; 
	   $this->FieldRight = "NSRight"; 
	   $this->FieldDiffer = "NSDiffer";
	   $this->FieldLevel = "NSLevel";  
	   $this->FieldOrder = "ordering";
	   $this->FieldIgnore = "NSIgnore"; 
	
	   $this->TransactionTable = "cms_ns_transactions";
	   $this->_IsInTransaction = false;
	   $this->_TransactionTStamp = 0;

	   $this->MyLink = null;  
	} 

	// Begin private functions //
	private function _safe_set (&$var_true, $var_false = "")
	{
		if (!isset ($var_true)) 
		{ $var_true = $var_false; }
	}

	private function _safe_query ($query, $link)
	{
		if (empty($query)) { return false; }
        $inDB   = cmsDatabase::getInstance();
		$result = $inDB->query($query) or die("Nested sets error: $query");
		return $result;
	}
	// End private functions //
	
	// Begin transaction functions //
	private function InitializeTransaction ($Differ = "")
	{
		$sql_verify = "SELECT * FROM " . $this->TransactionTable .
					  " WHERE TableName = '" . $this->TableName . "' " .
					  " AND Differ = '" . $Differ . "'";
		$rs_verify = $this->_safe_query ($sql_verify, $this->MyLink);
		
		if (($rs_verify) && (mysql_num_rows ($rs_verify) == 0))
		{
			mysql_free_result ($rs_verify);
			
	   		$sql_insert = "INSERT INTO " . $this->TransactionTable . 
						  " (TableName, Differ, InTransaction) " .
						  " VALUES ('" . $this->TableName . "', '" . $Differ . "', 0)";
			$this->_safe_query ($sql_insert, $this->MyLink);
			
			return true;
		}
		else
		{ return false; }
	}

	private function BeginTransaction ($Differ = "")
	{
        //r2: ����� ����� ����� ����������� � ������������
        $this->_IsInTransaction = true; return true;

        $this->InitializeTransaction($Differ);

		$TStamp = date ("YmdHis");

		$sql_update = "UPDATE " . $this->TransactionTable . 
					  " SET TStamp = " . $TStamp . ", " .
					  " InTransaction = 1 " .
					  " WHERE InTransaction = 0 " .
					  " AND TableName = '" . $this->TableName . "' " .
					  " AND Differ = '" . $Differ . "'";
		$this->_safe_query ($sql_update, $this->MyLink);
		
		$sql_verify = "SELECT * FROM " . $this->TransactionTable .
					  " WHERE TableName = '" . $this->TableName . "' " .
					  " AND InTransaction = 1 " .
					  " AND TStamp = " . $TStamp . 
					  " AND Differ = '" . $Differ . "'";
		$rs_verify = $this->_safe_query ($sql_verify, $this->MyLink);

		if (($rs_verify) && (mysql_num_rows ($rs_verify) == 1))
		{
			mysql_free_result ($rs_verify);
			$this->_IsInTransaction = true;
			$this->_TransactionTStamp = $TStamp;
			return true;
		}
		else
		{ 
			$this->_IsInTransaction = false;
			$this->_TransactionTStamp = 0;
			return false; 
		}
	}
	
	public function EndTransaction ($Differ = "")
	{
		$sql_update = "UPDATE " . $this->TransactionTable . 
					  " SET InTransaction = 0 " .
					  " WHERE TableName = '" . $this->TableName . "' " .
					  " AND TStamp = " . $this->_TransactionTStamp . 
					  " AND Differ = '" . $Differ . "'";
		$this->_safe_query ($sql_update, $this->MyLink);
		$this->_IsInTransaction = false;
	}

	public function IsInTransaction ()
	{ return $this->_IsInTransaction; }
	// End transaction  functions //
	
	// Begin nasted set functions //
	public function ClearNodes ($Differ = "")
	{
        $this->BeginTransaction($Differ);
		$sql_delete = "DELETE FROM " . $this->TableName . " WHERE " . $this->FieldDiffer . " = '" . $Differ . "'";
		$this->_safe_query ($sql_delete, $this->MyLink);
        $this->EndTransaction($Differ);
	}

	public function DeleteNode ($IDNode = -1, $Differ = "")
	{
        $this->BeginTransaction($Differ);

		if (!$this->_IsInTransaction)
		{ return false; }
		
		$sql_select = "SELECT * FROM " . $this->TableName . " WHERE " . $this->FieldID . " = " . $IDNode . " AND " . $this->FieldDiffer . " = '" . $Differ . "'"; 
		$rs_select = $this->_safe_query ($sql_select, $this->MyLink);
		if (($rs_select) && ($row_select = mysql_fetch_assoc ($rs_select)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$delete_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft];

			// Delete sub nodes
			$sql_delete = "DELETE FROM " . $this->TableName . 
						  " WHERE " . $this->FieldLeft . " >= " . $row_select[$this->FieldLeft] . 
						  " AND " . $this->FieldLeft . " <= " . $row_select[$this->FieldRight] .
						  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
			$this->_safe_query ($sql_delete, $this->MyLink);

			// Update FieldLeft 
			$sql_update = "UPDATE " . $this->TableName . 
						  " SET " . $this->FieldLeft . " = " . $this->FieldLeft . " - " .  ($delete_offset + 1) .
						  " WHERE " . $this->FieldLeft . " > " . $row_select[$this->FieldRight] .
						  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
			$this->_safe_query ($sql_update, $this->MyLink);

			// Update FieldRight 
			$sql_update = "UPDATE " . $this->TableName . 
						  " SET " . $this->FieldRight . " = " . $this->FieldRight . " - " .  ($delete_offset + 1) .
						  " WHERE " . $this->FieldRight . " > " . $row_select[$this->FieldRight] . 
						  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
			$this->_safe_query ($sql_update, $this->MyLink);
		
			mysql_free_result ($rs_select);

            $this->EndTransaction($Differ);
			return true;


        } else {

            $this->EndTransaction($Differ);
            return false;

        }
	}

	public function AddRootNode ($Differ = "")
	{
        $this->BeginTransaction($Differ);

		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_insert = "INSERT INTO " . $this->TableName . 
					  " (" . $this->FieldIDParent . ", " . $this->FieldLeft . ", " . $this->FieldRight . 
					  ", " . $this->FieldLevel . ", " . $this->FieldOrder . ", " . $this->FieldDiffer . ") " .
					  " VALUES (0, 1, 2, 0, 1, '" . $Differ . "')";
		$this->_safe_query ($sql_insert, $this->MyLink);

        $new_id = mysql_insert_id ($this->MyLink);

        $this->EndTransaction($Differ);

		return $new_id;
	}

	public function AddNode ($IDParent = -1, $Order = -1, $Differ = "")
	{
        $this->BeginTransaction($Differ);

		if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "SELECT * FROM " . $this->TableName . " WHERE " . $this->FieldID . " = " . $IDParent . " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
		$rs_select = $this->_safe_query ($sql_select, $this->MyLink);
		if (($rs_select) && ($row_select = mysql_fetch_assoc ($rs_select)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			
			$left = $row_select[$this->FieldLeft] + 1;
		
			// Update Order (set order = order +1 where order>$Order)
			if ($Order == -1)
			{
				$sql_order = "SELECT * FROM " . $this->TableName . 
							 " WHERE " . $this->FieldIDParent . " = " . $IDParent .
						  	 " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
							 " ORDER BY " . $this->FieldOrder . " DESC " .
							 " LIMIT 0,1";
				$rs_order = $this->_safe_query ($sql_order, $this->MyLink);
				if (($rs_order) && ($row_order = mysql_fetch_assoc ($rs_order)))
				{
					$this->_safe_set ($row_order[$this->FieldOrder], 0);
					$Order = $row_order[$this->FieldOrder] + 1;
					mysql_free_result ($rs_order);
				}
				else
				{ $Order = 1; }
			}

			$sql_update = "UPDATE " . $this->TableName . 
						  " SET " . $this->FieldOrder . " = " . $this->FieldOrder . " + 1" .
						  " WHERE " . $this->FieldIDParent . " = " . $IDParent .
						  " AND " . $this->FieldOrder . " >= " . $Order . 
						  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
			$this->_safe_query ($sql_update, $this->MyLink);
			
			$sql_order = "SELECT * FROM " . $this->TableName . 
						 " WHERE " . $this->FieldIDParent . " = " . $IDParent .
						 " AND " . $this->FieldOrder  . " <= " . $Order .
						 " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
						 " ORDER BY " . $this->FieldOrder . " DESC " .
						 " LIMIT 0,1";
			$rs_order = $this->_safe_query ($sql_order, $this->MyLink);
			if (($rs_order) && ($row_order = mysql_fetch_assoc ($rs_order)))
			{
				$this->_safe_set ($row_order[$this->FieldRight], -1);
				$left = $row_order[$this->FieldRight] + 1;
				mysql_free_result ($rs_order);
			}
			
			$right = $left + 1;
			
			// Update FieldLeft 
			$sql_update = "UPDATE " . $this->TableName . 
						  " SET " . $this->FieldLeft . " = " . $this->FieldLeft . " + 2" .
						  " WHERE " . $this->FieldLeft . " >= " . $left .
					  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
			$this->_safe_query ($sql_update, $this->MyLink);
			
			// Update FieldRight 
			$sql_update = "UPDATE " . $this->TableName . 
						  " SET " . $this->FieldRight . " = " . $this->FieldRight . " + 2" .
						  " WHERE " . $this->FieldRight . " >= " . $left .
					  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
			$this->_safe_query ($sql_update, $this->MyLink);

			// Insert
	   		$sql_insert = "INSERT INTO " . $this->TableName . 
						  " (" . $this->FieldIDParent . ", " . $this->FieldLeft . ", " . $this->FieldRight . 
						  ", " . $this->FieldLevel . ", " . $this->FieldOrder . ", " . $this->FieldDiffer . ") " .
						  " VALUES (" . $IDParent . ", " . $left . ", " . $right . 
						  ", " . ($row_select[$this->FieldLevel] + 1) . ", " . $Order . ", '" . $Differ . "')";
			$this->_safe_query ($sql_insert, $this->MyLink);

			mysql_free_result ($rs_select);

            $new_id = mysql_insert_id ($this->MyLink);

            $this->EndTransaction($Differ);

			return $new_id;
		}
		else
		{ return false; }
	}
	
    public function MoveOrdering($IDNode, $dir = 1){

        $this->BeginTransaction();

        if (!$this->_IsInTransaction)
		{ return false; }
        
        $sql = "SELECT * FROM {$this->TableName} WHERE {$this->FieldID}='{$IDNode}'";
        $res = $this->_safe_query ($sql, $this->MyLink);
        $move_row = mysql_fetch_assoc($res);
        mysql_free_result($res);
        
        if ($move_row[$this->FieldDiffer]) $Differ = 'AND ' . $this->FieldDiffer . ' = ' . $move_row[$this->FieldDiffer];
        else $Differ = '';
        
		// ������������ �������� ����������
        $sql = "SELECT MAX({$this->FieldOrder}) FROM {$this->TableName} WHERE {$this->FieldIDParent}={$move_row[$this->FieldIDParent]}";
        $res = $this->_safe_query ($sql, $this->MyLink);
        list($maxordering) = mysql_fetch_row($res);
        if (!$maxordering) $maxordering = 1;
		// ����������� �������� ����������
        $sql_min = "SELECT MIN({$this->FieldOrder}) FROM {$this->TableName} WHERE {$this->FieldIDParent}={$move_row[$this->FieldIDParent]}";
        $res_min = $this->_safe_query ($sql_min, $this->MyLink);
        list($minordering) = mysql_fetch_row($res_min);
        if (!$minordering) $minordering = 1;

        mysql_free_result($res);
        
        if ($dir == -1 && $move_row[$this->FieldOrder] == $minordering) return;
        if ($dir == 1 && $move_row[$this->FieldOrder] == $maxordering) return;
        
        if ($dir == -1){
            
            $sql = "UPDATE {$this->TableName} SET {$this->FieldIgnore} = 1 
                    WHERE {$this->FieldLeft} >= {$move_row[$this->FieldLeft]} AND {$this->FieldRight} <= {$move_row[$this->FieldRight]} {$Differ}";
            $this->_safe_query ($sql, $this->MyLink);
            $count = mysql_affected_rows($this->MyLink) * 2;
            
            $sql = "SELECT * FROM {$this->TableName} 
                    WHERE {$this->FieldIDParent} = {$move_row[$this->FieldIDParent]} AND {$this->FieldOrder} = ".($move_row[$this->FieldOrder]-1);
            $res = $this->_safe_query ($sql, $this->MyLink);
            $near = mysql_fetch_assoc($res);
            mysql_free_result($res);
            
            $sql = "UPDATE {$this->TableName} 
                    SET {$this->FieldLeft} = {$this->FieldLeft} + {$count},
                        {$this->FieldRight} = {$this->FieldRight} + {$count}
                    WHERE {$this->FieldLeft} >= {$near[$this->FieldLeft]} AND {$this->FieldRight} <= {$near[$this->FieldRight]}
                    {$Differ}";
                    
            $this->_safe_query ($sql, $this->MyLink);
            $count2 = mysql_affected_rows($this->MyLink) * 2;
            
            $sql = "UPDATE {$this->TableName} 
                    SET {$this->FieldLeft} = {$this->FieldLeft} - {$count2},
                        {$this->FieldRight} = {$this->FieldRight} - {$count2},
                        {$this->FieldIgnore} = 0
                    WHERE {$this->FieldIgnore} = 1
                    {$Differ}";
            $this->_safe_query ($sql, $this->MyLink);
            
            $sql = "UPDATE {$this->TableName} SET {$this->FieldOrder} = {$this->FieldOrder} - 1
                    WHERE {$this->FieldID} = {$IDNode}";
            $this->_safe_query ($sql, $this->MyLink);
            
            $sql = "UPDATE {$this->TableName} SET {$this->FieldOrder} = {$this->FieldOrder} + 1
                    WHERE {$this->FieldID} = {$near[$this->FieldID]}";
            $this->_safe_query ($sql, $this->MyLink);
        }
        
        if ($dir == 1){
            
            $sql = "UPDATE {$this->TableName} SET {$this->FieldIgnore} = 1 
                    WHERE {$this->FieldLeft} >= {$move_row[$this->FieldLeft]} AND {$this->FieldRight} <= {$move_row[$this->FieldRight]} {$Differ}";
            $this->_safe_query ($sql, $this->MyLink);
            $count = mysql_affected_rows($this->MyLink) * 2;
            
            $sql = "SELECT * FROM {$this->TableName} 
                    WHERE {$this->FieldIDParent} = {$move_row[$this->FieldIDParent]} AND {$this->FieldOrder} = ".($move_row[$this->FieldOrder]+1);
            $res = $this->_safe_query ($sql, $this->MyLink);
            $near = mysql_fetch_assoc($res);
            mysql_free_result($res);
            
            $sql = "UPDATE {$this->TableName} 
                    SET {$this->FieldLeft} = {$this->FieldLeft} - {$count},
                        {$this->FieldRight} = {$this->FieldRight} - {$count}
                    WHERE {$this->FieldLeft} >= {$near[$this->FieldLeft]} AND {$this->FieldRight} <= {$near[$this->FieldRight]}
                    {$Differ}";
                    
            $this->_safe_query ($sql, $this->MyLink);
            $count2 = mysql_affected_rows($this->MyLink) * 2;
            
            $sql = "UPDATE {$this->TableName} 
                    SET {$this->FieldLeft} = {$this->FieldLeft} + {$count2},
                        {$this->FieldRight} = {$this->FieldRight} + {$count2},
                        {$this->FieldIgnore} = 0
                    WHERE {$this->FieldIgnore} = 1
                    {$Differ}";
            $this->_safe_query ($sql, $this->MyLink);
            
            $sql = "UPDATE {$this->TableName} SET {$this->FieldOrder} = {$this->FieldOrder} + 1
                    WHERE {$this->FieldID} = {$IDNode}";
            $this->_safe_query ($sql, $this->MyLink);
            
            $sql = "UPDATE {$this->TableName} SET {$this->FieldOrder} = {$this->FieldOrder} - 1
                    WHERE {$this->FieldID} = {$near[$this->FieldID]}";
            $this->_safe_query ($sql, $this->MyLink);
        }

        $this->EndTransaction($Differ);

        return true;
    }	

	public function MoveNode ($IDNode = -1, $IDParent = -1, $Order = -1, $Differ = "")
	{
        $this->BeginTransaction($Differ);

        if (!$this->_IsInTransaction)
		{ return false; }

		$sql_select = "SELECT * FROM " . $this->TableName .
		              " WHERE " . $this->FieldID . " = " . $IDNode .
		              " AND " . $this->FieldDiffer . " = '" . $Differ . "'";

		$rs_select = $this->_safe_query ($sql_select, $this->MyLink);
		if (($rs_select) && ($row_select = mysql_fetch_assoc ($rs_select)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			$delete_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft];


			$sql_select_parent = "SELECT * FROM " . $this->TableName .
			                     " WHERE " . $this->FieldID . " = " . $IDParent .
			                     " AND " . $this->FieldDiffer . " = '" . $Differ . "'";

			$rs_select_parent = $this->_safe_query ($sql_select_parent, $this->MyLink);
			if (($rs_select_parent) && ($row_select_parent = mysql_fetch_assoc ($rs_select_parent)))
			{
				$this->_safe_set ($row_select_parent[$this->FieldID], -1);
				$this->_safe_set ($row_select_parent[$this->FieldLeft], -1);
				$this->_safe_set ($row_select_parent[$this->FieldRight], -1);
				$this->_safe_set ($row_select_parent[$this->FieldLevel], -1);

				$left = $row_select_parent[$this->FieldLeft] + 1;

				//Set node tree as ignore
				$sql_ignore = "UPDATE " . $this->TableName .
							  " SET " . $this->FieldIgnore . " = 1" .
							  " WHERE " . $this->FieldLeft . " >= " . $row_select[$this->FieldLeft] .
							  " AND " . $this->FieldRight . " <= " . $row_select[$this->FieldRight] .
						  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
				$this->_safe_query ($sql_ignore, $this->MyLink);

				// Update Order (set order = order +1 where order>$Order)
				if ($Order == -1)
				{
					$sql_order = "SELECT * FROM " . $this->TableName .
								 " WHERE " . $this->FieldIDParent . " = " . $IDParent .
								 " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
								 " ORDER BY " . $this->FieldOrder . " DESC " .
								 " LIMIT 0,1";
					$rs_order = $this->_safe_query ($sql_order, $this->MyLink);
					if (($rs_order) && ($row_order = mysql_fetch_assoc ($rs_order)))
					{
						$this->_safe_set ($row_order[$this->FieldOrder], 0);
						$Order = $row_order[$this->FieldOrder] + 1;
						mysql_free_result ($rs_order);
					}
					else
					{ $Order = 1; }
				}

				$sql_update = "UPDATE " . $this->TableName .
							  " SET " . $this->FieldOrder . " = " . $this->FieldOrder . " + 1" .
							  " WHERE " . $this->FieldIDParent . " = " . $IDParent .
							  " AND " . $this->FieldOrder . " >= " . $Order .
							  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
				$this->_safe_query ($sql_update, $this->MyLink);

				$sql_order = "SELECT * FROM " . $this->TableName .
							 " WHERE " . $this->FieldIDParent . " = " . $IDParent .
							 " AND " . $this->FieldOrder  . " <= " . $Order .
							 " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
							 " ORDER BY " . $this->FieldOrder . " DESC " .
							 " LIMIT 0,1";
				$rs_order = $this->_safe_query ($sql_order, $this->MyLink);
				if (($rs_order) && ($row_order = mysql_fetch_assoc ($rs_order)))
				{
					$this->_safe_set ($row_order[$this->FieldRight], -1);
					$left = $row_order[$this->FieldRight] + 1;
					mysql_free_result ($rs_order);
				}

				$child_offset = $row_select[$this->FieldRight] - $row_select[$this->FieldLeft] + 1;

				// Update FieldLeft
				if ($left < $row_select[$this->FieldLeft]) // Move to left
				{
					$sql_update = "UPDATE " . $this->TableName .
								  " SET " . $this->FieldLeft . " = " . $this->FieldLeft . " + (" . $child_offset . ")" .
								  " WHERE " . $this->FieldLeft . " >= " . $left .
								  " AND " . $this->FieldLeft . " <= " . $row_select[$this->FieldLeft] .
								  " AND " . $this->FieldIgnore . " = 0" .
								  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
				}
				else // Move to right
				{
					$sql_update = "UPDATE " . $this->TableName .
								  " SET " . $this->FieldLeft . " = " . $this->FieldLeft . " - " . $child_offset .
								  " WHERE " . $this->FieldLeft . " <= " . $left .
								  " AND " . $this->FieldLeft . " >= " . $row_select[$this->FieldLeft] .
								  " AND " . $this->FieldIgnore . " = 0" .
								  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
				}
				$this->_safe_query ($sql_update, $this->MyLink);

				// Update FieldRight
				if ($left < $row_select[$this->FieldLeft]) // Move to left
				{
					$sql_update = "UPDATE " . $this->TableName .
								  " SET " . $this->FieldRight . " = " . $this->FieldRight . " + (" . $child_offset . ")" .
								  " WHERE " . $this->FieldRight . " >= " . $left .
								  " AND " . $this->FieldRight . " <= " . $row_select[$this->FieldRight] .
								  " AND " . $this->FieldIgnore . " = 0" .
								  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
				}
				else // Move to right
				{
					$sql_update = "UPDATE " . $this->TableName .
								  " SET " . $this->FieldRight . " = " . $this->FieldRight . " - " . $child_offset .
								  " WHERE " . $this->FieldRight . " < " . $left .
								  " AND " . $this->FieldRight . " >= " . $row_select[$this->FieldRight] .
								  " AND " . $this->FieldIgnore . " = 0" .
								  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
				}
				$this->_safe_query ($sql_update, $this->MyLink);

				$level_difference = $row_select_parent[$this->FieldLevel] - $row_select[$this->FieldLevel] + 1;
				$new_offset = $row_select[$this->FieldLeft] - $left;
                if ($left > $row_select[$this->FieldLeft]) // i.e. move to right
                { $new_offset += $child_offset; }

				//Update new tree left
				$sql_update = "UPDATE " . $this->TableName .
							  " SET " . $this->FieldLeft . " = " . $this->FieldLeft . " - (" . $new_offset . "), " .
							  $this->FieldRight . " = " . $this->FieldRight . " - (" . $new_offset . ")," .
							  "$this->FieldLevel = $this->FieldLevel + $level_difference" .
							  " WHERE " . $this->FieldLeft . " >= " . $row_select[$this->FieldLeft] .
							  " AND " . $this->FieldRight . " <= " . $row_select[$this->FieldRight] .
							  " AND " . $this->FieldIgnore . " = 1" .
						  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
				$this->_safe_query ($sql_update, $this->MyLink);

				//Remove ignore statis from node tree
				$sql_ignore = "UPDATE " . $this->TableName .
							  " SET " . $this->FieldIgnore . " = 0" .
							  " WHERE " . $this->FieldLeft . " >= " . ($row_select[$this->FieldLeft] - $new_offset) .
							  " AND " . $this->FieldRight . " <= " . ($row_select[$this->FieldRight] - $new_offset) .
							  " AND " . $this->FieldIgnore . " = 1" .
						  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
				$this->_safe_query ($sql_ignore, $this->MyLink);

				//Update insert root field
				$sql_update = "UPDATE " . $this->TableName . " SET " . $this->FieldIDParent . " = " . $IDParent . ", " .
							  $this->FieldOrder . " = " . $Order . " WHERE " . $this->FieldID . " = " . $IDNode;
				$this->_safe_query ($sql_update, $this->MyLink);

				mysql_free_result ($rs_select_parent);
                $this->EndTransaction($Differ);
				return true;
			}
			else
			{ $this->EndTransaction($Differ);
                return false; }

			mysql_free_result ($rs_select);
            $this->EndTransaction($Differ);
			return true;
		}
		else
		{ $this->EndTransaction($Differ); return false; }
	}

	public function SelectPath ($IDNode = -1, $Differ = "")
	{

		$sql_select = "SELECT * FROM " . $this->TableName . " WHERE " . $this->FieldID . " = " . $IDNode . " AND " . $this->FieldDiffer . " = '" . $Differ . "'";        
		$rs_select = $this->_safe_query ($sql_select, $this->MyLink);
		if (($rs_select) && ($row_select = mysql_fetch_assoc ($rs_select)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$sql_result = "SELECT * FROM " . $this->TableName . 
						  " WHERE " . $this->FieldLeft . " <= " . $row_select[$this->FieldLeft] .
						  " AND " . $this->FieldRight . " >= " . $row_select[$this->FieldRight] . 
					  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'" . 
						  " ORDER BY " . $this->FieldLeft;
			mysql_free_result ($rs_select);
			return $this->_safe_query ($sql_result, $this->MyLink); // Remember to free result			  
		}
		else
		{ return false; }
	}

	public function SelectSubNodes ($IDNode = -1, $Level = -1, $Differ = "")
	{

		$sql_select = "SELECT * FROM " . $this->TableName . " WHERE " . $this->FieldID . " = " . $IDNode . " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
		$rs_select = $this->_safe_query ($sql_select, $this->MyLink);
		if (($rs_select) && ($row_select = mysql_fetch_assoc ($rs_select)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT * FROM " . $this->TableName . 
							  " WHERE " . $this->FieldLeft . " > " . $row_select[$this->FieldLeft] .
							  " AND " . $this->FieldRight . " < " . $row_select[$this->FieldRight] . 
						  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
							  " ORDER BY " . $this->FieldLeft . "," . $this->FieldOrder;
			}
			else // Only $Level child nodes
			{
				$sql_result = "SELECT * FROM " . $this->TableName . 
							  " WHERE " . $this->FieldLeft . " >= " . $row_select[$this->FieldLeft] .
							  " AND " . $this->FieldRight . " <= " . $row_select[$this->FieldRight] . 
							  " AND " . $this->FieldLevel . " <= " . ($Level + $row_select[$this->FieldLevel]) . 
						  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
							  " ORDER BY " . $this->FieldLeft . "," . $this->FieldOrder;
			}
			mysql_free_result ($rs_select);            
			return $this->_safe_query ($sql_result, $this->MyLink); // Remember to free result			  
		}
		else
		{ return false; }
	}
	
	public function SelectCountSubNodes ($IDNode = -1, $Level = -1, $Differ = "")
	{

		$sql_select = "SELECT * FROM " . $this->TableName . " WHERE " . $this->FieldID . " = " . $IDNode . " AND " . $this->FieldDiffer . " = '" . $Differ . "'";
		$rs_select = $this->_safe_query ($sql_select, $this->MyLink);
		if (($rs_select) && ($row_select = mysql_fetch_assoc ($rs_select)))
		{
			$this->_safe_set ($row_select[$this->FieldID], -1);
			$this->_safe_set ($row_select[$this->FieldLeft], -1);
			$this->_safe_set ($row_select[$this->FieldRight], -1);
			$this->_safe_set ($row_select[$this->FieldLevel], -1);
			if ($Level == -1) // All child nodes
			{
				$sql_result = "SELECT count(" . $this->FieldID . ") FROM " . $this->TableName . 
							  " WHERE " . $this->FieldLeft . " > " . $row_select[$this->FieldLeft] .
							  " AND " . $this->FieldRight . " < " . $row_select[$this->FieldRight] . 
						  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
							  " ORDER BY " . $this->FieldLeft . "," . $this->FieldOrder;
			}
			else // Only $Level child nodes
			{
				$sql_result = "SELECT count(" . $this->FieldID . ") FROM " . $this->TableName . 
							  " WHERE " . $this->FieldLeft . " > " . $row_select[$this->FieldLeft] .
							  " AND " . $this->FieldRight . " < " . $row_select[$this->FieldRight] . 
							  " AND " . $this->FieldLevel . " <= " . ($Level + $row_select[$this->FieldLevel]) . 
						  	  " AND " . $this->FieldDiffer . " = '" . $Differ . "'" .
							  " ORDER BY " . $this->FieldLeft . "," . $this->FieldOrder;
			}
			mysql_free_result ($rs_select);
			$res = $this->_safe_query ($sql_result, $this->MyLink); // Remember to free result
			list($count) = mysql_fetch_row($res);
			mysql_free_result ($res);
			return $count;
		}
		else
		{ return false; }
	}
	// End nasted set functions //
} 

?>