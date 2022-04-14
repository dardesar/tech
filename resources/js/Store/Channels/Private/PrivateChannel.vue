<template>
    <div></div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
    name: 'private-channel',
    watch: {
        user: function (socket) {
            this.join();
        },
    },
    computed: mapGetters({
        user: 'getUser',
        channel: 'getUserChannel'
    }),
    methods: {
        join: function () {

            if(!this.user) return false;

            // If already joined ignore
            if(this.channel in window.Echo.connector.channels) return false;

            // Join channel and listen events
            Echo.channel(this.channel)
                .listen('WalletUpdated', (payload) => {

                    // Listen wallet updated event

                    this.$store.dispatch('updateWallet', {
                        wallet: payload.wallet
                    });

                })
                .listen('DepositUpdated', (payload) => {

                        // Listen deposit received event
                        if(payload.type == "received") {
                            this.$store.dispatch('storeDeposit', {
                                deposit: payload.deposit
                            });
                            this.$toast.open('Deposit Received');
                        }

                        if(payload.type == "updated") {
                            this.$store.dispatch('updateDeposit', {
                                deposit: payload.deposit
                            });
                            this.$toast.open('Deposit Updated');
                        }
                    })
                .listen('WithdrawalUpdated', (payload) => {
                    this.$store.dispatch('updateWithdrawal', {
                        withdrawal: payload.withdrawal
                    });
                    this.$toast.open('Withdrawal Updated');
                });
        },
    }
}
</script>
