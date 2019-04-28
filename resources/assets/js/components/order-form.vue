<script>
    import Selectize from 'vue2-selectize'
    export default {
        components: {
            Selectize
        },
        mounted() {
            console.log('Order-form mounted.');
        },
        created: function(){
            var self = this;
            self.initFormData();
            self.initInternalData();
        },
        props: ['input'],
        data: function () {
            return {
                settings: {},
                formData: {
                    partner_id: '',
                    products: {
                        current: [],
                        for_add: []
                    },
                    client_email: '',
                    status: '',
                },
                internal: {
                    product_src: [],
                    product_selected: '',
                    selectize: {
                        settings: {}
                    },
                    isActiveForm: true
                },
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
            initInternalData: function(){
                var self = this;
                if (self.input){
                    _.forEach(self.internal, function (val, key) {
                        if (key in self.input){
                            self.internal[key] = self.input[key];
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
            },

            addProduct: function (){
                var self = this;
                if (self.internal.product_selected){
                    self.$nextTick(function () {
                        var productToAdd = _.find(self.internal.product_src, function (o) {
                            return o.product_id == self.internal.product_selected;
                        });
                        var isExistInContainer = _.find(self.formData.products.for_add, {'product_id': productToAdd.product_id});
                        if ( productToAdd && !isExistInContainer ){
                            self.formData.products.for_add.push(productToAdd);
                        }
                    });
                }
            },

            submitForm: function () {
                var self = this;
                var action = self.$refs['form'].action;
                self.$validator.validateAll().then((result) => {
                    if (result) {
                        self.internal.isActiveForm = false;
                        apiForm.formSubmit(action, self.formData).then(function (response) {
                            if (response.status == 200 && 'redirect' in response.data){
                                window.location.href = response.data.redirect;
                            } else {
                                self.internal.isActiveForm = true;
                            }
                        }).catch(function (e) {
                            console.error('form save error:', e);
                            self.internal.isActiveForm = true;
                        })
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

    var apiForm = {
        formSubmit: function (action, formData) {
            return axios.post(action, formData);
        }
    };
</script>
