<script>
import Template from '{Template}/Web/Pages/Market/Partials/MarketTrades.template'
import {math_formatter} from "@/Functions/Math";
import moment from "moment";

export default Template({
    props: {
        market: Object,
    },
    data() {
        return {
            limit: 10,
        }
    },
    mounted() {
        this.$store.dispatch('fetchMarketTrades', { market: this.market.name });
    },
    computed: {
        trades: function () {
            return _.take(_.orderBy(this.$store.getters.getMarketTrades(this.market.name), 'created_at', 'desc'), this.limit);
        },
    },
    methods: {
        decimal_format(value, decimal) {
            return math_formatter(value, decimal);
        },
        parseTime(date) {
            return moment(date).format('hh:mm:ss');
        }
    },
})
</script>
