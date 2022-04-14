<template>
    <div></div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
    name: 'market-channel',
    data() {
        return {
            channel: 'market',
        }
    },
    computed: mapGetters({
        user: 'getUser',
        socket: 'getSocket'
    }),
    watch: {
        socket: function (val) {
            this.join();
        }
    },
    methods: {
        join: function () {
            //Join to the channel and listen events
            Echo.channel(this.channel)
                .listen('MarketStatsUpdated', (payload) => {
                this.$store.dispatch('updateMarket', {
                    market: payload.market
                });
            }).listen('MarketTradeUpdated', (payload) => {
                this.$store.dispatch('updateMarketTrade', {
                    market: payload.market,
                    trade: payload.trade
                });
            });

        }
    }
}
</script>
