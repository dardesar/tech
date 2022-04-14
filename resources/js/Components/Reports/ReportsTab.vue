<script>
import Template from '{Template}/Web/Components/Reports/ReportsTab.template'
export default Template({
    props: ['section'],
    methods: {
        setReport(report) {
            this.$inertia.visit(route(report));
        }
    }
})
</script>
