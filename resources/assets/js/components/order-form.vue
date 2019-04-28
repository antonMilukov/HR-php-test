<script>
    export default {
        mounted() {
            console.log('Order-form mounted.');
        },
        created: function(){
            var self = this;
            console.log('created input is: ', this.input);
            self.initFormData();
        },
        props: ['input'],
        data: function () {
            return {
                formData: {
                    partner_id: '',
                    products: {
                        current: [],
                        for_add: []
                    },
                    client_email: '',
                    status: '',
                }
            }
        },
        methods: {
            initFormData: function () {
                var self = this;
                if (self.input){
                    _.forEach(self.formData, function (val, key) {
                        if (key in self.input){
                            self.formData[key] = self.input[key];
                        }
                    });
                }
            },
            removeProduct: function (src, alias, val) {
                var self = this;
                _.forEach(src, function (product, key) {
                    if (product[alias] == val){
                        console.log('Y', product);
                        self.$nextTick(function () {
                            src.splice(key, 1);
                        });
                    }
                });
            }
        },
        computed: {
            sum: function () {
                var self = this;
                var r = 0;
                _.forEach(self.formData.products, function (container) {
                    _.forEach(container, function (product) {
                        r += product.quantity * product.price;
                    });
                });
                return r;
            }
        }
    }
</script>
