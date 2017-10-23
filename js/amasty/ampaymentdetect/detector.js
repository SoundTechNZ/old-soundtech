AmastyPaymentDetector = Class.create();
AmastyPaymentDetector.prototype = {
    cards: {
        electron: /^(4026|417500|4405|4508|4844|4913|4917)\d+$/,
        dankort: /^(5019)\d+$/,
        interpayment: /^(636)\d+$/,
        unionpay: /^(62|88)\d+$/,
        visa: /^4[0-9]{0,}$/,
        mastercard: /^(5[1-5]|222[1-9]|22[3-9]|2[3-6]|27[01]|2720)[0-9]{0,}$/,
        amex: /^3[47][0-9]{0,}$/,
        diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/,
        discover: /^(6011|65|64[4-9]|62212[6-9]|6221[3-9]|622[2-8]|6229[01]|62292[0-5])[0-9]{0,}$/,
        maestro: /^(5[06789]|6)[0-9]{0,}$/,
        jcb: /^(?:2131|1800|35)[0-9]{0,}$/
    },
    icons: $H({}),
    titles: $H({}),
    orders: $H({}),
    config: $H({
        hideDropdown: '1',
        showIcons: '1',
        iconWidth: '70'
    }),
    templates: $H({
        wrapper: new Template('<ul class="amasty-payment-detect">#{content}</ul>'),
        element: new Template('<li style="order: #{order};" title="#{title}" amasty-payment-icon-listen="1" select="#{select}" value="#{value}">' +
                '<img alt="#{title}" width="#{width}" src="#{image}"/>' +
            '</li>')
    }),
    /**
     * selectors with card types
     */
    modules: $H({}),
    initialize: function(
        icons,
        titles,
        orders,
        modules,
        config
    ){
        this.modules = this.modules.merge(modules);
        this.icons = this.icons.merge(icons);
        this.titles = this.titles.merge(titles);
        this.orders = this.orders.merge(orders);
        this.config = this.config.merge(config);

        this.initAllLayouts();
        this.initAllEvents();
    },
    initAllLayouts: function() {
        this.modules.each(function (module) {
            this.initLayout(module.value);
        }.bind(this));
    },
    initAllEvents: function() {
        this.modules.each(function (module) {
            this.initEvents(module.value);
        }.bind(this));
    },
    /**
     * build icons html and connect with type selectbox
     * @param module
     */
    initLayout: function(module){
        var options = $H(module.options);

        $$( module.selectors.type).each(function(select){
            var content = [];
            select.select('option').each(function(selectOption){
                var configOptions = options.filter(function(configOption){
                    return configOption.value == selectOption.value;
                });

                if (configOptions.length > 0){
                    this.initType(content, select, configOptions[0], selectOption);
                }
            }.bind(this));

            if (this.config.get('showIcons')) {
                select.insert({
                    'before': this.templates.get('wrapper').evaluate({
                        content: content.join('')
                    })
                });
            }

            this.initTypeEvents(module, select);
            this.onChangeType(module, select);

            if (this.config.get('hideDropdown')){
                select.hide();
            }

        }.bind(this));
    },
    /**
     * @param module
     * @param select
     */
    initTypeEvents: function(module, select){
        select.on('change', function(){
            this.onChangeType(module, select);
        }.bind(this));

        select.up().on('click', 'li[amasty-payment-icon-listen=1]', function(event, li){
            select.setValue(li.getAttribute('value'))
            this.onChangeType(module, select);
        }.bind(this));
    },
    /**
     * @param module
     * @param select
     */
    onChangeType: function(module, select){
        $$('li[select=' + select.id + ']').each(function(li){
            if (li.getAttribute('value') == select.getValue()){
                li.addClassName('selected');
            } else {
                li.removeClassName('selected');
            }
        });
    },
    /**
     *
     * @param content
     * @param select
     * @param configOption
     * @param selectOption
     */
    initType: function(content, select, configOption, selectOption)
    {
        var vars = {
            image: this.icons.get(configOption.key),
            select: select.id,
            value: selectOption.value,
            width: this.config.get('iconWidth'),
            title: this.titles.get(configOption.key),
            order: this.orders.get(configOption.key)
        };

        content.push(this.templates.get('element').evaluate(vars));
    },
    /**
     * initialize events
     */
    initEvents: function(module){
        document.on('keyup', module.selectors.number, function (event, input) {
            this.numbersKeyup(module, input);
        }.bind(this));
    },
    /**
     * on number input keyup
     * @param module
     * @param input
     */
    numbersKeyup: function(module, input){
        var card = this.detect(input.value);
        if (card){
            this.apply(module, card);
        }
    },
    /**
     * detect card type by card number
     * @param number
     * @returns {*}
     */
    detect: function(number){
        var cards = $H(this.cards).filter(function(card){
            return card.value.test(number);
        });
        return cards.length > 0 ? cards[0] : null;
    },
    /**
     * apply card type
     * @param module
     * @param card
     */
    apply: function(module, card)
    {
        $$(module.selectors.type).each(function(select){
            select.setValue(module.options[card.key]);
            this.onChangeType(module, select);
        }.bind(this));
    }
}