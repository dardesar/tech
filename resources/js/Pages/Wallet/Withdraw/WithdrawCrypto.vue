<script>
import Template from '{Template}/Web/Pages/Wallet/Withdraw/WithdrawCrypto.template'
import AppLayout from '@/Layouts/AppLayout'
import TextUserInput from "@/Jetstream/TextUserInput";
import SelectInput from "@/Jetstream/SelectInput";
import {mapGetters} from "vuex";
import {string_cut} from "@/Functions/String";
import JetDialogModal from '@/Jetstream/DialogModal'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import LoadingButton from "@/Jetstream/LoadingButton";
import SvgIcon from "@/Components/Svg/SvgIcon";

const defaultForm = {
    network: null,
    address: null,
    amount: 0,
    payment_id: null,
};

export default Template({
    components: {
        AppLayout,
        TextUserInput,
        SelectInput,
        JetDialogModal,
        JetSecondaryButton,
        LoadingButton,
        SvgIcon
    },
    props: {
        symbol: String,
        currency: Object,
        errors: Object,
    },
    mounted() {

        if(_.isEmpty(this.wallets)) {
            this.$store.dispatch('fetchWallets');
        }

        if(_.isEmpty(this.withdrawals)) {
            this.$store.dispatch('fetchWithdrawals');
        }
    },
    data() {
        return {
            form: Object.assign({}, defaultForm),
            showWithdrawalModal: false,
            closeWithdrawalModal: false,
            withdrawalModal: null,
            sending: false
        }
    },
    computed: {
        ...mapGetters({
            wallets: 'getWallets',
            withdrawals: 'getWithdrawals',
        }),
        wallet: function () {
            if(this.$store.getters.getUser) {
                return this.$store.getters.getWallet(this.currency.symbol);
            }
        },
    },
    methods: {
        format_string(string, limit) {
            return string_cut(string, limit);
        },
        doCopy(string) {
            this.$copyText(string).then(() => {
                this.$toast.open('Text was copied to the clipboard');
            }, function (e) {

            })
        },
        withdraw() {

            if(this.sending) return;

            this.sending = true;

            this.form.symbol = this.currency.symbol;
            this.form.network = this.currency.networks[0].id;

            axios.post(route('wallets.api.withdraw'), this.form).then((response) => {
                this.$inertia.visit(route('wallets.withdraw.crypto.success'));
            }).catch(error => {
                this.sending = false;
                _.each(error.response.data.errors, (field, key) => {
                    this.$toast.error(field[0]);
                });
            });
        },
        openModal(withdrawal) {
            this.withdrawalModal = withdrawal;
            this.showWithdrawalModal = true;
        },
        closeModal() {
            this.showWithdrawalModal = false;
        }
    },
})
</script>
