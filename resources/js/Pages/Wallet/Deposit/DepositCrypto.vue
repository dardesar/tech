<script>
import SelectInput from "@/Jetstream/SelectInput";
import Template from '{Template}/Web/Pages/Wallet/Deposit/DepositCrypto.template'
import AppLayout from '@/Layouts/AppLayout'
import {mapGetters} from "vuex";
import {string_cut} from "@/Functions/String";
import JetDialogModal from '@/Jetstream/DialogModal'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import SvgIcon from "@/Components/Svg/SvgIcon";
import QrCode from 'vue-qrcode-component'

export default Template({
    components: {
        AppLayout,
        SelectInput,
        JetDialogModal,
        JetSecondaryButton,
        SvgIcon,
        QrCode,
    },
    props: {
        symbol: String,
        currency: Object,
        errors: Object,
    },
    data() {
        return {
            wallet : false,
            walletData: false,
            showDepositModal: false,
            closeDepositModal: false,
            depositModal: null,
            showQr: false,
            showQrAlt: false,
            tooltipOptions: {
                placement: 'auto-start'
            }
        }
    },
    mounted() {
        if(_.isEmpty(this.wallets)) {
            this.$store.dispatch('fetchWallets');
        }

        if(_.isEmpty(this.deposits)) {
            this.$store.dispatch('fetchDeposits');
        }

        this.loadAddress();
    },
    computed: {
        ...mapGetters({
            wallets: 'getWallets',
            deposits: 'getDeposits',
        }),
    },
    methods: {
        doCopy(string) {
            this.$copyText(string).then(() => {
                this.$toast.open('Text was copied to the clipboard');
            }, function (e) {

            })
        },
        format_string(string, limit) {
            return string_cut(string, limit);
        },
        loadAddress() {
            if(this.currency.networks[0]) {
                this.getAddress(this.currency.networks[0].id);
            }
        },
        getAddress(network) {
            axios.get(route('wallets.api.getAddress'), {
                params: {
                    'network': network,
                    'symbol' : this.symbol
                }
            }).then((response) => {
                this.walletData = response.data;
            }).catch(error => {

            });
        },
        openModal(deposit) {
            this.depositModal = deposit;
            this.showDepositModal = true;
        },
        closeModal() {
            this.showDepositModal = false;
        }
    }
})
</script>
