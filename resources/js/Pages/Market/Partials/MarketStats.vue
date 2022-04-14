<script>
import Template from '{Template}/Web/Pages/Market/Partials/MarketStats.template'

export default Template({
    props: {
        market: Object,
    },
    computed: {
        market_stats: function () {
            return this.$store.getters.getMarket(this.market.name) ?? this.market;
        },
    },
});
</script>
