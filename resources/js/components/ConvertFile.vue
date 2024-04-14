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
            selectedFormat: ''
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
            console.log('files', this.files);
            console.log(this.conversionType);
            let formData = new FormData();
            // for (let i = 0; i <= this.files.length; i++) {
            //     formData.append('files[]', this.files[i]);
            // }
            // api.post('/convert-file', formData, {
            //     headers: {
            //         'Content-Type': 'multipart/form-data'
            //     }
            // }).then(response => {
            //     console.log(response.data);
            // }).catch(error => {
            //     console.log(error);
            // });
        }
    }
}
</script>
