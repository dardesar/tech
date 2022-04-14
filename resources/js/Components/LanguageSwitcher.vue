<script>
import Template from '{Template}/Web/Components/LanguageSwitcher.template'
export default Template({
    methods: {
        switchLanguage(slug) {
            axios.get(route('language.set'), {
                params: {
                    language: slug
                }
            }).then((response) => {
                //window.location.reload();
            }).catch(error => {

            });
        },
    }
})
</script>
