Event.observe(window, 'load', function () {
    var simpleAction = $('rule_simple_action'),
        stopRulesProcessing = $('rule_stop_rules_processing'),
        ruleTree = $('rule_actions_fieldset').up(),
        toggleElements = function () {
            var action = simpleAction.value == 'upgrade_shipping_method' ? 'hide' : 'show';
            simpleAction.up().up().siblings().each(function (tr) {
                tr[action]();
            });
            ruleTree[action]();
            stopRulesProcessing.up().up().show();
        }
        ;
    if (simpleAction && stopRulesProcessing && ruleTree) {
        simpleAction.observe('change', toggleElements);
        toggleElements();
    }
});
