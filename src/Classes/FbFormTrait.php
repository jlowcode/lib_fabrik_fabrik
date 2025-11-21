<?php
/**
 * Fabrik Joomla\CMS\Form\Form overloader
 * Used for lso we can add dynamic properties to the Form class
 * and not get php warnings or failures.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       5.0
 */

namespace Fabrik\Library\Fabrik\Classes;

use Joomla\CMS\Factory;

trait FbFormTrait {

    /**
     * Method to get an array of fieldset objects optionally filtered over a given field group.
     *
     * @param   string  $group  The dot-separated form group path on which to filter the fieldsets.
     *
     * @return  object[]  The array of fieldset objects.
     *
     * @since   1.7.0
     */
    public function getFieldsets($group = null)
    {

    	$fieldsets = parent::getFieldsets($group);

        $input = Factory::getApplication()->getInput();
        /* Skip this for element plugins */
        if ($input->getString('type', '') == 'element') {
         	return $fieldsets;
        }
        $subformprefix = $input->getString('subformprefix');
        if ($subformprefix == null) {
        	return $fieldsets;
        }

        /* Drop the jform prefix, we don't need it here */
        $subformprefix = str_replace('jform', '', $subformprefix);
        $subformprefix = preg_replace('/\[(.*?)\]/', '$1_', $subformprefix);

        foreach ($fieldsets as $key => $fieldset) {
        	$name = $fieldset->name ?? null;
        	if (!empty($name)) {
		        /* It is possible we will run through here more than once for the same field, check it */
		        if (strpos($name, $subformprefix) !== false) {
		        	continue;
		        }
		        $fieldsets[$key]->name = $subformprefix . $name;
		    }
		    $id = $fieldset->id ?? null;
        	if (!empty($id)) {
        		$subformprefix = preg_replace('/\[(.*?)\]/', '_$1', $subformprefix);
		        /* It is possible we will run through here more than once for the same field, check it */
		        if (strpos($id, $subformprefix) !== false) {
		        	continue;
		        }
		        $fieldsets[$key]->$id = $subformprefix . '_' .  $id;
		    }
        }

        return $fieldsets;
    }
    /**
     * Method to get an array of `<field>` elements from the form XML document which are in a specified fieldset by name.
     *
     * @param   string  $name  The name of the fieldset.
     *
     * @return  \SimpleXMLElement[]|boolean  Boolean false on error or array of SimpleXMLElement objects.
     *
     * @since   1.7.0
     */
    protected function &findFieldsByFieldset($name)
    {
        // Make sure there is a valid Form XML document.
        if (!($this->xml instanceof \SimpleXMLElement)) {
            throw new \UnexpectedValueException(\sprintf('%s::%s `xml` is not an instance of SimpleXMLElement', \get_class($this), __METHOD__));
        }

        $input = Factory::getApplication()->getInput();
        $subformprefix = $input->getString('subformprefix');
        if (!empty($subformprefix)) {
        	/* Drop the subform prefix from the name for the search as the prefix will not be in the xml file */
		    $subformprefix = str_replace('jform', '', $subformprefix);
		    $subformprefix = preg_replace('/\[(.*?)\]/', '$1_', $subformprefix);
		    $name = str_replace($subformprefix, '', $name);
		}

        /*
         * Get an array of <field /> elements that are underneath a <fieldset /> element
         * with the appropriate name attribute, and also any <field /> elements with
         * the appropriate fieldset attribute. To allow repeatable elements only fields
         * which are not descendants of other fields are selected.
         */
        $fields = $this->xml->xpath('(//fieldset[@name="' . $name . '"]//field | //field[@fieldset="' . $name . '"])[not(ancestor::field)]');

        return $fields;
    }

}