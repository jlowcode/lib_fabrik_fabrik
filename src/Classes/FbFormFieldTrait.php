<?php
/**
 * Fabrik Joomla\CMS\Form\FormField overloader
 * Used for  we can add dynamic properties to the Form class
 * and not get php warnings or failures.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       5.0
 */

namespace Fabrik\Library\Fabrik\Classes;

use Joomla\CMS\Factory;

trait FbFormFieldTrait
{

    /**
     * Method to get the name used for the field input tag.
     *
     * @param   string  $fieldName  The field element name.
     *
     * @return  string  The name to be used for the field input tag.
     *
     * @since   3.6
     */
    protected function getName($fieldName)
    {

    	$name = parent::getName($fieldName);
        $input = Factory::getApplication()->getInput();
        /* Skip this for element plugins */
        if ($input->getString('type', '') == 'element') {
        	return $name;
        }

        $subformprefix = $input->getString('subformprefix');
        if ($subformprefix == null) {
        	return $name;
        }

        /* It is possible we will run through here more than once for the same field, check it */
        if (strpos($name, $subformprefix) !== false) {
        	return $name;
        }

        $name = str_replace('jform', $subformprefix, $name);

        return $name;

    }

    /**
     * Method to get the id used for the field input tag.
     *
     * @param   string  $fieldId    The field element id.
     * @param   string  $fieldName  The field element name.
     *
     * @return  string  The id to be used for the field input tag.
     *
     * @since   1.7.0
     */
    protected function getId($fieldId, $fieldName)
    {
    	$id = parent::getId($fieldId, $fieldName);
        $input = Factory::getApplication()->getInput();
         /* Skip this for element plugins */
        if ($input->getString('type', '') == 'element') {
        	return $id;
        }
       $subformprefix = $input->getString('subformprefix');
        if ($subformprefix == null) {
        	return $id;
        }

        $subformprefix = preg_replace('/\[(.*?)\]/', '_$1', $subformprefix);
        
        /* It is possible we will run through here more than once for the same field, check it */
        if (strpos($id, $subformprefix) !== false) {
        	return $id;
        }

        $id = str_replace("jform", $subformprefix, $id);
        
        return $id;
    }
}
