<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <label v-for="elem in formArray"  > {{ elem.columnName }}
                        <select v-model="elem.choice"  @change="loadNext(elem)">
                            <option v-for="(availables, index) in elem.options" :value="availables"> {{ index }}</option>
                        </select>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    name: "form-component",
    data() {
        return {
            currentDpoFilter: null,
            loader: false,
            dpoFilters: null,
            lastColumnName: null,
            lastArray: null,
            optionList: null,
            choice: null,
            formArray: []
        }
    },
    mounted: function() {
        this.getFilters();
    },
    methods: {
        async getFilters() {
            await axios
                .get('/api/get-filters')
                .then((response) => {
                    this.dpoFilters = response.data[0];
                    this.lastColumnName = response.data[0][0];
                    this.currentDpoFilter = 0;
                })
                .catch(error => console.error(error));

            let options = await this.getOptions(this.dpoFilters[this.currentDpoFilter], null);

            this.formArray.push({
                columnName: this.dpoFilters[this.currentDpoFilter],
                options: options.data[0]
            })

            this.currentDpoFilter++;

        },

        getOptions(colName, previousArray) {
            try {
                return axios.post('/api/get-xls-data', {
                        column_name: colName,
                        previous_option_array: previousArray
                });
            }  catch (err) {
                console.error(err);
            }
        },

        async loadNext(elem) {
            this.currentDpoFilter = this.dpoFilters.indexOf(elem.columnName)+1;
            this.formArray.splice(this.dpoFilters.indexOf(elem.columnName)+1, this.formArray.length-this.dpoFilters.indexOf(elem.columnName));

            if(this.currentDpoFilter === this.dpoFilters.length) {
                return;
            }
            const choice = {
                choice: elem.choice
            }
            let options = await this.getOptions(this.dpoFilters[this.currentDpoFilter], choice);
            options = options.data[0];

                this.formArray.push({
                    columnName: this.dpoFilters[this.currentDpoFilter],
                    options: options
                })

            this.currentDpoFilter++;
        }
    },

}
</script>
