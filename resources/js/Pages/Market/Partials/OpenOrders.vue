<script>
import Template from '{Template}/Web/Pages/Market/Partials/OpenOrders.template'
import {math_formatter} from "@/Functions/Math";

export default Template({
    props: {
        market: Object,
    },
    data() {
        return {
            openOrdersInterval: null,
            limit: 20,
        }
    },
    mounted() {
        if(this.$page.props.user) {
            this.fetchOpenOrders();

            this.openOrdersInterval = setInterval(() => {
                this.fetchOpenOrders();
            }, 5000);
        }
    },
    beforeDestroy: function(){
        clearInterval(this.openOrdersInterval)
    },
    computed: {
        orders: function () {
            return _.take(_.orderBy(this.$store.getters.getOpenOrders(this.market.name), 'created_at', 'desc'), this.limit);
        },
    },
    methods: {
        cancelOrder(order) {

            let form = {
                'uuid': order.id
            };

            axios.post(route('orders.api.cancel'), form).then((response) => {

            }).catch(error => {

            });
        },
        fetchOpenOrders() {
            this.$store.dispatch('fetchOpenOrders', {market: this.market.name});
        },
        decimal_format(value, decimal) {
            return math_formatter(value, decimal);
        }
    }
})
</script>
