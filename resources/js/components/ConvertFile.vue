<template>
    <form @submit.prevent="convertFile">
        <div class="form-group">
            <label for="file">File</label>
            <input type="file" class="form-control" id="file" v-on:change="files = $event.target.files" multiple>
        </div>
        <div class="form-group">
            <label for="format">Format</label>
            <select name="format" id="format" class="form-control" v-model="selectedFormat" @change="conversionType = selectedFormat">
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
        };
    },
    created() {
        this.getFormats();
    },
    methods: {
        getFormats() {
            console.log('onCreated');
            api.get('/formats').then(response => {
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
            api.post('/convert', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                console.log(response.data);
                localStorage.setItem('guid', response.data.guid);
                console.log(localStorage.getItem('guid'));
                this.subscribeToChannel();
            }).catch(error => {
                console.log(error);
            });
        },
        subscribeToChannel() {
            console.log('subscribing to channel');
            let sessionId = localStorage.getItem('guid');
            // window.Echo.private('conversion.' + sessionId)
            window.Echo.channel('conversion.' + sessionId)
            .listen('ImageConverted', (event) => {
                // Handle event, e.g., update UI
                console.log(event);
            });
        }
    }
}
</script>
