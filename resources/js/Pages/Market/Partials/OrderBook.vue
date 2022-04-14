<script>
import Template from '{Template}/Web/Pages/Market/Partials/OrderBook.template'
import {math_formatter, math_percentage_of_number} from "@/Functions/Math";

export default Template({
    props: {
        market: Object,
    },
    data() {
        return {
            limit: 100,
        }
    },
    mounted() {
        this.$store.dispatch('fetchOrders', { market: this.market.name });
    },
    computed: {
        bids: function () {
            return _.take(_.orderBy(this.$store.getters.getOrderbook(this.market.name, 'bids'), (item) => {
                return parseFloat(item.price);
            }, 'desc'), this.limit);
        },
        asks: function () {
            return _.take(_.orderBy(this.$store.getters.getOrderbook(this.market.name, 'asks'), (item) => {
                return parseFloat(item.price);
            }, 'asc'), this.limit).reverse();
        },
        market_stats: function () {
            return this.$store.getters.getMarket(this.market.name) ?? this.market;
        },
        sumBuyQuantity: function () {
            return _.sumBy(this.bids, function(order) { return parseFloat(order.quantity * order.price); });
        },
        sumSellQuantity: function () {
            return _.sumBy(this.asks, function(order) { return parseFloat(order.quantity); });
        },
    },
    methods: {
        decimal_format(value, decimal) {
            return math_formatter(value, decimal);
        },
        calculateAmountBar(order, side) {

            let total = '';
            let amount = '';

            if(side == 'buy') {
                total = this.sumBuyQuantity;
                amount = parseFloat(order.quantity * order.price);
            } else {
                total = this.sumSellQuantity;
                amount = order.quantity;
            }

            return math_percentage_of_number(amount, total) + '%';
        },
        handleOrder(order, side) {
            this.$worker.$emit('place-order', {
                order: order,
                side: side,
            });
        }
    }
})
</script>
