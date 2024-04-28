<template>
    <form @submit.prevent="convertFile" v-if="!isConverting">
        <div class="form-group">
            <label for="file">File</label>
            <input type="file" class="form-control" :id="this.page" v-on:change="files = $event.target.files" multiple>
        </div>
        <div class="form-group">
            <label for="format">Format</label>
            <select name="format" id="format" class="form-control" v-model="selectedFormat"
                @change="conversionType = selectedFormat">
                <option v-for="format in formats" :value="format.id">{{ format.name }}</option>
            </select>
        </div>
        <div v-if="this.page === 'image'">
            <input type="checkbox" @change="resize = !resize"> Resize</input>
            <div class="form-group" v-if="resize">
                <label for="width">Width(px)</label>
                <input type="number" class="form-control" id="width" v-model="width">
            </div>
            <div class="form-group" v-if="resize">
                <label for="height">Height(px)</label>
                <input type="number" class="form-control" id="height" v-model="height">
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-primary" :disabled="files.length === 0">Convert</button>
    </form>
    <div class="my-3" v-else>
        <div style="display: flex; align-items: center; justify-content: center;">
            <div class="spinner-border mb-3" :class="spinnerClass" role="status"></div>
            <span class="sr-only">{{ conversionStatus.charAt(0).toUpperCase() + conversionStatus.slice(1) }}</span>
        </div>
        <button class="btn btn-primary" @click="resetConversion()">Convert Another</button>
    </div>
</template>

<script>
import api from '../axios'
export default {
    name: 'ConversionForm',
    props: {
        page: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            files: [],
            conversionType: '',
            formats: '',
            selectedFormat: '',
            isConverting: false,
            conversionStatus: '',
            resize: false,
        }
    },
    created() {
        if(!localStorage.getItem(`${this.page}Formats`)){
            this.getFormats();
        } else {
            this.formats = JSON.parse(localStorage.getItem(`${this.page}Formats`));
        }
        this.setLocalStorage();
    },
    methods: {
        setLocalStorage() {
            if(!localStorage.getItem('pastConversions')){
                localStorage.setItem('pastConversions', JSON.stringify({
                    'video': [],
                    'audio': [],
                    'image': [],
                    'spreadsheet': []
                }));
            }
        },
        resetConversion() {
            this.isConverting = false;
            this.conversionStatus = '';
        },
        getFormats() {
            console.log('onCreated');
            api.get(`/formats/${this.page}`).then(response => {
                console.log(response.data);
                this.formats = response.data;
                this.storeFormats(response.data);
            }).catch(error => {
                console.log(error);
            });
        },
        storeFormats(formatData) {
            if(!localStorage.getItem(`${this.page}Formats`)){
                localStorage.setItem(`${this.page}Formats`, JSON.stringify(formatData));
            }
        },
        convertFile() {
            let formData = new FormData();
            let selectedFormat = this.selectedFormat;
            let firstFileName = this.files[0].name;
            for (let i = 0; i <= this.files.length; i++) {
                formData.append(`${this.page}[]`, this.files[i]);
            }
            if (this.width && this.height){
                formData.append('width', this.width);
                formData.append('height', this.height);
            }
            formData.append('format', selectedFormat);
            api.post(`/conversions/${this.page}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.isConverting = true;
                this.conversionStatus = 'started';
                let guid = response.data.guid;
                this.subscribeToChannel(guid, firstFileName);
                console.log(response.data);
            }).catch(error => {
                console.log(error);
            });
        },
        subscribeToChannel(guid, firstFileName) {
            console.log('subscribing to channel');
            window.Echo.channel('conversion.' + guid)
            .listen('ImageConverted', (event) => {
                this.conversionStatus = event.status;
                if (event.status == 'completed'){
                    window.Echo.leave('conversion.' + guid);
                    this.addPastConversion(guid, this.page, firstFileName);
                    this.fetchConversion(guid);
                }
            });
        },
        addPastConversion(sessionId, type, firstFileName) {
            let pastConversions = JSON.parse(localStorage.getItem('pastConversions'));
            if(pastConversions[type]) {
                pastConversions[type].push({ guid: sessionId, filename: firstFileName });
                localStorage.setItem('pastConversions', JSON.stringify(pastConversions));
            }
        },
        fetchConversion(sessionId) {
            api.get(`/${this.page}/` + sessionId + '.zip', { responseType: 'blob' })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'file.zip');
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    console.log(error);
                });
        }
    },
    computed: {
        spinnerClass() {
            return {
                'text-secondary': this.conversionStatus === 'started',
                'text-primary': this.conversionStatus === 'processing',
                'text-success': this.conversionStatus === 'completed',
                'text-danger': this.conversionStatus === 'failed'
            }
        }
    }
}
</script>