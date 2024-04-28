<template>
    <h2>{{ capitalizedName }} Conversions</h2>
    <div v-for="conversions in pastConversions">
        <a @click="downloadConversion(conversions.guid)" class="text-left">{{ conversions.filename }}</a>
    </div>
</template>

<script>
import { capitalize } from "vue";
import api from "../axios.js";

export default {
    name: "PastConversionList",
    props: {
        pastConversions: {
            type: Object,
            required: true
        },
        name: {
            type: String,
            required: true
        }
    },
    methods: {
        downloadConversion(conversion) {
            api.get(`/${this.name}/${conversion}.zip`, {responseType: 'blob'})
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
        capitalizedName() {
            return capitalize(this.name);
        }
    },
}
</script>