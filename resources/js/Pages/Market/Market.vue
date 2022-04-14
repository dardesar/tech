<script>
import Template from '{Template}/Web/Pages/Market/Market.template'
import AppLayout from '@/Layouts/AppLayout'
import OrderBook from "@/Pages/Market/Partials/OrderBook";
import MarketsWidget from "@/Pages/Market/Partials/MarketsWidget";
import TextInput from "@/Jetstream/TextInput";
import TButton from "@/Jetstream/Button";
import MarketStats from "@/Pages/Market/Partials/MarketStats";
import OrderForm from "@/Pages/Market/Partials/OrderForm";
import OpenOrders from "@/Pages/Market/Partials/OpenOrders";
import MarketTrades from "@/Pages/Market/Partials/MarketTrades";
import OrderbookChannel from "@/Store/Channels/Public/Market/OrderbookChannel";
import MarketMixin from '@/Mixins/Market/MarketMixin';

export default Template({
    components: {
        OrderbookChannel,
        MarketTrades,
        OrderForm,
        MarketStats,
        TButton,
        TextInput,
        MarketsWidget,
        AppLayout,
        OrderBook,
        OpenOrders
    },
    mixins: [MarketMixin],
    props: {
        market: Object,
        quotes: [Array, Object],
    },
    mounted() {
        this.loadFavorites();
    }
})
</script>
