<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card doc-form">
                    {{ error }}
                    <label v-for="elem in formArray"  > {{ elem.columnName }}
                        <select v-model="elem.choice"  @change="loadNext(elem)">
                            <option v-for="(availables, index) in elem.options" :value="availables"> {{ index }}</option>
                        </select>
                    </label>
                    <div v-if="loader">
                        <button @click="downloadPdf">Download PDF file</button>
                    </div>
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
            error: null,
            currentDpoFilter: null,
            loader: false,
            dpoFilters: null,
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
                    this.currentDpoFilter = 0;
                })
                .catch((error) => {
                    this.error = error.response.data[0];
                });

            let options = await this.getOptions(this.dpoFilters[this.currentDpoFilter], null);

            this.formArray.push({
                columnName: this.dpoFilters[this.currentDpoFilter],
                options: options.data[0]
            })

            this.currentDpoFilter++;

        },

        async loadNext(elem) {
            const choice = {
                choice: elem.choice
            }
            const indexOfClickedFilter = this.dpoFilters.indexOf(elem.columnName)
            const amountOfElementsToSplice = this.formArray.length-indexOfClickedFilter;
            let options;

            this.currentDpoFilter = indexOfClickedFilter+1;
            this.formArray.splice(indexOfClickedFilter+1, amountOfElementsToSplice);


            do {
                if(this.currentDpoFilter === this.dpoFilters.length) {
                    this.loader=true;
                    return;
                }
                options = await this.getOptions(this.dpoFilters[this.currentDpoFilter], choice);
                options = options.data[0];
                this.currentDpoFilter++;
            } while (!Object.keys(options).some(x => (x !== null && x !== '')));

            this.formArray.push({
                columnName: this.dpoFilters[this.currentDpoFilter-1],
                options: options
            })
        },

        getOptions(colName, previousArray) {
            try {
                return axios.post('/api/get-xls-data', {
                    column_name: colName,
                    previous_option_array: previousArray
                });
            }  catch (err) {
                this.error = 'An error occurred.';
            }
        },

        downloadPdf() {
            window.open('/api/download')
        }
    },
}
</script>
