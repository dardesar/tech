<script>
import Template from '{Template}/Web/Pages/Market/Partials/MarketsWidget.template'
import MarketMixin from '@/Mixins/Market/MarketMixin';
import TableFilter from "@/Mixins/Filter/TableFilter";
import IconFilter from "@/Components/Table/IconFilter";

export default Template({
    props: {
        quotes: [Array, Object],
    },
    components: {
        IconFilter
    },
    data() {
        return {
            openOrdersInterval: null,
        }
    },
    computed: {
        markets: function () {

            let markets = _.map(this.$store.getters.getMarkets, (market) => {
                market['favorite'] = this.isFavorite(market.name);
                return market;
            });

            if(markets && markets.length) {

                let orderMarkets = _.orderBy(markets, (market) => {

                    if (this.filter.filterBy == 'name') {
                        return market[this.filter.filterBy];
                    } else {
                        return parseFloat(market[this.filter.filterBy]);
                    }

                }, this.filter.filterDirection == 'desc' ? 'desc' : 'asc');

                return _.filter(orderMarkets, (market) => {
                    let sortedBy = true;

                    if (this.filter.sortBy == "favorites") {
                        sortedBy = market.favorite == true;
                    } else if (this.filter.sortBy) {
                        sortedBy = market.quote_currency == this.filter.sortBy;
                    }

                    return market.name.toLowerCase().includes(this.filter.search.toLowerCase()) && sortedBy;
                });

            }

        },
    },
    mixins: [MarketMixin, TableFilter],
    mounted() {

        this.setFilter('name', 'asc', true);

        if(_.isEmpty(this.markets)) {
            this.$store.dispatch('fetchMarkets');
        }
    }
})
</script>
