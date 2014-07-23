<?php

/**
 * Deal with EE SOLR for dynamic categories
 *
 * @category   ProxiBlue
 * @package    DynCatProd
 * @author     Lucas van Staden (sales@proxiblue.com.au)
 */
class ProxiBlue_DynCatProd_Model_Enterprise_Search_Adapter_HttpStream extends Enterprise_Search_Model_Adapter_HttpStream {

    /**
     * Prepare facet fields conditions
     *
     * @param array $facetFields
     * @return array
     */
    protected function _prepareFacetConditions($facetFields) {
        $result = array();
        if (is_array($facetFields)) {
            $result['facet'] = 'on';
            foreach ($facetFields as $facetField => $facetFieldConditions) {
                if (empty($facetFieldConditions)) {
                    $result['facet.field'][] = $facetField;
                } else {
                    foreach ($facetFieldConditions as $facetCondition) {
                        if (is_array($facetCondition) && count($facetCondition) == 0) {
                            $result['facet.field'][] = $facetField;
                            continue;
                        } else if (is_array($facetCondition) && isset($facetCondition['from']) && isset($facetCondition['to'])) {
                            $from = (isset($facetCondition['from']) && strlen(trim($facetCondition['from']))) ? $this->_prepareQueryText($facetCondition['from']) : '*';
                            $to = (isset($facetCondition['to']) && strlen(trim($facetCondition['to']))) ? $this->_prepareQueryText($facetCondition['to']) : '*';
                            $fieldCondition = "$facetField:[$from TO $to]";
                        } else {
                            $facetCondition = $this->_prepareQueryText($facetCondition);
                            $fieldCondition = $this->_prepareFieldCondition($facetField, $facetCondition);
                        }

                        $result['facet.query'][] = $fieldCondition;
                    }
                }
            }
        }
        return $result;
    }

}
