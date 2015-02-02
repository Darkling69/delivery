<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class CalculatorViewCalculator extends JViewLegacy
{
        // Overwriting JView display method
        function display($tpl = null) 
        {
			$model = $this->getModel();
			$model->Calculate(1);
			if($model->IsInnerPriceViewer()){
				$model->Calculate(0);
			}
			
			$model->MakeOrder();
			
			$cities = $model->GetCities();
			
			$terminals = array(
				'from' => $model->GetTerminalsByCity($model->city_from),
				'to' => $model->GetTerminalsByCity($model->city_to)
			);
						
			$this->assignRef('model', $model);
			$this->assignRef('cities', $cities);
			$this->assignRef('terminals', $terminals);
			
			parent::display($tpl);
        }
}
?>
