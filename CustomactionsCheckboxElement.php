<?php

namespace Icinga\Module\Neteye\Web\Form\Element;

use ipl\Html\FormElement\CheckboxElement;

class CustomactionsCheckboxElement extends CheckboxElement
{
    use BaseInputElement;
    
    public function setValue($value)
    {
        $this->value = $value;
        // To validate the value
        $this->isValid = null;
        return $this;
    }
}