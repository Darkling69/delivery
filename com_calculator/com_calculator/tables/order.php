<?php defined( '_JEXEC' ) or die( 'Restricted access' );
	class TableOrder extends JTable
	{
		function __construct( &$db )
		{
			parent::__construct('#__calc_order', 'order', $db);
		}
	}
?>
