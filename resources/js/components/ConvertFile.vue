<template>
    <form @submit.prevent="convertFile" v-if="!isConverting">
        <div class="form-group">
            <label for="file">File</label>
            <input type="file" class="form-control" id="file" v-on:change="files = $event.target.files" multiple>
        </div>
        <div class="form-group">
            <label for="format">Format</label>
            <select name="format" id="format" class="form-control" v-model="selectedFormat"
                @change="conversionType = selectedFormat">
                <option v-for="format in formats" :value="format.id">{{ format.name }}</option>
            </select>
        </div>
        <div>
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
import api from '../axios';
export default {
    name: 'ConvertFile',
    data() {
        return {
            files: [],
            conversionType: '',
            formats: '',
            selectedFormat: '',
            width: null,
            height: null,
            resize: false,
            isConverting: false,
            conversionStatus: '',
        };
    },
    created() {
        this.getFormats();
    },
    methods: {
        resetConversion() {
            this.isConverting = false;
            this.conversionStatus = '';
        },
        getFormats() {
            console.log('onCreated');
            api.get('/formats/image').then(response => {
                console.log(response.data);
                this.formats = response.data;
            }).catch(error => {
                console.log(error);
            });
        },
        convertFile() {
            let formData = new FormData();
            let selectedFormat = this.selectedFormat;
            for (let i = 0; i <= this.files.length; i++) {
                formData.append('images[]', this.files[i]);
            }
            if (this.width && this.height){
                formData.append('width', this.width);
                formData.append('height', this.height);
            }
            formData.append('format', selectedFormat);
            api.post('/conversions/image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.isConverting = true;
                this.conversionStatus = 'started';
                let guid = response.data.guid;
                this.setLocalStorage(guid);
                this.subscribeToChannel();
            }).catch(error => {
                console.log(error);
            });
        },
        setLocalStorage(guid){
            localStorage.setItem('guid', guid);
            let convertedGuids = JSON.parse(localStorage.getItem('convertedGuids')) || [];
            convertedGuids.push(guid);
            localStorage.setItem('convertedGuids', JSON.stringify(convertedGuids));
        },
        subscribeToChannel() {
            console.log('subscribing to channel');
            let sessionId = localStorage.getItem('guid');
            window.Echo.channel('conversion.' + sessionId)
            .listen('ImageConverted', (event) => {
                this.conversionStatus = event.status;
                if (event.status == 'completed'){
                    window.Echo.leave('conversion.' + sessionId);
                    this.fetchConversion(sessionId);
                }
            });
        },
        fetchConversion(sessionId) {
            api.get('/images/' + sessionId + '.zip', { responseType: 'blob' })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'file.zip'); //or any other extension
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
