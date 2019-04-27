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
                    // @todo
                    products: [
                        {
                            id:1,
                            name: 'Продукт №1',
                            quantity: 2,
                            price: 100
                        }
                    ],
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
            }
        },
        computed: {
            sum: function () {
                var self = this;
                var r = 0;
                _.forEach(self.formData.products, function (product) {
                    r += product.quantity * product.price;
                });
                return r;
            }
        }
    }
</script>
