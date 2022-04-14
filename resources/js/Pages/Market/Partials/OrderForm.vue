<script>
import Template from '{Template}/Web/Pages/Market/Partials/OrderForm.template'
import TextInput from "@/Jetstream/TextInput";
import SelectInput from "@/Jetstream/SelectInput";
import {mapGetters} from "vuex";
import VueSlider from 'vue-slider-component'
import '@/../css/progress-slider/default.css'
import {math_formatter, math_percentage} from "@/Functions/Math";

export default Template({
    components: {
        TextInput,
        SelectInput,
        VueSlider
    },
    props: {
        market: Object,
    },
    data() {
        return {
            orderType: 'limit',
            bid: {
                price: 0,
                quantity: 0,
                type: 'limit',
                side: 'buy',
                trigger_price: 0,
                total: 0,
            },
            ask: {
                price: 0,
                quantity: 0,
                type: 'limit',
                side: 'sell',
                trigger_price: 0,
                total: 0,
            },
            buySlider: {
                value: 0,
                marks: [0, 25, 50, 75, 100],
                formatter: '{value}%',
                disabled: true,
            },
            sellSlider: {
                value: 0,
                marks: [0, 25, 50, 75, 100],
                formatter: '{value}%',
                disabled: true,
            },
            errors: null,
            placingBuyOrder: false,
            placingSellOrder: false,
            buyErrorField: false,
            sellErrorField: false,
        }
    },
    mounted() {

        /*
        Order click event listener
         */
        this.$worker.$on('place-order', (data) => {

            // Set price
            if(this.orderType != "market") {
                this.bid.price = data.order.price;
                this.ask.price = data.order.price;
            } else {
                this.bid.price = '';
                this.ask.price = '';
            }

            this.bid.quantity = data.order.quantity;
            this.ask.quantity = data.order.quantity;
        })

        if(_.isEmpty(this.wallets)) {
            this.$store.dispatch('fetchWallets');
        }

        if(this.$page.props.user) {
            this.buySlider.disabled = false;
            this.sellSlider.disabled = false;
        }
    },
    computed: {
        ...mapGetters({
            wallets: 'getWallets'
        }),
        baseWallet: function () {
            if(this.$store.getters.getUser) {
                return this.$store.getters.getWallet(this.market.base_currency);
            }
        },
        quoteWallet: function () {
            if(this.$store.getters.getUser) {
                return this.$store.getters.getWallet(this.market.quote_currency);
            }
        },
    },
    methods: {
        placeBuyOrder() {

            if(this.placingBuyOrder) return;

            this.buyErrorField = null;
            this.sellErrorField = null;
            this.placingBuyOrder = true;

            if(!this.$page.props.user) {
                return this.$inertia.visit(route('login'));
            }

            this.bid.market = this.market.name;
            this.bid.type = this.orderType;

            if(this.bid.type == "market") {
                this.bid.quoteQuantity = this.bid.quantity;
            }

            if(this.bid.type == "stop") {
                this.bid.trigger_condition = 'down'; //this.bid.trigger_condition;
                this.bid.trigger_price = this.bid.trigger_price;
            }

            axios.post(route('orders.store'), this.bid).then((response) => {
                this.placingBuyOrder = false;
            }).catch(error => {

                this.placingBuyOrder = false;

                _.each(error.response.data.errors, (field, key) => {

                    if(key == "quoteQuantity") key = "quantity";

                    this.buyErrorField = key;
                    this.$toast.error(field[0]);
                });
            });
        },
        placeSellOrder() {

            if(this.placingSellOrder) return;

            this.placingSellOrder = true;
            this.buyErrorField = null;
            this.sellErrorField = null;

            if(!this.$page.props.user) {
                return this.$inertia.visit(route('login'));
            }

            this.ask.market = this.market.name;
            this.ask.type = this.orderType;

            if(this.ask.type == "market") {
                this.ask.quoteQuantity = this.ask.quantity;
            }

            if(this.ask.type == "stop") {
                this.ask.trigger_condition = 'down'; //this.ask.trigger_condition;
                this.ask.trigger_price = this.ask.trigger_price;
            }

            axios.post(route('orders.store'), this.ask).then((response) => {
                this.placingSellOrder = false;
                this.$store.dispatch('fetchOpenOrders', { market: this.market.name });
            }).catch(error => {
                this.placingSellOrder = false;
                _.each(error.response.data.errors, (field, key) => {
                    this.sellErrorField = key;
                    this.$toast.error(field[0]);
                });
            });
        },
        setOrderType(type) {
            this.orderType = type;
            this.buyErrorField = null;
            this.sellErrorField = null;
        },
        multiplier(num1, num2, side, field, precision) {

            if(!num1 || !num2) {

                if (side == "ask") {
                    this.ask.total = 0;
                } else {
                    this.bid.total = 0;
                }

                return;
            }

            let amount = math_formatter(parseFloat(num1) * parseFloat(num2), precision);

            if(side == "ask") {
                this.ask[field] = amount;
            } else {
                this.bid[field] = amount;
            }
        },
        divider(num1, num2, side, field, precision) {

            if(!num1 || !num2 || num2 == 0) {

                if(side == "ask") {
                    this.ask.total = 0;
                } else {
                    this.bid.total = 0;
                }

                return
            };

            let amount = math_formatter(parseFloat(num1) / parseFloat(num2), precision);

            if(side == "ask") {
                this.ask[field] = amount;
            } else {
                this.bid[field] = amount;
            }
        },
        changeBuySlider(percentage) {
            let balance = this.quoteWallet ? this.quoteWallet.balance_in_wallet : 0;
            let amountWithPercentage = math_percentage(balance, percentage);
            if(this.orderType == 'market') {
                this.bid.quantity = amountWithPercentage;
            } else {
                this.bid.total = amountWithPercentage;
                this.divider(this.bid.total, this.bid.price, 'bid', 'quantity', this.market.base_precision)
            }
        },
        changeSellSlider(percentage) {
            let balance = this.baseWallet ? this.baseWallet.balance_in_wallet : 0;
            this.ask.quantity = math_formatter(math_percentage(balance, percentage), this.market.quote_precision);
        },
        handleInput ($event, side, field) {

            let keyCode = ($event.keyCode ? $event.keyCode : $event.which);

            if ((keyCode < 48 || keyCode > 57) && (keyCode !== 46 || this.getFormData(side, field).indexOf('.') != -1)) {
                $event.preventDefault();
            }

            // restrict to 2 decimal places
            if(this.getFormData(side, field) != null && this.getFormData(side, field).indexOf(".")>-1 && (this.getFormData(side, field).split('.')[1].length > 8)){
                $event.preventDefault();
            }
        },
        clearInput ($event, side, field) {
            if(this.getFormData(side, field).charAt(0) == '.') {
                this.setFormData(side, field, 0);
            }
        },
        getFormData(side, field) {
            if(side == 'buy') {
                return this.bid[field].toString();
            }
            return this.ask[field].toString();
        },
        setFormData(side, field, value) {
            if(side == 'buy') {
                this.bid[field] = value;
            }
            this.ask[field] = value;
        },
    },
    watch: {
        orderType: function(type) {
            if(type == 'market') {
                this.bid.price = '';
                this.ask.price = '';
            }
        },
        'ask.quantity'(newVal){
            this.multiplier(this.ask.price, this.ask.quantity, 'ask', 'total', this.market.quote_precision)
        },
        'ask.price'(newVal){
            this.multiplier(this.ask.price, this.ask.quantity, 'ask', 'total', this.market.quote_precision)
        },
        'bid.quantity'(newVal){
            this.multiplier(this.bid.price, this.bid.quantity, 'bid', 'total', this.market.quote_precision)
        },
        'bid.price'(newVal){
            this.multiplier(this.bid.price, this.bid.quantity, 'bid', 'total', this.market.quote_precision)
        },
    }
})
</script>
