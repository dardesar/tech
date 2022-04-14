<script>
    import Template from '{Template}/Web/Pages/Dashboard.template'
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
    import LanguageSwitcher from '@/Components/LanguageSwitcher'
    import MarketMixin from "@/Mixins/Market/MarketMixin";
    import { Hooper, Slide } from 'hooper';
    import 'hooper/dist/hooper.css';

    export default Template({
        components: {
            AppLayout,
            Welcome,
            LanguageSwitcher,
            Hooper,
            Slide
        },
        data() {

            return {
                sliderElements: 2,
                isMobile: false,
                hooperSettings: {
                    itemsToShow: this.sliderElements,
                    vertical: true,
                    autoPlay: true,
                    touchDrag: false,
                    mouseDrag: false,
                    playSpeed: 3000,
                    infiniteScroll: true,
                    breakpoints: {
                        670: {
                            itemsToShow: 1,
                        }
                    }
                }
            }
        },
        created() {
            window.addEventListener("resize", this.sliderListener);
        },
        destroyed() {
            window.removeEventListener("resize", this.sliderListener);
        },
        methods: {
            sliderListener() {
                if(window.innerWidth >= 670) {
                    this.sliderElements = 3;
                    this.isMobile = false;
                } else {
                    this.sliderElements = 2;
                    this.isMobile = true;
                }
            }
        },
        computed: {

            sliderMarkets: function () {

                let markets = this.$store.getters.getMarkets;

                if(markets && markets.length) {
                    return _.chunk(_.orderBy(markets, (market) => {
                        return parseFloat(market['last']);
                    }, 'desc'), this.sliderElements);
                }
            },
            topMarkets: function () {

                let markets = this.$store.getters.getMarkets;

                if(markets && markets.length) {
                    return _.orderBy(markets, (market) => {
                        return parseFloat(market['last']);
                    }, 'desc');
                }
            },
        },
        mixins: [MarketMixin],
        mounted() {

            this.sliderListener();

            if(_.isEmpty(this.markets)) {
                this.$store.dispatch('fetchMarkets');
            }
            window.TyperSetup();
        }
    });
</script>
