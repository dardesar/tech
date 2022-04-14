<script>
    import AppLayout from '@/Layouts/AppLayout'
    import DeleteUserForm from './DeleteUserForm'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import LogoutOtherBrowserSessionsForm from './LogoutOtherBrowserSessionsForm'
    import TwoFactorAuthenticationForm from './TwoFactorAuthenticationForm'
    import UpdatePasswordForm from './UpdatePasswordForm'
    import UpdateProfileInformationForm from './UpdateProfileInformationForm'
    import ProfileInformation from "./ProfileInformation";
    import Template from '{Template}/Web/Pages/Profile/Show.template'

    export default Template({
        props: ['sessions', 'slug'],
        data() {
            return {
                page : 'info',
            }
        },
        components: {
            AppLayout,
            DeleteUserForm,
            JetSectionBorder,
            LogoutOtherBrowserSessionsForm,
            TwoFactorAuthenticationForm,
            UpdatePasswordForm,
            UpdateProfileInformationForm,
            ProfileInformation
        },
        mounted() {

            let slug = this.slug;

            if(!this.slug) {
                slug = this.page;
            }

            this.page = slug;
        },
        methods: {
            setPage(page) {
                this.$inertia.visit(route('profile.show', page));
            },
        }
    });
</script>
