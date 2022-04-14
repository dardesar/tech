<script>
import Template from '{Template}/Web/Pages/Wallet/Withdraw/WithdrawFiat.template'
import SelectInput from "@/Jetstream/SelectInput";
import AppLayout from '@/Layouts/AppLayout'
import {mapGetters} from "vuex";
import {string_cut} from "@/Functions/String";
import JetButton from '@/Jetstream/Button'
import TextUserInput from "@/Jetstream/TextUserInput";
import LoadingButton from "@/Jetstream/LoadingButton";
import JetDialogModal from '@/Jetstream/DialogModal'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import SvgIcon from "@/Components/Svg/SvgIcon";

const defaultForm = {
    currency: null,
    amount: 0,
    name: '',
    iban: '',
    swift: '',
    ifsc: '',
    address: '',
    account_holder_name: '',
    account_holder_address: '',
    country_id: null,
};

export default Template({
    components: {
        AppLayout,
        SelectInput,
        JetButton,
        TextUserInput,
        LoadingButton,
        JetDialogModal,
        JetSecondaryButton,
        SvgIcon
    },
    props: {
        symbol: String,
        currency: Object,
        errors: Object,
        countries: Object,
    },
    data() {
        return {
            form: Object.assign({}, defaultForm),
            sending: false,
            showWithdrawalModal: false,
            closeWithdrawalModal: false,
            withdrawalModal: null,
        }
    },
    mounted() {

        if(_.isEmpty(this.wallets)) {
            this.$store.dispatch('fetchWallets');
        }

        if(_.isEmpty(this.withdrawals)) {
            this.$store.dispatch('fetchFiatWithdrawals');
        }
    },
    computed: {
        ...mapGetters({
            wallets: 'getWallets',
            withdrawals: 'getFiatWithdrawals',
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
        submit() {

            if(this.sending) return;

            let afterRequest = {
                onStart: () => this.sending = true,
                onFinish: () => this.sending = false,
                onSuccess: () => {
                    this.$toast.open('Withdraw was submitted');
                    this.$inertia.visit(route('wallets.withdraw.fiat.success'));
                },
                onError: () => {
                    this.sending = false;
                    this.$toast.error('There are some form errors');
                },
            };

            this.form.currency_id = this.currency.id;

            this.$inertia.post(this.route('wallets.withdraw.store.fiat'), this.form, afterRequest);
        },
        openModal(withdrawal) {
            this.withdrawalModal = withdrawal;
            this.showWithdrawalModal = true;
        },
        closeModal() {
            this.showWithdrawalModal = false;
        }
    }
});
</script>
