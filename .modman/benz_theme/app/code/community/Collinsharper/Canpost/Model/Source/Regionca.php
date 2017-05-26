<?php

class Collinsharper_Canpost_Model_Source_Regionca extends Collinsharper_Canpost_Model_Source_Regionus
{
    public function toOptionArray($isMultiselect = false)
    {
        return $this->toOptionArrayCcountry($isMultiselect, 'CA');
    }
}
