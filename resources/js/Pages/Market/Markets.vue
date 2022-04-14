<script>
import Template from '{Template}/Web/Pages/Market/Markets.template'
import AppLayout from '@/Layouts/AppLayout'
import TableFilter from "@/Mixins/Filter/TableFilter";
import IconFilter from "@/Components/Table/IconFilter";
import MarketMixin from "@/Mixins/Market/MarketMixin";

export default Template({
    components: {
        AppLayout,
        IconFilter
    },
    mixins: [MarketMixin, TableFilter],
    computed: {
        markets: function () {

            let markets = _.map(this.$store.getters.getMarkets, (market) => {
                market['favorite'] = this.isFavorite(market.name);
                return market;
            });

            let orderMarkets = _.orderBy(markets, (market) => {

                if(this.filter.filterBy == 'name') {
                    return market[this.filter.filterBy];
                } else {
                    return parseFloat(market[this.filter.filterBy]);
                }

            }, this.filter.filterDirection == 'desc' ? 'desc' : 'asc');

            return _.filter(orderMarkets, (market) => {
                let sortedBy = true;

                if(this.filter.sortBy == "favorites") {
                    sortedBy = market.favorite == true;
                } else if(this.filter.sortBy) {
                    sortedBy = market.quote_currency == this.filter.sortBy;
                }

                return market.name.toLowerCase().includes(this.filter.search.toLowerCase()) && sortedBy;
            });


        },
    },
    mounted() {

        this.loadFavorites();

        this.setFilter('name', 'asc', true);

        if(_.isEmpty(this.markets)) {
            this.$store.dispatch('fetchMarkets');
        }

    },
})
</script>
