<script>
    import ApiTokenManager from './ApiTokenManager'
    import AppLayout from '@/Layouts/AppLayout'
    import Template from '{Template}/Web/Pages/API/Index.template'

    export default Template({
        props: [
            'tokens',
            'availablePermissions',
            'defaultPermissions',
        ],

        components: {
            ApiTokenManager,
            AppLayout,
        },
        methods: {
            setPage(page) {
                this.$inertia.visit(route('profile.show', page));
            },
        }
    });
</script>
