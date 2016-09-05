define([
    'Magento_Ui/js/form/components/button',
    'Magento_Ui/js/grid/provider'
], function(Button1, Provider1){

    return Button1.extend({

        defaults: {
            test: 'bar',
        },

        testFunction: function (data) {
            console.log('testFunction 123');
            Provider1.reload(data);
            console.log('testFunction 321');
            return this;
        }
});
});