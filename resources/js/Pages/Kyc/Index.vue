<script>
import Template from '{Template}/Web/Pages/Kyc/Index.template'
import AppLayout from '@/Layouts/AppLayout'
import TextUserInput from "@/Jetstream/TextUserInput";
import TButton from "@/Jetstream/Button";
import LoadingButton from "@/Jetstream/LoadingButton";

const defaultForm = {
    first_name: "",
    last_name: "",
    middle_name: "",
    country_id: null,
    document_type: 'id',
    documents: [],
    selfie_id: null,
};

export default Template({
    components: {
        TButton,
        LoadingButton,
        TextUserInput,
        AppLayout,
    },
    data() {
        return {
            uploaded: false,
            uploadedSelfie: false,
            documentPhoto: null,
            selfiePhoto: null,
            sending: false,
            sendingSelfie: false,
            form: Object.assign({}, defaultForm),
            uploadUrl: route('user-file-upload'),
            uploadHeaders: {
                'X-XSRF-TOKEN' : $cookies.get('XSRF-TOKEN')
            }
        }
    },
    computed: {
        isMultiple: function() {
            return this.form.document_type == "id" || this.form.document_type == "residence_permit";
        },
        actionButtonTitle: function () {
            return 'Submit';
        },
    },
    props: {
        isVerified: Boolean,
        errors: Object,
        countries: Object,
        pendingDocument: Object,
        rejectedDocument: Object,
    },
    methods: {
        submit() {

            if(this.sending) return;

            this.sending = true;

            let afterRequest = {
                onStart: () => this.sending = true,
                onFinish: () => this.sending = false,
                onSuccess: () => {
                    this.sending = false;
                    this.$toast.open('KYC Documents were submitted');
                },
                onError: () => {
                    this.sending = false;
                    this.$toast.error('There are some form errors');
                },
                preserveScroll: true,
            };

            this.$inertia.post(this.route('user.kyc.store'), this.form, afterRequest);
        },
        removeSelfiePic: function(){
            this.selfiePhoto = null;
            this.form.selfie_id = null;
            this.uploadedSelfie = false;
        },
        removePic: function(){
            this.documentPhoto = null;
            this.form.documents = [];
            this.uploaded = false;
        },
        upload: function(){
            let self = this;
            this.$refs.fileUploadForm.upload(this.uploadUrl, this.uploadHeaders, [this.documentPhoto]).then(function(){
                self.uploaded = true;
                setTimeout(function(){
                    // self.currencyLogo.progress(0);
                }, 500);
            });
        },
        uploadSelfie: function(){
            let self = this;
            this.$refs.selfieUploadForm.upload(this.uploadUrl, this.uploadHeaders, [this.selfiePhoto]).then(function(){
                self.uploadedSelfie = true;
            });
        },
        onSelect: function(fileRecords){
            this.upload();
            this.uploaded = false;
        },
        onUpload: function(responses){
            let response = responses[0];
            if (response && !response.error) {
                this.form.documents.push(response.data.uuid);
            }
        },
        onSelectSelfie: function(fileRecords){
            this.uploadSelfie();
            this.uploadedSelfie = false;
        },
        onUploadSelfie: function(responses){
            let response = responses[0];
            if (response && !response.error) {
                this.form.selfie_id = response.data.uuid;
            }
        }
    },
    watch: {
        'form.document': function (type, newType) {
            this.documentPhoto = null;
            this.form.documents = [];
        },
    }
})
</script>
